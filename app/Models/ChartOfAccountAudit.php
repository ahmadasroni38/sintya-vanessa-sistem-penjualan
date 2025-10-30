<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChartOfAccountAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'chart_of_account_id',
        'event_type',
        'old_values',
        'new_values',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    // Scopes
    public function scopeByEventType(Builder $query, string $eventType): Builder
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getFormattedEventTypeAttribute(): string
    {
        return ucfirst($this->event_type);
    }

    public function getHasChangesAttribute(): bool
    {
        return !empty($this->old_values) || !empty($this->new_values);
    }

    public function getChangesSummaryAttribute(): string
    {
        if (!$this->has_changes) {
            return 'No changes detected';
        }

        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;

                if ($oldValue !== $newValue) {
                    $changes[] = "{$key}: '{$oldValue}' → '{$newValue}'";
                }
            }
        }

        return implode(', ', $changes);
    }

    // Static Methods
    public static function getEventTypes(): array
    {
        return [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored'
        ];
    }

    public static function logEvent(int $accountId, string $eventType, array $oldValues = [], array $newValues = [], ?string $userId = null, ?string $userName = null, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return self::create([
            'chart_of_account_id' => $accountId,
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId ?? auth()->id(),
            'user_name' => $userName ?? auth()->user()->name ?? 'system',
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }
}
