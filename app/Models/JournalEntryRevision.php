<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryRevision extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'journal_entry_id',
        'revision_number',
        'changes',
        'revised_by',
        'revision_notes',
        'created_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the journal entry that owns the revision.
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the user who made the revision.
     */
    public function revisor()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    /**
     * Scope a query to only include revisions by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('revised_by', $userId);
    }

    /**
     * Scope a query to only include revisions after a specific revision number.
     */
    public function scopeAfterRevision($query, $revisionNumber)
    {
        return $query->where('revision_number', '>', $revisionNumber);
    }

    /**
     * Get the formatted changes for display.
     */
    public function getFormattedChangesAttribute(): array
    {
        $changes = $this->changes;
        $formatted = [];

        foreach ($changes as $field => $change) {
            $formatted[] = [
                'field' => $this->formatFieldName($field),
                'old_value' => $this->formatValue($change['old'] ?? null),
                'new_value' => $this->formatValue($change['new'] ?? null),
            ];
        }

        return $formatted;
    }

    /**
     * Format field name for display.
     */
    private function formatFieldName(string $field): string
    {
        $fieldNames = [
            'entry_date' => 'Entry Date',
            'reference_number' => 'Reference Number',
            'description' => 'Description',
            'entry_type' => 'Entry Type',
            'status' => 'Status',
            'total_debit' => 'Total Debit',
            'total_credit' => 'Total Credit',
            'currency' => 'Currency',
            'exchange_rate' => 'Exchange Rate',
        ];

        return $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Format value for display.
     */
    private function formatValue($value): string
    {
        if ($value === null) {
            return 'N/A';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Get the summary of changes.
     */
    public function getChangeSummaryAttribute(): string
    {
        $changes = count($this->changes);

        if ($changes === 0) {
            return 'No changes';
        }

        if ($changes === 1) {
            $field = key($this->changes);
            return "Changed {$this->formatFieldName($field)}";
        }

        return "Changed {$changes} fields";
    }

    /**
     * Check if a specific field was changed.
     */
    public function hasFieldChanged(string $field): bool
    {
        return array_key_exists($field, $this->changes);
    }

    /**
     * Get the old value of a specific field.
     */
    public function getOldValue(string $field)
    {
        return $this->changes[$field]['old'] ?? null;
    }

    /**
     * Get the new value of a specific field.
     */
    public function getNewValue(string $field)
    {
        return $this->changes[$field]['new'] ?? null;
    }
}
