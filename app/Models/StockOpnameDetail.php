<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'system_quantity',
        'physical_quantity',
        'difference_quantity',
        'adjustment_type',
        'notes',
        'counted_by',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:2',
        'physical_quantity' => 'decimal:2',
        'difference_quantity' => 'decimal:2',
    ];

    /**
     * Get the stock opname that owns the detail.
     */
    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Get the product associated with the detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who counted this item.
     */
    public function counter()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    /**
     * Boot method to auto-calculate difference and type.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            // Auto-calculate difference
            $detail->difference_quantity = $detail->physical_quantity - $detail->system_quantity;

            // Auto-determine adjustment type
            if ($detail->difference_quantity > 0) {
                $detail->adjustment_type = 'increase';
            } elseif ($detail->difference_quantity < 0) {
                $detail->adjustment_type = 'decrease';
            } else {
                // If no difference, set to null
                $detail->adjustment_type = null;
            }
        });
    }
}
