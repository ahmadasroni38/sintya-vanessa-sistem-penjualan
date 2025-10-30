<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opname_number', 'opname_date', 'location_id', 'description',
        'status', 'created_by', 'completed_by', 'completed_at',
    ];

    protected $casts = ['opname_date' => 'date', 'completed_at' => 'datetime'];

    public function location() { return $this->belongsTo(Location::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function completer() { return $this->belongsTo(User::class, 'completed_by'); }
    public function details() { return $this->hasMany(StockOpnameDetail::class); }

    public function scopeByStatus($query, $status) { return $query->where('status', $status); }

    public function complete($userId)
    {
        if ($this->status !== 'in_progress') throw new \Exception('Only in-progress opname can be completed');

        \DB::transaction(function () use ($userId) {
            $this->update(['status' => 'completed', 'completed_by' => $userId, 'completed_at' => now()]);
            $this->createStockAdjustments();
        });
        return $this;
    }

    protected function createStockAdjustments()
    {
        foreach ($this->details as $detail) {
            if ($detail->difference_quantity != 0) {
                $adjustmentType = $detail->difference_quantity > 0 ? 'increase' : 'decrease';

                StockAdjustment::create([
                    'product_id' => $detail->product_id,
                    'location_id' => $this->location_id,
                    'adjustment_date' => $this->opname_date,
                    'system_quantity' => $detail->system_quantity,
                    'actual_quantity' => $detail->physical_quantity,
                    'difference_quantity' => $detail->difference_quantity,
                    'adjustment_type' => $adjustmentType,
                    'reason' => 'Stock Opname: ' . $this->opname_number,
                    'notes' => $detail->notes,
                    'status' => 'posted',
                    'created_by' => $this->created_by,
                    'approved_by' => $this->completed_by,
                    'approved_at' => $this->completed_at,
                ]);
            }
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($opname) {
            if (empty($opname->opname_number)) {
                $last = static::whereYear('created_at', date('Y'))->orderBy('id', 'desc')->first();
                $next = $last ? intval(substr($last->opname_number, -5)) + 1 : 1;
                $opname->opname_number = 'SO-' . date('Y') . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
