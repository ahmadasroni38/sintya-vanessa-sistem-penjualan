<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_mutation_id',
        'product_id',
        'quantity',
        'available_stock',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'available_stock' => 'decimal:2',
    ];

    /**
     * Relationship to StockMutation
     */
    public function stockMutation()
    {
        return $this->belongsTo(StockMutation::class);
    }

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
