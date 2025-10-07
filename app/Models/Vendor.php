<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
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
        'company_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'contact_person',
        'contact_phone',
        'contact_email',
        'vendor_type',
        'description',
        'is_active',
        'credit_limit',
        'payment_terms',
        'tax_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the assets from this vendor.
     * Note: This relationship will be available when Assets model is created
     */
    public function assets(): HasMany
    {
        // For now, return an empty relationship since Asset model doesn't exist yet
        // This will be updated when Asset model is created
        if (!class_exists('App\Models\Asset')) {
            return $this->hasMany(self::class, 'non_existent_field')->whereRaw('1 = 0');
        }
        return $this->hasMany('App\Models\Asset', 'vendor_id');
    }

    /**
     * Get the purchase orders from this vendor.
     * Note: This relationship will be available when PurchaseOrder model is created
     */
    public function purchaseOrders(): HasMany
    {
        // For now, return an empty relationship since PurchaseOrder model doesn't exist yet
        if (!class_exists('App\Models\PurchaseOrder')) {
            return $this->hasMany(self::class, 'non_existent_field')->whereRaw('1 = 0');
        }
        return $this->hasMany('App\Models\PurchaseOrder', 'vendor_id');
    }

    /**
     * Scope to get only active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by vendor type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    /**
     * Check if the vendor is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Activate the vendor.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the vendor.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle the vendor's status.
     */
    public function toggleStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Check if this vendor can be deleted.
     * A vendor can be deleted if it has no assets and no purchase orders.
     */
    public function canBeDeleted(): bool
    {
        // Check if has assets (only if Asset model exists)
        try {
            if ($this->assets()->count() > 0) {
                return false;
            }
        } catch (\Exception $e) {
            // If Asset model doesn't exist yet, assume no assets
        }

        // Check if has purchase orders (only if PurchaseOrder model exists)
        try {
            return $this->purchaseOrders()->count() === 0;
        } catch (\Exception $e) {
            // If PurchaseOrder model doesn't exist yet, assume no purchase orders
            return true;
        }
    }

    /**
     * Get the vendor's display name (company name or name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->name;
    }

    /**
     * Get the vendor's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get vendor type label.
     */
    public function getVendorTypeLabelAttribute(): string
    {
        $types = [
            'supplier' => 'Supplier',
            'service_provider' => 'Service Provider',
            'contractor' => 'Contractor',
            'other' => 'Other',
        ];

        return $types[$this->vendor_type] ?? $this->vendor_type;
    }

    /**
     * Get payment terms label.
     */
    public function getPaymentTermsLabelAttribute(): string
    {
        $terms = [
            'cash' => 'Cash',
            'net_15' => 'Net 15 Days',
            'net_30' => 'Net 30 Days',
            'net_45' => 'Net 45 Days',
            'net_60' => 'Net 60 Days',
            'custom' => 'Custom',
        ];

        return $terms[$this->payment_terms] ?? $this->payment_terms;
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (empty($vendor->code)) {
                $vendor->code = static::generateUniqueCode($vendor->name ?: $vendor->company_name);
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