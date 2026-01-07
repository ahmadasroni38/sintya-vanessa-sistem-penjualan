<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
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
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'color',
        'is_active',
        'parent_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
    ];

    /**
     * Get the parent location.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Get the child locations.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Get all descendant locations (children, grandchildren, etc.)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the assets in this location.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    /**
     * Get all stock cards for the location.
     */
    public function stockCards(): HasMany
    {
        return $this->hasMany(StockCard::class);
    }

    /**
     * Get all stock balances for the location.
     */
    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    /**
     * Get the asset categories associated with this location.
     */
    public function assetCategories(): HasMany
    {
        return $this->hasMany(AssetCategory::class, 'location_id');
    }

    /**
     * Scope to get only active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only root locations (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full hierarchical path of the location.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the hierarchy level of the location.
     */
    public function getHierarchyLevelAttribute(): int
    {
        $level = 0;
        $parent = $this->parent;

        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return $level;
    }

    /**
     * Get the full address of the location.
     */
    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Check if the location is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the location.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the location.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle the location's status.
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Check if this location can be deleted.
     * A location can be deleted if it has no assets, no children, and no asset categories.
     */
    public function canBeDeleted(): bool
    {
        // Check if has children
        if ($this->children()->count() > 0) {
            return false;
        }

        // Check if has asset categories (only if AssetCategory model exists)
        try {
            if ($this->assetCategories()->count() > 0) {
                return false;
            }
        } catch (\Exception $e) {
            // If AssetCategory model doesn't exist yet, assume no asset categories
        }

        // Check if has stock cards (only if StockCard model exists)
        try {
            if ($this->stockCards()->count() > 0) {
                return false;
            }
        } catch (\Exception $e) {
            // If StockCard model doesn't exist yet, assume no stock cards
        }

        // Check if has assets (only if Asset model exists)
        try {
            return $this->assets()->count() === 0;
        } catch (\Exception $e) {
            // If Asset model doesn't exist yet, assume no assets
            return true;
        }
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            if (empty($location->code)) {
                $location->code = static::generateUniqueCode($location->name);
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
            $baseCode = str_pad($baseCode, 3, 'L');
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
