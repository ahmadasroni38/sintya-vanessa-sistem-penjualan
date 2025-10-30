<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'location_id', 'transaction_date', 'transaction_type',
        'reference_id', 'reference_number', 'quantity_in', 'quantity_out',
        'balance', 'unit_price', 'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity_in' => 'integer',
        'quantity_out' => 'integer',
        'balance' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function location() { return $this->belongsTo(Location::class); }

    public function scopeByProduct($query, $productId) { return $query->where('product_id', $productId); }
    public function scopeByLocation($query, $locationId) { return $query->where('location_id', $locationId); }
    public function scopeDateRange($query, $start, $end) { return $query->whereBetween('transaction_date', [$start, $end]); }
}
