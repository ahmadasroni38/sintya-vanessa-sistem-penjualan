<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relationship to StockIn
     */
    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Boot method untuk auto-calculate total_price
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $detail->total_price = $detail->quantity * $detail->unit_price;
        });
    }
}
