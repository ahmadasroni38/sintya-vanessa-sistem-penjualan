<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMutation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'from_location_id',
        'to_location_id',
        'reference_number',
        'notes',
        'status',
        'created_by',
        'submitted_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'completed_by',
        'completed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $appends = ['total_quantity', 'total_items'];

    /**
     * Relationship to stock mutation details
     */
    public function details()
    {
        return $this->hasMany(StockMutationDetail::class);
    }

    // Relasi ke from location
    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    // Relasi ke to location
    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    // Relasi ke user yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang submit
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // Relasi ke user yang approve
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi ke user yang complete
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
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

    // Method untuk submit mutation (draft -> pending)
    public function submit($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft mutations can be submitted');
        }

        if ($this->details()->count() === 0) {
            throw new \Exception('Cannot submit mutation without details');
        }

        // Validate stock availability
        $this->validateStockAvailability();

        $this->update([
            'status' => 'pending',
            'submitted_by' => $userId,
            'submitted_at' => now(),
        ]);

        return $this;
    }

    // Method untuk approve mutation (pending -> approved)
    public function approve($userId)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending mutations can be approved');
        }

        // Revalidate stock availability
        $this->validateStockAvailability();

        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return $this;
    }

    // Method untuk complete mutation (approved -> completed)
    public function complete($userId)
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Only approved mutations can be completed');
        }

        \DB::transaction(function () use ($userId) {
            // Update status
            $this->update([
                'status' => 'completed',
                'completed_by' => $userId,
                'completed_at' => now(),
            ]);

            // Create stock cards for all details
            $this->createStockCards();
        });

        return $this;
    }

    // Method untuk cancel mutation
    public function cancel($reason = null)
    {
        if (!in_array($this->status, ['draft', 'pending', 'approved'])) {
            throw new \Exception('Cannot cancel completed or cancelled mutations');
        }

        $this->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
        ]);

        return $this;
    }

    // Validate stock availability for all details
    protected function validateStockAvailability()
    {
        foreach ($this->details as $detail) {
            $availableStock = $this->getAvailableStock($detail->product_id);

            if ($availableStock < $detail->quantity) {
                $product = Product::find($detail->product_id);
                $productName = $product ? ($product->product_name ?? $product->name ?? 'Unknown Product') : 'Unknown Product';
                throw new \Exception("Insufficient stock for {$productName}. Available: {$availableStock}, Required: {$detail->quantity}");
            }
        }
    }

    // Get available stock for a product at from_location
    protected function getAvailableStock($productId)
    {
        $lastCard = StockCard::where('product_id', $productId)
            ->where('location_id', $this->from_location_id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastCard ? $lastCard->balance : 0;
    }

    // Method untuk membuat stock cards untuk semua details
    protected function createStockCards()
    {
        foreach ($this->details as $detail) {
            // Create stock OUT for source location
            $this->createStockCardOut($detail);

            // Create stock IN for destination location
            $this->createStockCardIn($detail);
        }
    }

    protected function createStockCardOut($detail)
    {
        $previousCard = StockCard::where('product_id', $detail->product_id)
            ->where('location_id', $this->from_location_id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousCard ? $previousCard->balance : 0;

        StockCard::create([
            'product_id' => $detail->product_id,
            'location_id' => $this->from_location_id,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => 'mutation_out',
            'reference_id' => $this->id,
            'reference_number' => $this->transaction_number,
            'quantity_in' => 0,
            'quantity_out' => $detail->quantity,
            'balance' => $previousBalance - $detail->quantity,
            'unit_price' => 0,
            'notes' => $detail->notes ?? $this->notes,
        ]);
    }

    protected function createStockCardIn($detail)
    {
        $previousCard = StockCard::where('product_id', $detail->product_id)
            ->where('location_id', $this->to_location_id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousCard ? $previousCard->balance : 0;

        StockCard::create([
            'product_id' => $detail->product_id,
            'location_id' => $this->to_location_id,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => 'mutation_in',
            'reference_id' => $this->id,
            'reference_number' => $this->transaction_number,
            'quantity_in' => $detail->quantity,
            'quantity_out' => 0,
            'balance' => $previousBalance + $detail->quantity,
            'unit_price' => 0,
            'notes' => $detail->notes ?? $this->notes,
        ]);
    }

    // Auto-generate transaction number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mutation) {
            if (empty($mutation->transaction_number)) {
                $lastTransaction = static::whereYear('created_at', date('Y'))
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastTransaction ? intval(substr($lastTransaction->transaction_number, -5)) + 1 : 1;
                $mutation->transaction_number = 'SM-' . date('Y') . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
