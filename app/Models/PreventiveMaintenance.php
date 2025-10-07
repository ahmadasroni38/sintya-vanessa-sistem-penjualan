<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PreventiveMaintenance extends Model
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
        'maintenance_type',
        'priority',
        'status',
        'asset_id',
        'location_id',
        'created_by',
        'assigned_to',
        'frequency_type',
        'frequency_value',
        'start_date',
        'end_date',
        'next_due_date',
        'last_completed_date',
        'custom_frequency_settings',
        'skip_dates',
        'estimated_duration_hours',
        'estimated_cost',
        'required_materials',
        'required_tools',
        'safety_requirements',
        'work_instructions',
        'checklist_items',
        'safety_notes',
        'attachments',
        'compliance_standard',
        'requires_certification',
        'certification_required',
        'total_occurrences',
        'completed_occurrences',
        'missed_occurrences',
        'average_completion_time',
        'average_cost',
        'compliance_rate',
        'last_updated_date',
        'notification_settings',
        'auto_create_work_orders',
        'advance_notice_days',
        'notes',
        'is_template',
        'template_source_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'last_completed_date' => 'date',
        'last_updated_date' => 'date',
        'custom_frequency_settings' => 'array',
        'skip_dates' => 'array',
        'estimated_duration_hours' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'required_materials' => 'array',
        'required_tools' => 'array',
        'safety_requirements' => 'array',
        'checklist_items' => 'array',
        'attachments' => 'array',
        'requires_certification' => 'boolean',
        'total_occurrences' => 'integer',
        'completed_occurrences' => 'integer',
        'missed_occurrences' => 'integer',
        'average_completion_time' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'compliance_rate' => 'decimal:2',
        'notification_settings' => 'array',
        'auto_create_work_orders' => 'boolean',
        'advance_notice_days' => 'integer',
        'is_template' => 'boolean',
        'frequency_value' => 'integer',
    ];

    /**
     * Get the asset this preventive maintenance is for.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the location this preventive maintenance is in.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the user who created this preventive maintenance.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this preventive maintenance.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the template source (if created from template).
     */
    public function templateSource(): BelongsTo
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'template_source_id');
    }

    /**
     * Get the preventive maintenances created from this template.
     */
    public function templatedInstances(): HasMany
    {
        return $this->hasMany(PreventiveMaintenance::class, 'template_source_id');
    }

    /**
     * Get the executions for this preventive maintenance.
     */
    public function executions(): HasMany
    {
        return $this->hasMany(PreventiveMaintenanceExecution::class);
    }

    /**
     * Get the upcoming executions for this preventive maintenance.
     */
    public function upcomingExecutions(): HasMany
    {
        return $this->hasMany(PreventiveMaintenanceExecution::class)
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->orderBy('scheduled_date');
    }

    /**
     * Get the completed executions for this preventive maintenance.
     */
    public function completedExecutions(): HasMany
    {
        return $this->hasMany(PreventiveMaintenanceExecution::class)
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc');
    }

    /**
     * Scope to get active preventive maintenances.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get inactive preventive maintenances.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get preventive maintenances by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get preventive maintenances by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get preventive maintenances by maintenance type.
     */
    public function scopeByMaintenanceType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Scope to get preventive maintenances by asset.
     */
    public function scopeByAsset($query, $assetId)
    {
        return $query->where('asset_id', $assetId);
    }

    /**
     * Scope to get preventive maintenances by location.
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope to get preventive maintenances by assigned user.
     */
    public function scopeByAssignedUser($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope to get preventive maintenances by frequency.
     */
    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency_type', $frequency);
    }

    /**
     * Scope to search preventive maintenances.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('work_instructions', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get due preventive maintenances.
     */
    public function scopeDue($query, $date = null)
    {
        $dueDate = $date ? Carbon::parse($date) : now();
        return $query->where('next_due_date', '<=', $dueDate)
                    ->where('status', 'active');
    }

    /**
     * Scope to get overdue preventive maintenances.
     */
    public function scopeOverdue($query)
    {
        return $query->where('next_due_date', '<', now())
                    ->where('status', 'active');
    }

    /**
     * Scope to get due soon preventive maintenances.
     */
    public function scopeDueSoon($query, $days = 7)
    {
        return $query->whereBetween('next_due_date', [now(), now()->addDays($days)])
                    ->where('status', 'active');
    }

    /**
     * Scope to get templates.
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope to get non-templates.
     */
    public function scopeNonTemplates($query)
    {
        return $query->where('is_template', false);
    }

    /**
     * Check if this preventive maintenance is due.
     */
    public function isDue(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast() && $this->status === 'active';
    }

    /**
     * Check if this preventive maintenance is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast() && $this->status === 'active';
    }

    /**
     * Check if this preventive maintenance is due soon.
     */
    public function isDueSoon($days = 7): bool
    {
        return $this->next_due_date &&
               $this->next_due_date->between(now(), now()->addDays($days)) &&
               $this->status === 'active';
    }

    /**
     * Get days until next due date.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if ($this->next_due_date) {
            return now()->diffInDays($this->next_due_date, false);
        }
        return null;
    }

    /**
     * Get the completion rate as a percentage.
     */
    public function getCompletionRateAttribute(): float
    {
        if (!$this->total_occurrences || $this->total_occurrences === 0) {
            return 0;
        }
        return round(($this->completed_occurrences / $this->total_occurrences) * 100, 2);
    }

    /**
     * Get the efficiency rating.
     */
    public function getEfficiencyRatingAttribute(): ?float
    {
        if ($this->estimated_duration_hours && $this->average_completion_time) {
            $efficiency = ($this->estimated_duration_hours / $this->average_completion_time) * 100;
            return round(min($efficiency, 100), 2);
        }
        return null;
    }

    /**
     * Calculate the next due date based on frequency.
     */
    public function calculateNextDueDate(Carbon $fromDate = null): Carbon
    {
        $baseDate = $fromDate ?? $this->last_completed_date ?? $this->start_date ?? now();
        $nextDate = $baseDate;

        $interval = $this->frequency_value ?? 1;

        switch ($this->frequency_type) {
            case 'daily':
                $nextDate = $baseDate->addDays($interval);
                break;
            case 'weekly':
                $nextDate = $baseDate->addWeeks($interval);
                break;
            case 'monthly':
                $nextDate = $baseDate->addMonths($interval);
                break;
            case 'quarterly':
                $nextDate = $baseDate->addMonths(3 * $interval);
                break;
            case 'semi_annual':
                $nextDate = $baseDate->addMonths(6 * $interval);
                break;
            case 'annual':
                $nextDate = $baseDate->addYears($interval);
                break;
            case 'custom':
                if ($this->custom_frequency_settings) {
                    // Handle custom frequency logic here
                    $nextDate = $this->calculateCustomFrequency($baseDate);
                }
                break;
        }

        // Skip holidays and designated skip dates
        if ($this->skip_dates) {
            $nextDate = $this->skipRestrictedDates($nextDate);
        }

        return $nextDate;
    }

    /**
     * Calculate custom frequency based on settings.
     */
    private function calculateCustomFrequency(Carbon $baseDate): Carbon
    {
        $settings = $this->custom_frequency_settings;

        // This is a placeholder for complex custom frequency calculations
        // You can implement specific logic based on your requirements

        return $baseDate->addDays(30); // Default to 30 days
    }

    /**
     * Skip restricted dates (holidays, weekends, etc.).
     */
    private function skipRestrictedDates(Carbon $date): Carbon
    {
        $skipDates = $this->skip_dates ?? [];

        while (in_array($date->format('Y-m-d'), $skipDates) || $date->isWeekend()) {
            $date = $date->addDay();
        }

        return $date;
    }

    /**
     * Update the next due date.
     */
    public function updateNextDueDate(): void
    {
        $this->next_due_date = $this->calculateNextDueDate();
        $this->save();
    }

    /**
     * Create the next execution instance.
     */
    public function createNextExecution(): PreventiveMaintenanceExecution
    {
        $nextDueDate = $this->calculateNextDueDate();

        $execution = new PreventiveMaintenanceExecution([
            'execution_code' => $this->generateExecutionCode(),
            'preventive_maintenance_id' => $this->id,
            'scheduled_date' => $nextDueDate,
            'due_date' => $nextDueDate,
            'status' => 'scheduled',
            'assigned_to' => $this->assigned_to,
        ]);

        $execution->save();

        // Auto-create work order if enabled
        if ($this->auto_create_work_orders) {
            $execution->createWorkOrder();
        }

        return $execution;
    }

    /**
     * Generate a unique execution code.
     */
    private function generateExecutionCode(): string
    {
        $prefix = 'PME';
        $baseCode = strtoupper(substr($this->code, 0, 6));
        $timestamp = now()->format('ymdHis');

        return $prefix . '-' . $baseCode . '-' . $timestamp;
    }

    /**
     * Update compliance metrics.
     */
    public function updateComplianceMetrics(): void
    {
        $executions = $this->executions;

        $this->total_occurrences = $executions->count();
        $this->completed_occurrences = $executions->where('status', 'completed')->count();
        $this->missed_occurrences = $executions->where('status', 'overdue')->count();

        if ($this->total_occurrences > 0) {
            $this->compliance_rate = ($this->completed_occurrences / $this->total_occurrences) * 100;
        } else {
            $this->compliance_rate = 0;
        }

        // Update averages
        $completedExecutions = $executions->where('status', 'completed');
        if ($completedExecutions->count() > 0) {
            $this->average_completion_time = $completedExecutions->avg('actual_duration_hours');
            $this->average_cost = $completedExecutions->avg('actual_cost');
        }

        $this->last_updated_date = now();
        $this->save();
    }

    /**
     * Generate a unique code if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($preventiveMaintenance) {
            if (empty($preventiveMaintenance->code)) {
                $preventiveMaintenance->code = static::generateUniqueCode($preventiveMaintenance->title);
            }

            if (empty($preventiveMaintenance->next_due_date)) {
                $preventiveMaintenance->next_due_date = $preventiveMaintenance->start_date ?? now();
            }
        });

        static::updated(function ($preventiveMaintenance) {
            if ($preventiveMaintenance->isDirty(['frequency_type', 'frequency_value', 'start_date'])) {
                $preventiveMaintenance->updateNextDueDate();
            }
        });
    }

    /**
     * Generate a unique code based on the title.
     */
    protected static function generateUniqueCode(string $title): string
    {
        $prefix = 'PM';
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
