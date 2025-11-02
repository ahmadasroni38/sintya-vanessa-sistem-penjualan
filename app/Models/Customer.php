<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'address',
        'phone',
        'email',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationship to Sales
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive customers
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Auto-generate customer code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $lastCustomer = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastCustomer ? intval(substr($lastCustomer->customer_code, 3)) + 1 : 1;
                $customer->customer_code = 'CUS' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get customer's full info for display
     */
    public function getFullInfoAttribute()
    {
        return $this->customer_code . ' - ' . $this->customer_name;
    }
}
