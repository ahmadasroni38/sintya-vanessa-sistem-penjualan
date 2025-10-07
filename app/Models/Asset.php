<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'serial_number',
        'model_number',
        'manufacturer',
        'purchase_date',
        'purchase_price',
        'current_value',
        'quantity',
        'condition',
        'status',
        'image_path',
        'specifications',
        'warranty_expiry',
        'notes',
        'category_id',
        'location_id',
        'parent_asset_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'quantity' => 'integer',
        'specifications' => 'array',
    ];

    /**
     * Get the category this asset belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    /**
     * Get the location this asset is in.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the parent asset (for hierarchical assets).
     */
    public function parentAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'parent_asset_id');
    }

    /**
     * Get the child assets (components).
     */
    public function childAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'parent_asset_id');
    }

    /**
     * Get all descendant assets (components of components, etc.)
     */
    public function descendantAssets(): HasMany
    {
        return $this->childAssets()->with('descendantAssets');
    }

    /**
     * Scope to get only active assets.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get assets by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get assets by condition.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Scope to get assets by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get assets by location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope to search assets by name, code, or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('model_number', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full hierarchical path of the asset.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parentAsset;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parentAsset;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the hierarchy level of the asset.
     */
    public function getHierarchyLevelAttribute(): int
    {
        $level = 0;
        $parent = $this->parentAsset;

        while ($parent) {
            $level++;
            $parent = $parent->parentAsset;
        }

        return $level;
    }

    /**
     * Get the image URL for the asset.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    /**
     * Check if the asset is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the asset is under maintenance.
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Check if the asset is retired.
     */
    public function isRetired(): bool
    {
        return $this->status === 'retired';
    }

    /**
     * Check if the asset is in good condition.
     */
    public function isInGoodCondition(): bool
    {
        return in_array($this->condition, ['excellent', 'good']);
    }

    /**
     * Calculate depreciation (simple straight-line method).
     */
    public function getDepreciationAttribute(): float
    {
        if (!$this->purchase_price || !$this->purchase_date) {
            return 0;
        }

        $years = now()->diffInYears($this->purchase_date);
        $usefulLife = 5; // Assume 5 year useful life
        $annualDepreciation = $this->purchase_price / $usefulLife;

        return min($annualDepreciation * $years, $this->purchase_price);
    }

    /**
     * Get current book value.
     */
    public function getBookValueAttribute(): float
    {
        return $this->current_value ?? max(0, $this->purchase_price - $this->depreciation);
    }

    /**
     * Check if warranty is still valid.
     */
    public function isWarrantyValid(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }

    /**
     * Check if this asset can be deleted.
     * An asset can be deleted if it has no child assets.
     */
    public function canBeDeleted(): bool
    {
        return $this->childAssets()->count() === 0;
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->code)) {
                $asset->code = static::generateUniqueCode($asset->name);
            }
        });
    }

    /**
     * Generate a unique code based on the name.
     */
    protected static function generateUniqueCode(string $name): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($name)), 0, 6));

        if (strlen($baseCode) < 3) {
            $baseCode = str_pad($baseCode, 3, 'A');
        }

        $code = $baseCode;
        $counter = 1;

        while (static::where('code', $code)->exists()) {
            $code = $baseCode . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }
}
