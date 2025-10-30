<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_number', 'adjustment_date', 'product_id', 'location_id',
        'system_quantity', 'actual_quantity', 'difference_quantity',
        'adjustment_type', 'reason', 'notes', 'status',
        'created_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'system_quantity' => 'integer',
        'actual_quantity' => 'integer',
        'difference_quantity' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }

    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeByType($query, $type) { return $query->where('adjustment_type', $type); }

    public function post($userId)
    {
        if ($this->status !== 'draft') throw new \Exception('Only draft adjustments can be posted');

        \DB::transaction(function () use ($userId) {
            $this->update(['status' => 'posted', 'approved_by' => $userId, 'approved_at' => now()]);
            $this->createStockCard();
        });
        return $this;
    }

    protected function createStockCard()
    {
        $previousCard = StockCard::where('product_id', $this->product_id)
            ->where('location_id', $this->location_id)
            ->orderBy('transaction_date', 'desc')->orderBy('id', 'desc')->first();

        $previousBalance = $previousCard ? $previousCard->balance : 0;
        $quantityIn = $this->adjustment_type === 'increase' ? abs($this->difference_quantity) : 0;
        $quantityOut = $this->adjustment_type === 'decrease' ? abs($this->difference_quantity) : 0;

        StockCard::create([
            'product_id' => $this->product_id, 'location_id' => $this->location_id,
            'transaction_date' => $this->adjustment_date, 'transaction_type' => 'adjustment',
            'reference_id' => $this->id, 'reference_number' => $this->adjustment_number,
            'quantity_in' => $quantityIn, 'quantity_out' => $quantityOut,
            'balance' => $previousBalance + $quantityIn - $quantityOut,
            'unit_price' => 0, 'notes' => $this->reason ?? $this->notes,
        ]);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($adj) {
            if (empty($adj->adjustment_number)) {
                $last = static::whereYear('created_at', date('Y'))->orderBy('id', 'desc')->first();
                $next = $last ? intval(substr($last->adjustment_number, -5)) + 1 : 1;
                $adj->adjustment_number = 'ADJ-' . date('Y') . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
