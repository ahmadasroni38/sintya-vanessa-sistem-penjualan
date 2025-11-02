<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'location_id',
        'total_items',
        'description',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'total_items' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the details for this adjustment.
     */
    public function details()
    {
        return $this->hasMany(StockAdjustmentDetail::class)->with('product');
    }

    /**
     * Get the location for this adjustment.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who created this adjustment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this adjustment.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeByType($query, $type) { return $query->where('adjustment_type', $type); }

    /**
     * Post/approve adjustment and create stock cards for all details.
     */
    public function post($userId)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft adjustments can be posted');
        }

        if ($this->details()->count() === 0) {
            throw new \Exception('Cannot post adjustment without any items');
        }

        \DB::transaction(function () use ($userId) {
            // Update master status
            $this->update([
                'status' => 'posted',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            // Create stock cards for each detail
            foreach ($this->details as $detail) {
                $this->createStockCardForDetail($detail);
            }
        });

        return $this;
    }

    /**
     * Create stock card entry for a single detail item.
     */
    protected function createStockCardForDetail($detail)
    {
        // Get previous balance for this product at this location
        $previousCard = StockCard::where('product_id', $detail->product_id)
            ->where('location_id', $this->location_id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousCard ? $previousCard->balance : 0;

        // Calculate quantity in/out based on adjustment type
        $quantityIn = $detail->adjustment_type === 'increase' ? abs($detail->difference_quantity) : 0;
        $quantityOut = $detail->adjustment_type === 'decrease' ? abs($detail->difference_quantity) : 0;

        // Create stock card entry
        StockCard::create([
            'product_id' => $detail->product_id,
            'location_id' => $this->location_id,
            'transaction_date' => $this->adjustment_date,
            'transaction_type' => 'adjustment',
            'reference_id' => $this->id,
            'reference_number' => $this->adjustment_number,
            'quantity_in' => $quantityIn,
            'quantity_out' => $quantityOut,
            'balance' => $previousBalance + $quantityIn - $quantityOut,
            'unit_price' => 0,
            'notes' => $detail->reason ?? $this->notes,
        ]);
    }

    /**
     * Cancel posted adjustment and remove stock cards.
     */
    public function cancel()
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted adjustments can be cancelled');
        }

        \DB::transaction(function () {
            // Delete all related stock cards
            StockCard::where('reference_id', $this->id)
                ->where('transaction_type', 'adjustment')
                ->delete();

            // Update status
            $this->update(['status' => 'cancelled']);
        });

        return $this;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($adj) {
            if (empty($adj->adjustment_number)) {
                $last = static::withTrashed()->whereYear('created_at', date('Y'))->orderBy('id', 'desc')->first();
                $next = $last ? intval(substr($last->adjustment_number, -5)) + 1 : 1;
                $adj->adjustment_number = 'ADJ-' . date('Y') . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
