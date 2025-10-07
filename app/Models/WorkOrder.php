<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WorkOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'code',
        'description',
        'type',
        'priority',
        'status',
        'asset_id',
        'location_id',
        'requester_id',
        'assigned_to',
        'requested_date',
        'due_date',
        'started_at',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'estimated_cost',
        'actual_cost',
        'materials_needed',
        'attachments',
        'notes',
        'completion_notes',
        'is_recurring',
        'recurring_frequency',
        'recurring_interval',
        'next_occurrence',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_date' => 'datetime',
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'materials_needed' => 'array',
        'attachments' => 'array',
        'is_recurring' => 'boolean',
        'recurring_interval' => 'integer',
        'next_occurrence' => 'datetime',
    ];

    /**
     * Get the asset this work order is for.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the location this work order is in.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the user who requested this work order.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the user assigned to this work order.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to get work orders by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get work orders by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get work orders by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get work orders by asset.
     */
    public function scopeByAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope to get work orders by location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope to get work orders by assigned user.
     */
    public function scopeByAssignedUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to get work orders by requester.
     */
    public function scopeByRequester($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    /**
     * Scope to search work orders by title, code, or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get overdue work orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get due soon work orders (within next 7 days).
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get active work orders.
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get completed work orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending work orders.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'pending']);
    }

    /**
     * Scope to get in progress work orders.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Check if the work order is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the work order is due soon.
     */
    public function isDueSoon($days = 7): bool
    {
        return $this->due_date &&
               $this->due_date->between(now(), now()->addDays($days)) &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the work order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the work order is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the work order is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Get the duration in hours.
     */
    public function getDurationAttribute(): ?float
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInHours($this->completed_at, true);
        }
        return null;
    }

    /**
     * Get the days until due date.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if ($this->due_date) {
            return now()->diffInDays($this->due_date, false);
        }
        return null;
    }

    /**
     * Get cost variance (actual vs estimated).
     */
    public function getCostVarianceAttribute(): ?float
    {
        if ($this->estimated_cost && $this->actual_cost) {
            return $this->actual_cost - $this->estimated_cost;
        }
        return null;
    }

    /**
     * Get hours variance (actual vs estimated).
     */
    public function getHoursVarianceAttribute(): ?float
    {
        if ($this->estimated_hours && $this->actual_hours) {
            return $this->actual_hours - $this->estimated_hours;
        }
        return null;
    }

    /**
     * Mark work order as started.
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
    }

    /**
     * Mark work order as completed.
     */
    public function markAsCompleted(array $completionData = []): void
    {
        $updateData = array_merge([
            'status' => 'completed',
            'completed_at' => now()
        ], $completionData);

        $this->update($updateData);

        // Schedule next occurrence if recurring
        if ($this->is_recurring) {
            $this->scheduleNextOccurrence();
        }
    }

    /**
     * Schedule next occurrence for recurring work orders.
     */
    public function scheduleNextOccurrence(): void
    {
        if (!$this->is_recurring || !$this->recurring_frequency) {
            return;
        }

        $interval = $this->recurring_interval ?? 1;
        $nextDate = null;

        switch ($this->recurring_frequency) {
            case 'daily':
                $nextDate = now()->addDays($interval);
                break;
            case 'weekly':
                $nextDate = now()->addWeeks($interval);
                break;
            case 'monthly':
                $nextDate = now()->addMonths($interval);
                break;
            case 'yearly':
                $nextDate = now()->addYears($interval);
                break;
        }

        if ($nextDate) {
            $this->update(['next_occurrence' => $nextDate]);

            // Create new work order for next occurrence
            $this->createRecurringWorkOrder($nextDate);
        }
    }

    /**
     * Create a new work order for recurring schedule.
     */
    private function createRecurringWorkOrder(Carbon $dueDate): void
    {
        $newWorkOrder = $this->replicate([
            'code', 'status', 'started_at', 'completed_at',
            'actual_hours', 'actual_cost', 'completion_notes'
        ]);

        $newWorkOrder->code = static::generateUniqueCode($this->title);
        $newWorkOrder->status = 'pending';
        $newWorkOrder->requested_date = now();
        $newWorkOrder->due_date = $dueDate;
        $newWorkOrder->started_at = null;
        $newWorkOrder->completed_at = null;
        $newWorkOrder->actual_hours = null;
        $newWorkOrder->actual_cost = null;
        $newWorkOrder->completion_notes = null;

        $newWorkOrder->save();
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workOrder) {
            if (empty($workOrder->code)) {
                $workOrder->code = static::generateUniqueCode($workOrder->title);
            }

            if (empty($workOrder->requested_date)) {
                $workOrder->requested_date = now();
            }
        });
    }

    /**
     * Generate a unique code based on the title.
     */
    protected static function generateUniqueCode(string $title): string
    {
        $prefix = 'WO';
        $baseCode = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($title)), 0, 4));

        if (strlen($baseCode) < 2) {
            $baseCode = str_pad($baseCode, 2, 'X');
        }

        $code = $prefix . '-' . $baseCode;
        $counter = 1;

        while (static::where('code', $code)->exists()) {
            $code = $prefix . '-' . $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }
}
