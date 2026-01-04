<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Services\StockBalanceService;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opname_number',
        'opname_date',
        'location_id',
        'total_items',
        'description',
        'notes',
        'status',
        'created_by',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'total_items' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the details for this stock opname.
     */
    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class)->with('product');
    }

    /**
     * Get the location for this stock opname.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user who created this stock opname.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who completed this stock opname.
     */
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('opname_date', [$startDate, $endDate]);
    }

    /**
     * Start counting process: draft → in_progress
     */
    public function startCounting($userId = null)
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Only draft opname can be started');
        }

        $this->update([
            'status' => 'in_progress',
        ]);

        return $this;
    }

    /**
     * Complete opname: in_progress → completed, create adjustment
     */
    public function complete($userId)
    {
        if ($this->status !== 'in_progress') {
            throw new \Exception('Only in-progress opname can be completed');
        }

        DB::transaction(function () use ($userId) {
            // Update status
            $this->update([
                'status' => 'completed',
                'completed_by' => $userId,
                'completed_at' => now(),
            ]);

            // Create adjustment if there are differences
            $adjustment = $this->createAdjustmentFromOpname();

            // Update stock balances directly for all counted items
            $this->updateStockBalances();

            // Post the adjustment if created to ensure balance updates
            if ($adjustment) {
                $adjustment->post($userId);
            }
        });

        return $this;
    }

    /**
     * Cancel opname: any status → cancelled
     */
    public function cancel()
    {
        if ($this->status === 'cancelled') {
            throw new \Exception('Opname is already cancelled');
        }

        if ($this->status === 'completed') {
            throw new \Exception('Completed opname cannot be cancelled');
        }

        $this->update(['status' => 'cancelled']);

        return $this;
    }

    /**
     * Create stock adjustment from opname differences.
     */
    public function createAdjustmentFromOpname()
    {
        // Check if there are any differences
        $detailsWithDifferences = $this->details()->where('difference_quantity', '!=', 0)->get();

        if ($detailsWithDifferences->isEmpty()) {
            return null; // No adjustment needed
        }

        // Create master adjustment
        $adjustment = StockAdjustment::create([
            'adjustment_date' => $this->opname_date,
            'location_id' => $this->location_id,
            'total_items' => $detailsWithDifferences->count(),
            'description' => "Auto-generated from Stock Opname: {$this->opname_number}",
            'notes' => $this->notes,
            'status' => 'draft',
            'created_by' => $this->completed_by,
        ]);

        // Create adjustment details
        foreach ($detailsWithDifferences as $detail) {
            StockAdjustmentDetail::create([
                'stock_adjustment_id' => $adjustment->id,
                'product_id' => $detail->product_id,
                'system_quantity' => $detail->system_quantity,
                'actual_quantity' => $detail->physical_quantity,
                'difference_quantity' => $detail->difference_quantity,
                'adjustment_type' => $detail->adjustment_type,
                'reason' => "Stock Opname: {$this->opname_number}",
                'notes' => $detail->notes,
            ]);
        }

        return $adjustment;
    }

    /**
     * Update stock balances for all products in this opname
     */
    protected function updateStockBalances()
    {
        $productLocations = [];

        foreach ($this->details as $detail) {
            $productLocations[] = [
                'product_id' => $detail->product_id,
                'location_id' => $this->location_id,
            ];
        }

        // Remove duplicates and update balances
        $uniqueProductLocations = array_unique($productLocations, SORT_REGULAR);

        StockBalanceService::updateBalancesFromTransaction(
            $uniqueProductLocations,
            'opname',
            $this->opname_date->format('Y-m-d')
        );
    }

    /**
     * Boot method to auto-generate opname number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($opname) {
            if (empty($opname->opname_number)) {
                $last = static::withTrashed()->whereYear('created_at', date('Y'))->orderBy('id', 'desc')->first();
                $next = $last ? intval(substr($last->opname_number, -5)) + 1 : 1;
                $opname->opname_number = 'OPN-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
