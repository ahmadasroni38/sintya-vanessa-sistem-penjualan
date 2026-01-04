<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Services\StockBalanceService;

class StockIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_in';

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'location_id',
        'total_price',
        'supplier_name',
        'reference_number',
        'notes',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_price' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    protected $appends = ['total_quantity', 'total_items'];

    /**
     * Relationship to stock in details
     */
    public function details()
    {
        return $this->hasMany(StockInDetail::class);
    }

    // Relasi ke location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Relasi ke user yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang posting
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Scope untuk filter by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter by date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Accessor untuk total quantity
     */
    public function getTotalQuantityAttribute()
    {
        return $this->details->sum('quantity');
    }

    /**
     * Accessor untuk total items
     */
    public function getTotalItemsAttribute()
    {
        return $this->details->count();
    }

    // Method untuk posting stock in
    public function post($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft transactions can be posted');
        }

        if ($this->details()->count() === 0) {
            throw new \Exception('Cannot post stock in without details');
        }

        DB::transaction(function () use ($userId) {
            // Update status
            $this->update([
                'status' => 'posted',
                'posted_by' => $userId,
                'posted_at' => now(),
            ]);

            // Create stock card entries for each detail
            $this->createStockCards();

            // Update stock balances for all affected products
            $this->updateStockBalances();
        });

        return $this;
    }

    // Method untuk membuat stock card untuk semua details
    protected function createStockCards()
    {
        foreach ($this->details as $detail) {
            // Get previous balance
            $previousCard = StockCard::where('product_id', $detail->product_id)
                ->where('location_id', $this->location_id)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $previousBalance = $previousCard ? $previousCard->balance : 0;

            // Create new stock card
            StockCard::create([
                'product_id' => $detail->product_id,
                'location_id' => $this->location_id,
                'transaction_date' => $this->transaction_date,
                'transaction_type' => 'stock_in',
                'reference_id' => $this->id,
                'reference_number' => $this->transaction_number,
                'quantity_in' => $detail->quantity,
                'quantity_out' => 0,
                'balance' => $previousBalance + $detail->quantity,
                'unit_price' => $detail->unit_price,
                'notes' => $detail->notes ?? $this->notes,
            ]);
        }
    }

    /**
     * Calculate total price from details
     */
    public function calculateTotal()
    {
        $this->total_price = $this->details()->sum('total_price');
        $this->save();
    }

    /**
     * Update stock balances for all products in this stock in
     */
    protected function updateStockBalances()
    {
        $productLocations = [];

        foreach ($this->details as $detail) {
            $productLocations[] = [
                'product_id' => $detail->product_id,
                'location_id' => $this->location_id,
            ];
        }

        // Remove duplicates and update balances
        $uniqueProductLocations = array_unique($productLocations, SORT_REGULAR);

        StockBalanceService::updateBalancesFromTransaction(
            $uniqueProductLocations,
            'stock_in',
            $this->transaction_date->format('Y-m-d')
        );
    }

    // Auto-generate transaction number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockIn) {
            if (empty($stockIn->transaction_number)) {
                $lastTransaction = static::whereYear('created_at', date('Y'))
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -5)) + 1 : 1;
                $stockIn->transaction_number = 'SI-' . date('Y') . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
