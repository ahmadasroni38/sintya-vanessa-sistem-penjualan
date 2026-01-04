<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StockBalanceService;

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

    /**
     * Update stock balance for this record
     */
    public function updateBalance()
    {
        return StockBalanceService::updateBalance(
            $this->product_id,
            $this->location_id,
            $this->last_transaction_type ?? 'manual_update',
            $this->last_transaction_date
        );
    }

    /**
     * Get current balance for product at location
     */
    public static function getCurrentBalance($productId, $locationId)
    {
        return StockBalanceService::getCurrentBalance($productId, $locationId);
    }

    /**
     * Get balance info for product at location
     */
    public static function getBalanceInfo($productId, $locationId)
    {
        return StockBalanceService::getBalanceInfo($productId, $locationId);
    }

    /**
     * Scope for products with low stock
     */
    public function scopeLowStock($query)
    {
        return $query->where('status', 'low_stock');
    }

    /**
     * Scope for out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
    }

    /**
     * Scope for overstock products
     */
    public function scopeOverstock($query)
    {
        return $query->where('status', 'overstock');
    }

    /**
     * Scope for in stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }
}
