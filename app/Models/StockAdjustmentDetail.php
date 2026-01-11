<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustmentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'system_quantity',
        'actual_quantity',
        'difference_quantity',
        'adjustment_type',
        'reason',
        'notes',
    ];

    protected $casts = [
        'system_quantity' => 'integer',
        'actual_quantity' => 'integer',
        'difference_quantity' => 'integer',
    ];

    /**
     * Get the stock adjustment that owns the detail.
     */
    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    /**
     * Get the product associated with the detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Boot method to auto-calculate difference and type.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            // Auto-calculate difference
            $detail->difference_quantity = $detail->actual_quantity - $detail->system_quantity;

            // Auto-determine adjustment type
            if ($detail->difference_quantity > 0) {
                $detail->adjustment_type = 'increase';
            } elseif ($detail->difference_quantity < 0) {
                $detail->adjustment_type = 'decrease';
            } else {
                // If no difference, default to increase
                $detail->adjustment_type = 'increase';
            }
        });
    }
}
