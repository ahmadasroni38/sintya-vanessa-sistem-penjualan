<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relationship to Sale
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Boot method for auto-calculating amounts
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            // Calculate discount amount
            $discountAmount = ($detail->unit_price * $detail->quantity) * ($detail->discount_percent / 100);
            $detail->discount_amount = $discountAmount;

            // Calculate subtotal after discount
            $subtotal = ($detail->unit_price * $detail->quantity) - $discountAmount;

            // Calculate tax amount
            $taxAmount = $subtotal * ($detail->tax_percent / 100);
            $detail->tax_amount = $taxAmount;

            // Calculate total price
            $detail->total_price = $subtotal + $taxAmount;
        });
    }

    /**
     * Get subtotal before tax and discount
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get subtotal after discount
     */
    public function getSubtotalAfterDiscountAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }
}
