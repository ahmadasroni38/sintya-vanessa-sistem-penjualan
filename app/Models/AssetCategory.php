<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCategory extends Model
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
        'color',
        'is_active',
        'parent_id',
        'location_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(AssetCategory::class, 'parent_id');
    }

    /**
     * Get all descendant categories (children, grandchildren, etc.)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the assets in this category.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    /**
     * Get the location this category belongs to.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the full hierarchical path of the category.
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
     * Get the hierarchy level of the category.
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
     * Check if the category is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the category.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the category.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle the category's status.
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Check if this category can be deleted.
     * A category can be deleted if it has no assets and no children.
     */
    public function canBeDeleted(): bool
    {
        // Check if has children
        if ($this->children()->count() > 0) {
            return false;
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

        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = static::generateUniqueCode($category->name);
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
            $baseCode = str_pad($baseCode, 3, 'X');
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
