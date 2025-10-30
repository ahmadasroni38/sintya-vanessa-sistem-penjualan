<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_code',
        'product_name',
        'description',
        'product_type',
        'category_id',
        'unit_id',
        'purchase_price',
        'selling_price',
        'minimum_stock',
        'maximum_stock',
        'location_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product_type_label',
        'profit_margin',
        'profit_margin_percentage',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically uppercase product code before saving
        static::saving(function ($product) {
            $product->product_code = strtoupper($product->product_code);
        });

        // Clear cache after save or delete
        static::saved(function () {
            Cache::forget('active_products');
            Cache::forget('product_statistics');
        });

        static::deleted(function () {
            Cache::forget('active_products');
            Cache::forget('product_statistics');
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the unit that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the default location for the product.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get all stock in transactions for the product.
     */
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    /**
     * Get all stock mutations for the product.
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Get all stock adjustments for the product.
     */
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Get all stock cards for the product.
     */
    public function stockCards(): HasMany
    {
        return $this->hasMany(StockCard::class);
    }

    /**
     * Get all stock opname details for the product.
     */
    public function stockOpnameDetails(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    /**
     * Scope a query to only include active products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by product type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('product_type', $type);
    }

    /**
     * Scope a query to filter by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include low stock products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $locationId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query, ?int $locationId = null)
    {
        return $query->whereHas('stockCards', function ($q) use ($locationId) {
            if ($locationId) {
                $q->where('location_id', $locationId);
            }
            $q->selectRaw('product_id, MAX(balance) as current_stock')
              ->groupBy('product_id')
              ->havingRaw('current_stock < minimum_stock');
        });
    }

    /**
     * Get current stock for a specific location.
     *
     * @param int|null $locationId
     * @return int
     */
    public function getCurrentStock(?int $locationId = null): int
    {
        $cacheKey = "product_stock_{$this->id}" . ($locationId ? "_location_{$locationId}" : '');

        return Cache::remember($cacheKey, 300, function () use ($locationId) {
            $query = $this->stockCards();

            if ($locationId) {
                $query->where('location_id', $locationId);
            }

            $lastCard = $query->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            return $lastCard ? $lastCard->balance : 0;
        });
    }

    /**
     * Check if stock is below minimum threshold.
     *
     * @param int|null $locationId
     * @return bool
     */
    public function isBelowMinimum(?int $locationId = null): bool
    {
        return $this->getCurrentStock($locationId) < $this->minimum_stock;
    }

    /**
     * Check if stock is above maximum threshold.
     *
     * @param int|null $locationId
     * @return bool
     */
    public function isAboveMaximum(?int $locationId = null): bool
    {
        return $this->getCurrentStock($locationId) > $this->maximum_stock;
    }

    /**
     * Get stock status for a location.
     *
     * @param int|null $locationId
     * @return string
     */
    public function getStockStatus(?int $locationId = null): string
    {
        $currentStock = $this->getCurrentStock($locationId);

        if ($currentStock <= 0) {
            return 'out_of_stock';
        }

        if ($this->isBelowMinimum($locationId)) {
            return 'low_stock';
        }

        if ($this->isAboveMaximum($locationId)) {
            return 'overstock';
        }

        return 'normal';
    }

    /**
     * Get product type label.
     *
     * @return string
     */
    public function getProductTypeLabelAttribute(): string
    {
        return match($this->product_type) {
            'raw_material' => 'Raw Material',
            'finished_goods' => 'Finished Goods',
            'consumable' => 'Consumable',
            default => ucfirst(str_replace('_', ' ', $this->product_type)),
        };
    }

    /**
     * Get profit margin.
     *
     * @return float
     */
    public function getProfitMarginAttribute(): float
    {
        if (!$this->selling_price || !$this->purchase_price) {
            return 0;
        }

        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Get profit margin percentage.
     *
     * @return float
     */
    public function getProfitMarginPercentageAttribute(): float
    {
        if (!$this->selling_price || !$this->purchase_price || $this->purchase_price == 0) {
            return 0;
        }

        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Get total stock value based on purchase price.
     *
     * @param int|null $locationId
     * @return float
     */
    public function getTotalStockValue(?int $locationId = null): float
    {
        $stock = $this->getCurrentStock($locationId);
        return $stock * $this->purchase_price;
    }

    /**
     * Check if product can be deleted.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return $this->stockCards()->count() === 0;
    }

    /**
     * Get stock movement history.
     *
     * @param int|null $locationId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStockHistory(?int $locationId = null, int $limit = 10)
    {
        $query = $this->stockCards()
            ->with(['location'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Clear stock cache for this product.
     *
     * @return void
     */
    public function clearStockCache(): void
    {
        Cache::forget("product_stock_{$this->id}");

        // Clear for all locations
        $locations = Location::pluck('id');
        foreach ($locations as $locationId) {
            Cache::forget("product_stock_{$this->id}_location_{$locationId}");
        }
    }
}
