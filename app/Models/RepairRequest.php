<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RepairRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'issue',
        'description',
        'issue_type',
        'severity',
        'priority',
        'status',
        'asset_id',
        'location_id',
        'requester_id',
        'assigned_to',
        'approved_by',
        'completed_by',
        'vendor_id',
        'work_order_id',
        'requested_date',
        'approved_date',
        'started_date',
        'completed_date',
        'due_date',
        'estimated_cost',
        'actual_cost',
        'estimated_hours',
        'actual_hours',
        'issue_details',
        'steps_to_reproduce',
        'impact_description',
        'repair_actions',
        'resolution_notes',
        'testing_notes',
        'required_parts',
        'parts_used',
        'tools_required',
        'tools_used',
        'asset_condition_before',
        'asset_condition_after',
        'condition_notes',
        'attachments',
        'before_images',
        'after_images',
        'documentation',
        'requires_follow_up',
        'follow_up_notes',
        'follow_up_date',
        'customer_satisfied',
        'satisfaction_rating',
        'feedback',
        'under_warranty',
        'warranty_claim_number',
        'safety_incident',
        'safety_notes',
        'requires_calibration',
        'environmental_impact',
        'disposal_notes',
        'approval_notes',
        'rejection_reason',
        'approval_workflow',
        'approval_threshold',
        'downtime_hours',
        'response_time_minutes',
        'resolution_time_hours',
        'service_level',
        'internal_notes',
        'custom_fields',
        'external_reference',
        'billable',
        'cost_center',
        'budget_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_date' => 'datetime',
        'approved_date' => 'datetime',
        'started_date' => 'datetime',
        'completed_date' => 'datetime',
        'due_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
        'approval_threshold' => 'decimal:2',
        'required_parts' => 'array',
        'parts_used' => 'array',
        'tools_required' => 'array',
        'tools_used' => 'array',
        'attachments' => 'array',
        'before_images' => 'array',
        'after_images' => 'array',
        'documentation' => 'array',
        'approval_workflow' => 'array',
        'custom_fields' => 'array',
        'requires_follow_up' => 'boolean',
        'customer_satisfied' => 'boolean',
        'under_warranty' => 'boolean',
        'safety_incident' => 'boolean',
        'requires_calibration' => 'boolean',
        'environmental_impact' => 'boolean',
        'billable' => 'boolean',
        'satisfaction_rating' => 'integer',
        'response_time_minutes' => 'integer',
        'resolution_time_hours' => 'integer',
    ];

    /**
     * Get the asset this repair request is for.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the location where this repair is taking place.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the user who requested this repair.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the user assigned to this repair.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who approved this repair.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who completed this repair.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the vendor for this repair.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the associated work order.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    /**
     * Scope to get repair requests by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending repair requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved repair requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get in progress repair requests.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope to get completed repair requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get repair requests by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get repair requests by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to get repair requests by issue type.
     */
    public function scopeByIssueType($query, $type)
    {
        return $query->where('issue_type', $type);
    }

    /**
     * Scope to get repair requests by asset.
     */
    public function scopeByAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope to get repair requests by location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope to get repair requests by requester.
     */
    public function scopeByRequester($query, $userId)
    {
        return $query->where('requester_id', $userId);
    }

    /**
     * Scope to get repair requests by assigned user.
     */
    public function scopeByAssignedUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to get repair requests by vendor.
     */
    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope to search repair requests.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('issue', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('issue_details', 'like', "%{$search}%")
              ->orWhere('resolution_notes', 'like', "%{$search}%")
              ->orWhereHas('asset', function ($assetQuery) use ($search) {
                  $assetQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
              })
              ->orWhereHas('requester', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope to get overdue repair requests.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get repair requests due soon.
     */
    public function scopeDueSoon($query, $days = 3)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope to get urgent repair requests.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent')
                    ->orWhere('severity', 'critical');
    }

    /**
     * Scope to get billable repair requests.
     */
    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    /**
     * Scope to get warranty repair requests.
     */
    public function scopeUnderWarranty($query)
    {
        return $query->where('under_warranty', true);
    }

    /**
     * Check if the repair request is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the repair request is due soon.
     */
    public function isDueSoon($days = 3): bool
    {
        return $this->due_date &&
               $this->due_date->between(now(), now()->addDays($days)) &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if the repair request is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent' || $this->severity === 'critical';
    }

    /**
     * Check if the repair request is pending approval.
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the repair request requires approval.
     */
    public function requiresApproval(): bool
    {
        return $this->approval_threshold &&
               $this->estimated_cost &&
               $this->estimated_cost > $this->approval_threshold;
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
     * Get the total repair time in hours.
     */
    public function getTotalRepairTimeAttribute(): ?float
    {
        if ($this->started_date && $this->completed_date) {
            return $this->started_date->diffInHours($this->completed_date, true);
        }
        return null;
    }

    /**
     * Get the response time in minutes.
     */
    public function getResponseTimeAttribute(): ?int
    {
        if ($this->requested_date && $this->started_date) {
            return $this->requested_date->diffInMinutes($this->started_date);
        }
        return null;
    }

    /**
     * Get the resolution time in hours.
     */
    public function getResolutionTimeAttribute(): ?int
    {
        if ($this->requested_date && $this->completed_date) {
            return $this->requested_date->diffInHours($this->completed_date);
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
     * Get time variance (actual vs estimated).
     */
    public function getTimeVarianceAttribute(): ?float
    {
        if ($this->estimated_hours && $this->actual_hours) {
            return $this->actual_hours - $this->estimated_hours;
        }
        return null;
    }

    /**
     * Approve the repair request.
     */
    public function approve(int $approvedById, string $notes = ''): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedById,
            'approved_date' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Reject the repair request.
     */
    public function reject(int $rejectedById, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $rejectedById,
            'approved_date' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Start the repair.
     */
    public function start(int $startedById = null): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_date' => now(),
            'assigned_to' => $startedById ?? $this->assigned_to,
        ]);
    }

    /**
     * Complete the repair.
     */
    public function complete(array $completionData = []): void
    {
        $updateData = array_merge([
            'status' => 'completed',
            'completed_date' => now(),
            'completed_by' => auth()->id(),
        ], $completionData);

        // Calculate performance metrics
        if (!isset($updateData['response_time_minutes'])) {
            $updateData['response_time_minutes'] = $this->response_time;
        }

        if (!isset($updateData['resolution_time_hours'])) {
            $updateData['resolution_time_hours'] = $this->resolution_time;
        }

        $this->update($updateData);
    }

    /**
     * Put the repair on hold.
     */
    public function putOnHold(string $reason = ''): void
    {
        $this->update([
            'status' => 'on_hold',
            'internal_notes' => $this->internal_notes . "\n[" . now() . "] Put on hold: " . $reason,
        ]);
    }

    /**
     * Cancel the repair request.
     */
    public function cancel(string $reason = ''): void
    {
        $this->update([
            'status' => 'cancelled',
            'internal_notes' => $this->internal_notes . "\n[" . now() . "] Cancelled: " . $reason,
        ]);
    }

    /**
     * Create a work order from this repair request.
     */
    public function createWorkOrder(): WorkOrder
    {
        $workOrder = WorkOrder::create([
            'title' => "Repair: {$this->issue}",
            'description' => "Repair request: {$this->code}\n\n{$this->description}",
            'type' => 'repair',
            'priority' => $this->priority,
            'status' => 'pending',
            'asset_id' => $this->asset_id,
            'location_id' => $this->location_id,
            'requester_id' => $this->requester_id,
            'assigned_to' => $this->assigned_to,
            'requested_date' => $this->requested_date,
            'due_date' => $this->due_date,
            'estimated_hours' => $this->estimated_hours,
            'estimated_cost' => $this->estimated_cost,
            'notes' => $this->issue_details,
        ]);

        $this->update(['work_order_id' => $workOrder->id]);

        return $workOrder;
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($repairRequest) {
            if (empty($repairRequest->code)) {
                $repairRequest->code = static::generateUniqueCode();
            }

            if (empty($repairRequest->requested_date)) {
                $repairRequest->requested_date = now();
            }
        });
    }

    /**
     * Generate a unique repair request code.
     */
    protected static function generateUniqueCode(): string
    {
        $prefix = 'RR';
        $year = now()->format('y');
        $month = now()->format('m');

        // Get the latest repair request for this month
        $latestRequest = static::where('code', 'like', "{$prefix}-{$year}{$month}%")
                              ->orderBy('code', 'desc')
                              ->first();

        if ($latestRequest) {
            // Extract the sequence number and increment
            $lastSequence = intval(substr($latestRequest->code, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
        $code = "{$prefix}-{$year}{$month}{$sequence}";

        // Ensure uniqueness
        while (static::where('code', $code)->exists()) {
            $newSequence++;
            $sequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$year}{$month}{$sequence}";
        }

        return $code;
    }
}
