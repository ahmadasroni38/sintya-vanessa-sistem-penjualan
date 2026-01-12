<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entry_number',
        'entry_date',
        'reference_number',
        'description',
        'entry_type',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'total_debit',
        'total_credit',
        'is_balanced',
        'can_edit',
        'can_delete',
        'can_post',
        'can_cancel',
        'formatted_entry_date',
        'formatted_posted_at',
        'status_label',
        'type_label',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    /**
     * Get the user who created this journal entry
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')
            ->select(['id', 'name', 'email']);
    }

    /**
     * Get the user who posted this journal entry
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by')
            ->select(['id', 'name', 'email']);
    }

    /**
     * Get all detail lines of this journal entry
     */
    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id')
            ->orderBy('id');
    }

    /**
     * Get debit details only
     */
    public function debitDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id')
            ->where('transaction_type', 'debit')
            ->orderBy('id');
    }

    /**
     * Get credit details only
     */
    public function creditDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id')
            ->where('transaction_type', 'credit')
            ->orderBy('id');
    }

    // =====================================================
    // QUERY SCOPES
    // =====================================================

    /**
     * Scope to filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter draft entries
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to filter posted entries
     */
    public function scopePosted(Builder $query): Builder
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope to filter cancelled entries
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to filter by entry type
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('entry_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by year
     */
    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('entry_date', $year);
    }

    /**
     * Scope to filter by month
     */
    public function scopeByMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('entry_date', $year)
            ->whereMonth('entry_date', $month);
    }

    /**
     * Scope to search by entry number, description, or reference
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('entry_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('reference_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get entries with their details and accounts
     */
    public function scopeWithFullDetails(Builder $query): Builder
    {
        return $query->with([
            'creator:id,name,email',
            'poster:id,name,email',
            'details' => function ($q) {
                $q->with('account:id,account_code,account_name,account_type,normal_balance');
            }
        ]);
    }

    // =====================================================
    // ACCESSORS (GETTERS)
    // =====================================================

    /**
     * Get total debit amount
     */
    public function getTotalDebitAttribute(): float
    {
        return (float) $this->details()
            ->where('transaction_type', 'debit')
            ->sum('amount');
    }

    /**
     * Get total credit amount
     */
    public function getTotalCreditAttribute(): float
    {
        return (float) $this->details()
            ->where('transaction_type', 'credit')
            ->sum('amount');
    }

    /**
     * Check if entry is balanced (debit = credit)
     */
    public function getIsBalancedAttribute(): bool
    {
        return $this->getTotalDebitAttribute() === $this->getTotalCreditAttribute();
    }

    /**
     * Check if entry can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if entry can be deleted
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if entry can be posted
     */
    public function getCanPostAttribute(): bool
    {
        return $this->status === 'draft'
            && $this->is_balanced
            && $this->details()->count() >= 2;
    }

    /**
     * Check if entry can be cancelled
     */
    public function getCanCancelAttribute(): bool
    {
        return $this->status === 'posted';
    }

    /**
     * Get formatted entry date
     */
    public function getFormattedEntryDateAttribute(): string
    {
        return $this->entry_date ? $this->entry_date->format('d M Y') : '-';
    }

    /**
     * Get formatted posted at
     */
    public function getFormattedPostedAtAttribute(): ?string
    {
        return $this->posted_at ? $this->posted_at->format('d M Y H:i') : null;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'posted' => 'Posted',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get entry type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->entry_type) {
            'general' => 'General Journal',
            'adjustment' => 'Adjustment Entry',
            'closing' => 'Closing Entry',
            'opening' => 'Opening Entry',
            default => ucfirst($this->entry_type),
        };
    }

    /**
     * Get balance difference (should be 0 if balanced)
     */
    public function getBalanceDifferenceAttribute(): float
    {
        return abs($this->total_debit - $this->total_credit);
    }

    /**
     * Check if entry has details
     */
    public function getHasDetailsAttribute(): bool
    {
        return $this->details()->count() > 0;
    }

    /**
     * Get number of detail lines
     */
    public function getDetailCountAttribute(): int
    {
        return $this->details()->count();
    }

    /**
     * Check if entry is posted
     */
    public function getIsPostedAttribute(): bool
    {
        return $this->status === 'posted';
    }

    /**
     * Check if entry is draft
     */
    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if entry is cancelled
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    // =====================================================
    // BUSINESS LOGIC METHODS
    // =====================================================

    /**
     * Calculate total debit
     */
    public function calculateTotalDebit(): float
    {
        return (float) $this->details()
            ->where('transaction_type', 'debit')
            ->sum('amount');
    }

    /**
     * Calculate total credit
     */
    public function calculateTotalCredit(): float
    {
        return (float) $this->details()
            ->where('transaction_type', 'credit')
            ->sum('amount');
    }

    /**
     * Check if journal entry is balanced
     */
    public function isBalanced(): bool
    {
        $debit = $this->calculateTotalDebit();
        $credit = $this->calculateTotalCredit();

        // Use epsilon comparison for floating point
        return abs($debit - $credit) < 0.01;
    }

    /**
     * Validate journal entry before posting
     */
    public function validate(): array
    {
        $errors = [];

        // Check status
        if ($this->status !== 'draft') {
            $errors[] = 'Only draft entries can be validated for posting';
        }

        // Check if has details
        if ($this->details()->count() < 2) {
            $errors[] = 'Journal entry must have at least 2 detail lines';
        }

        // Check if balanced
        if (!$this->isBalanced()) {
            $debit = $this->calculateTotalDebit();
            $credit = $this->calculateTotalCredit();
            $errors[] = "Journal entry is not balanced. Debit: {$debit}, Credit: {$credit}";
        }

        // Check if all details have valid accounts
        foreach ($this->details as $detail) {
            if (!$detail->account) {
                $errors[] = "Detail line #{$detail->id} has invalid account";
            }
            if ($detail->amount <= 0) {
                $errors[] = "Detail line #{$detail->id} has invalid amount";
            }
        }

        return $errors;
    }

    /**
     * Post the journal entry
     */
    public function post(?int $userId = null): bool
    {
        // Validate before posting
        $errors = $this->validate();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        DB::beginTransaction();
        try {
            // Update status
            $this->update([
                'status' => 'posted',
                'posted_by' => $userId ?? auth()->id(),
                'posted_at' => now(),
            ]);

            // Update account balances
            foreach ($this->details as $detail) {
                if ($detail->account) {
                    $detail->account->updateCurrentBalance();
                }
            }

            DB::commit();
            Log::info("Journal Entry #{$this->entry_number} posted successfully");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to post Journal Entry #{$this->entry_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a posted journal entry
     */
    public function cancel(?string $reason = null): bool
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted entries can be cancelled');
        }

        DB::beginTransaction();
        try {
            // Update status
            $this->update([
                'status' => 'cancelled',
            ]);

            // Recalculate account balances
            foreach ($this->details as $detail) {
                if ($detail->account) {
                    $detail->account->updateCurrentBalance();
                }
            }

            DB::commit();
            Log::info("Journal Entry #{$this->entry_number} cancelled. Reason: {$reason}");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to cancel Journal Entry #{$this->entry_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a reversal entry
     */
    public function reverse(string $description = null, ?\DateTime $reversalDate = null): self
    {
        if ($this->status !== 'posted') {
            throw new \Exception('Only posted entries can be reversed');
        }

        DB::beginTransaction();
        try {
            // Create reversal entry
            $reversal = self::create([
                'entry_date' => $reversalDate ?? now(),
                'reference_number' => $this->entry_number . '-REV',
                'description' => $description ?? "Reversal of {$this->entry_number}: {$this->description}",
                'entry_type' => $this->entry_type,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create reversed details (swap debit/credit)
            foreach ($this->details as $detail) {
                $reversal->details()->create([
                    'account_id' => $detail->account_id,
                    'transaction_type' => $detail->transaction_type === 'debit' ? 'credit' : 'debit',
                    'amount' => $detail->amount,
                    'description' => $detail->description,
                ]);
            }

            DB::commit();
            Log::info("Reversal entry created for Journal Entry #{$this->entry_number}");
            return $reversal;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create reversal for Journal Entry #{$this->entry_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Duplicate this journal entry
     */
    public function duplicate(?\DateTime $newDate = null): self
    {
        DB::beginTransaction();
        try {
            // Create duplicate entry
            $duplicate = self::create([
                'entry_date' => $newDate ?? $this->entry_date,
                'reference_number' => null,
                'description' => "Copy of: {$this->description}",
                'entry_type' => $this->entry_type,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Copy details
            foreach ($this->details as $detail) {
                $duplicate->details()->create([
                    'account_id' => $detail->account_id,
                    'transaction_type' => $detail->transaction_type,
                    'amount' => $detail->amount,
                    'description' => $detail->description,
                ]);
            }

            DB::commit();
            Log::info("Journal Entry #{$this->entry_number} duplicated");
            return $duplicate;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to duplicate Journal Entry #{$this->entry_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get affected accounts
     */
    public function getAffectedAccounts(): array
    {
        return $this->details()
            ->with('account')
            ->get()
            ->pluck('account')
            ->filter()
            ->unique('id')
            ->values()
            ->all();
    }

    /**
     * Get journal entry summary
     */
    public function getSummary(): array
    {
        return [
            'entry_number' => $this->entry_number,
            'entry_date' => $this->formatted_entry_date,
            'description' => $this->description,
            'type' => $this->type_label,
            'status' => $this->status_label,
            'total_debit' => $this->total_debit,
            'total_credit' => $this->total_credit,
            'is_balanced' => $this->is_balanced,
            'detail_count' => $this->detail_count,
            'created_by' => $this->creator?->name,
            'posted_by' => $this->poster?->name,
            'posted_at' => $this->formatted_posted_at,
        ];
    }

    // =====================================================
    // STATIC METHODS
    // =====================================================

    /**
     * Get entry types
     */
    public static function getEntryTypes(): array
    {
        return [
            'general' => 'General Journal',
            'adjustment' => 'Adjustment Entry',
            'closing' => 'Closing Entry',
            'opening' => 'Opening Entry',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'posted' => 'Posted',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Generate next entry number
     */
    public static function generateEntryNumber(?int $year = null): string
    {
        $year = $year ?? date('Y');

        $lastEntry = static::whereYear('created_at', $year)
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry && preg_match('/JE-(\d{4})(\d{5})/', $lastEntry->entry_number, $matches)) {
            $nextNumber = intval($matches[2]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'JE-' . $year . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get statistics
     */
    public static function getStatistics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('entry_date', [$startDate, $endDate]);
        }

        $totalEntries = $query->count();
        $draftEntries = (clone $query)->where('status', 'draft')->count();
        $postedEntries = (clone $query)->where('status', 'posted')->count();
        $cancelledEntries = (clone $query)->where('status', 'cancelled')->count();

        $totalAmount = (clone $query)
            ->where('status', 'posted')
            ->join('journal_entry_details', 'journal_entries.id', '=', 'journal_entry_details.journal_entry_id')
            ->where('transaction_type', 'debit')
            ->sum('amount');

        return [
            'total_entries' => $totalEntries,
            'draft_entries' => $draftEntries,
            'posted_entries' => $postedEntries,
            'cancelled_entries' => $cancelledEntries,
            'total_amount' => $totalAmount,
        ];
    }

    // =====================================================
    // EVENT HANDLERS
    // =====================================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate entry number when creating
        static::creating(function ($journalEntry) {
            if (empty($journalEntry->entry_number)) {
                $journalEntry->entry_number = static::generateEntryNumber();
            }

            // Set created_by if not set
            if (empty($journalEntry->created_by)) {
                $journalEntry->created_by = auth()->id();
            }
        });

        // Log when entry is created
        static::created(function ($journalEntry) {
            Log::info("Journal Entry created: {$journalEntry->entry_number}");
        });

        // Log when entry is updated
        static::updated(function ($journalEntry) {
            if ($journalEntry->wasChanged('status')) {
                Log::info("Journal Entry {$journalEntry->entry_number} status changed to {$journalEntry->status}");
            }
        });

        // Clean up details when deleting
        static::deleting(function ($journalEntry) {
            if ($journalEntry->status === 'posted') {
                throw new \Exception('Cannot delete posted journal entries. Please cancel first.');
            }

            // Delete details
            $journalEntry->details()->delete();
            Log::info("Journal Entry deleted: {$journalEntry->entry_number}");
        });

        // Restore details when restoring
        static::restoring(function ($journalEntry) {
            $journalEntry->details()->withTrashed()->restore();
            Log::info("Journal Entry restored: {$journalEntry->entry_number}");
        });
    }
}
