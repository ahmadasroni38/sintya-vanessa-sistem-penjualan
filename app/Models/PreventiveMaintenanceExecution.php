<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PreventiveMaintenanceExecution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'execution_code',
        'preventive_maintenance_id',
        'work_order_id',
        'scheduled_date',
        'due_date',
        'started_at',
        'completed_at',
        'status',
        'assigned_to',
        'completed_by',
        'actual_duration_hours',
        'actual_cost',
        'checklist_results',
        'materials_used',
        'tools_used',
        'work_performed',
        'findings',
        'recommendations',
        'asset_condition',
        'issues_found',
        'follow_up_required',
        'follow_up_notes',
        'compliance_verified',
        'certification_number',
        'certification_valid_until',
        'attachments',
        'before_photos',
        'after_photos',
        'completed_on_time',
        'days_early_late',
        'efficiency_rating',
        'environmental_conditions',
        'notes',
        'completion_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'date',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'actual_duration_hours' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'checklist_results' => 'array',
        'materials_used' => 'array',
        'tools_used' => 'array',
        'follow_up_required' => 'boolean',
        'compliance_verified' => 'boolean',
        'certification_valid_until' => 'date',
        'attachments' => 'array',
        'before_photos' => 'array',
        'after_photos' => 'array',
        'completed_on_time' => 'boolean',
        'days_early_late' => 'integer',
        'efficiency_rating' => 'decimal:2',
        'environmental_conditions' => 'array',
    ];

    /**
     * Get the preventive maintenance schedule.
     */
    public function preventiveMaintenance(): BelongsTo
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'preventive_maintenance_id');
    }

    /**
     * Get the associated work order.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Get the user assigned to this execution.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who completed this execution.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope to get executions by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get scheduled executions.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to get in progress executions.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed executions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get overdue executions.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get executions due soon.
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get executions by preventive maintenance.
     */
    public function scopeByPreventiveMaintenance($query, $pmId)
    {
        return $query->where('preventive_maintenance_id', $pmId);
    }

    /**
     * Scope to get executions by assigned user.
     */
    public function scopeByAssignedUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to search executions.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('execution_code', 'like', "%{$search}%")
              ->orWhere('work_performed', 'like', "%{$search}%")
              ->orWhere('findings', 'like', "%{$search}%")
              ->orWhereHas('preventiveMaintenance', function ($pmQuery) use ($search) {
                  $pmQuery->where('title', 'like', "%{$search}%")
                          ->orWhere('code', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Check if the execution is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the execution is due soon.
     */
    public function isDueSoon($days = 7): bool
    {
        return $this->due_date &&
               $this->due_date->between(now(), now()->addDays($days)) &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the execution is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get days until due date.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if ($this->due_date) {
            return now()->diffInDays($this->due_date, false);
        }
        return null;
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
     * Start the execution.
     */
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);
    }

    /**
     * Complete the execution.
     */
    public function complete(array $completionData = []): void
    {
        $updateData = array_merge([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ], $completionData);

        // Calculate completion metrics
        if ($this->due_date) {
            $updateData['completed_on_time'] = now()->lte($this->due_date);
            $updateData['days_early_late'] = $this->due_date->diffInDays(now(), false);
        }

        // Calculate efficiency rating
        if (isset($completionData['actual_duration_hours']) && $this->preventiveMaintenance->estimated_duration_hours) {
            $efficiency = ($this->preventiveMaintenance->estimated_duration_hours / $completionData['actual_duration_hours']) * 100;
            $updateData['efficiency_rating'] = min($efficiency, 100);
        }

        $this->update($updateData);

        // Update parent preventive maintenance metrics
        $this->preventiveMaintenance->updateComplianceMetrics();

        // Create next execution if this is a recurring maintenance
        $this->preventiveMaintenance->createNextExecution();
    }

    /**
     * Skip the execution.
     */
    public function skip(string $reason = ''): void
    {
        $this->update([
            'status' => 'skipped',
            'completion_notes' => $reason
        ]);

        // Update parent preventive maintenance metrics
        $this->preventiveMaintenance->updateComplianceMetrics();
    }

    /**
     * Cancel the execution.
     */
    public function cancel(string $reason = ''): void
    {
        $this->update([
            'status' => 'cancelled',
            'completion_notes' => $reason
        ]);
    }

    /**
     * Mark as overdue (typically done by a scheduled job).
     */
    public function markAsOverdue(): void
    {
        if ($this->due_date && $this->due_date->isPast() && $this->status === 'scheduled') {
            $this->update(['status' => 'overdue']);

            // Update parent preventive maintenance metrics
            $this->preventiveMaintenance->updateComplianceMetrics();
        }
    }

    /**
     * Create a work order for this execution.
     */
    public function createWorkOrder(): WorkOrder
    {
        $pm = $this->preventiveMaintenance;

        $workOrder = WorkOrder::create([
            'title' => "PM: {$pm->title}",
            'description' => "Preventive maintenance execution: {$this->execution_code}\n\n{$pm->description}",
            'type' => 'maintenance',
            'priority' => $pm->priority,
            'status' => 'pending',
            'asset_id' => $pm->asset_id,
            'location_id' => $pm->location_id,
            'requester_id' => $pm->created_by,
            'assigned_to' => $this->assigned_to,
            'requested_date' => now(),
            'due_date' => $this->due_date,
            'estimated_hours' => $pm->estimated_duration_hours,
            'estimated_cost' => $pm->estimated_cost,
            'materials_needed' => $pm->required_materials,
            'notes' => $pm->work_instructions,
        ]);

        $this->update(['work_order_id' => $workOrder->id]);

        return $workOrder;
    }

    /**
     * Generate a unique execution code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($execution) {
            if (empty($execution->execution_code)) {
                $execution->execution_code = static::generateUniqueCode($execution->preventive_maintenance_id);
            }
        });

        static::updated(function ($execution) {
            // Auto-mark as overdue if due date has passed
            if ($execution->isDirty('due_date') && $execution->isOverdue()) {
                $execution->markAsOverdue();
            }
        });
    }

    /**
     * Generate a unique execution code.
     */
    protected static function generateUniqueCode(int $pmId): string
    {
        $prefix = 'PME';
        $timestamp = now()->format('ymdHis');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        $code = "{$prefix}-{$pmId}-{$timestamp}-{$random}";

        // Ensure uniqueness
        while (static::where('execution_code', $code)->exists()) {
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$pmId}-{$timestamp}-{$random}";
        }

        return $code;
    }
}
