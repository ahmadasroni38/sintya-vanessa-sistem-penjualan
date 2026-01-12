<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntryDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'transaction_type',
        'amount',
        'debit_amount',
        'credit_amount',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'description',
        'department_id',
        'project_id',
        'reconciliation_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'debit_amount' => 0,
        'credit_amount' => 0,
        'tax_amount' => 0,
    ];

    // Relationships
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id')
            ->select(['id', 'entry_number', 'entry_date', 'status']);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id')
            ->select(['id', 'account_code', 'account_name', 'account_type', 'normal_balance']);
    }

    public function department()
    {
        return $this->belongsTo(Location::class, 'department_id')
            ->select(['id', 'name']);
    }

    public function project()
    {
        return $this->belongsTo(Product::class, 'project_id')
            ->select(['id', 'name']);
    }

    // Scopes
    public function scopeDebit(Builder $query): Builder
    {
        return $query->where('transaction_type', 'debit');
    }

    public function scopeCredit(Builder $query): Builder
    {
        return $query->where('transaction_type', 'credit');
    }

    public function scopeByAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByDepartment(Builder $query, ?int $departmentId): Builder
    {
        if ($departmentId) {
            return $query->where('department_id', $departmentId);
        }
        return $query;
    }

    public function scopeByProject(Builder $query, ?int $projectId): Builder
    {
        if ($projectId) {
            return $query->where('project_id', $projectId);
        }
        return $query;
    }

    public function scopeWithReconciliation(Builder $query, ?string $reconciliationId): Builder
    {
        if ($reconciliationId) {
            return $query->where('reconciliation_id', $reconciliationId);
        }
        return $query;
    }

    public function scopeUnreconciled(Builder $query): Builder
    {
        return $query->whereNull('reconciliation_id');
    }

    // Accessors & Mutators
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedDebitAmountAttribute(): string
    {
        return number_format($this->debit_amount, 2, ',', '.');
    }

    public function getFormattedCreditAmountAttribute(): string
    {
        return number_format($this->credit_amount, 2, ',', '.');
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return number_format($this->tax_amount, 2, ',', '.');
    }

    public function getTransactionTypeLabelAttribute(): string
    {
        return ucfirst($this->transaction_type);
    }

    public function getAccountCodeAttribute(): string
    {
        return $this->account?->account_code ?? 'N/A';
    }

    public function getAccountNameAttribute(): string
    {
        return $this->account?->account_name ?? 'N/A';
    }

    public function getDepartmentNameAttribute(): ?string
    {
        return $this->department?->name;
    }

    public function getProjectNameAttribute(): ?string
    {
        return $this->project?->name;
    }

    public function getIsReconciledAttribute(): bool
    {
        return !empty($this->reconciliation_id);
    }

    // Business Logic Methods
    public function getEffectiveAmount(): float
    {
        return $this->transaction_type === 'debit' ? $this->debit_amount : $this->credit_amount;
    }

    public function getNetAmount(): float
    {
        return $this->getEffectiveAmount() - $this->tax_amount;
    }

    public function getTaxInclusiveAmount(): float
    {
        return $this->getEffectiveAmount();
    }

    public function getTaxExclusiveAmount(): float
    {
        return $this->getEffectiveAmount() - $this->tax_amount;
    }

    public function calculateTaxAmount(): float
    {
        if ($this->tax_rate && $this->tax_rate > 0) {
            return ($this->getEffectiveAmount() * $this->tax_rate) / 100;
        }
        return 0;
    }

    public function updateTaxAmount(): void
    {
        $this->tax_amount = $this->calculateTaxAmount();
        $this->saveQuietly();
    }

    public function reconcile(string $reconciliationId): bool
    {
        $this->reconciliation_id = $reconciliationId;
        return $this->save();
    }

    public function unreconcile(): bool
    {
        $this->reconciliation_id = null;
        return $this->save();
    }

    public function validateAccount(): bool
    {
        return $this->account && $this->account->is_active;
    }

    public function validateAmount(): bool
    {
        $amount = $this->transaction_type === 'debit' ? $this->debit_amount : $this->credit_amount;
        return $amount > 0;
    }

    public function validateBalance(): bool
    {
        return $this->debit_amount > 0 || $this->credit_amount > 0;
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (!$this->validateAccount()) {
            $errors[] = 'Account is invalid or inactive';
        }

        if (!$this->validateAmount()) {
            $errors[] = 'Amount must be greater than 0';
        }

        if (!$this->validateBalance()) {
            $errors[] = 'Either debit or credit amount must be greater than 0';
        }

        return $errors;
    }
}
