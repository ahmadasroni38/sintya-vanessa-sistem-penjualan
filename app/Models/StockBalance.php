<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'location_id',
        'current_balance',
        'minimum_stock',
        'maximum_stock',
        'status',
        'last_transaction_date',
        'last_transaction_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'current_balance' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'maximum_stock' => 'decimal:2',
        'last_transaction_date' => 'date',
    ];

    /**
     * Get the product that owns the stock balance.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the location that owns the stock balance.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the stock status based on current balance.
     */
    public function getStatusAttribute()
    {
        $balance = $this->current_balance;
        $minStock = $this->minimum_stock;
        $maxStock = $this->maximum_stock;

        if ($balance == 0) {
            return 'out_of_stock';
        } elseif ($balance < $minStock) {
            return 'low_stock';
        } elseif ($balance > $maxStock) {
            return 'overstock';
        } else {
            return 'in_stock';
        }
    }
}
