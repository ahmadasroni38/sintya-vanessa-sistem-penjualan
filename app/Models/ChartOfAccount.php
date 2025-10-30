<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'normal_balance',
        'parent_id',
        'level',
        'is_active',
        'description',
        'opening_balance',
        'current_balance',
        'balance_updated_at',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'level' => 'integer',
        'balance_updated_at' => 'datetime',
        'metadata' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'opening_balance' => 0,
        'current_balance' => 0,
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id')
            ->select(['id', 'account_code', 'account_name', 'account_type', 'level']);
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id')
            ->orderBy('account_code');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function journalEntryDetails()
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }

    public function balanceHistories()
    {
        return $this->hasMany(AccountBalanceHistory::class, 'chart_of_account_id')
            ->orderBy('period_start', 'desc');
    }

    public function audits()
    {
        return $this->hasMany(ChartOfAccountAudit::class, 'chart_of_account_id')
            ->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('account_type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLevel(Builder $query, int $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeWithBalance(Builder $query): Builder
    {
        return $query->withSum('journalEntryDetails as total_debit', function ($query) {
                $query->where('transaction_type', 'debit')
                    ->whereHas('journalEntry', function ($q) {
                        $q->where('status', 'posted');
                    });
            })
            ->withSum('journalEntryDetails as total_credit', function ($query) {
                $query->where('transaction_type', 'credit')
                    ->whereHas('journalEntry', function ($q) {
                        $q->where('status', 'posted');
                    });
            });
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('account_code', 'like', "%{$search}%")
              ->orWhere('account_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getFormattedAccountCodeAttribute(): string
    {
        return $this->account_code;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->current_balance, 2, ',', '.');
    }

    public function getAccountTypeLabelAttribute(): string
    {
        return ucfirst($this->account_type);
    }

    public function getNormalBalanceLabelAttribute(): string
    {
        return ucfirst($this->normal_balance);
    }

    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getHasJournalEntriesAttribute(): bool
    {
        return $this->journalEntryDetails()->count() > 0;
    }

    public function getCanDeleteAttribute(): bool
    {
        return !$this->has_children && !$this->has_journal_entries;
    }

    public function getHierarchyPathAttribute(): string
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current->account_code . ' - ' . $current->account_name);
            $current = $current->parent;
        }

        return implode(' > ', $path);
    }

    // Business Logic Methods
    public function calculateBalance(?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = "account-balance:{$this->id}:" . md5(json_encode([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));

        return Cache::remember($cacheKey, 600, function () use ($startDate, $endDate) {
            $query = $this->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                });

            if ($startDate) {
                $query->whereHas('journalEntry', function ($q) use ($startDate) {
                    $q->where('entry_date', '>=', $startDate);
                });
            }

            if ($endDate) {
                $query->whereHas('journalEntry', function ($q) use ($endDate) {
                    $q->where('entry_date', '<=', $endDate);
                });
            }

            $results = $query->selectRaw('
                    transaction_type,
                    SUM(amount) as total
                ')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $debit = $results['debit'] ?? 0;
            $credit = $results['credit'] ?? 0;

            // Calculate balance based on normal balance
            if ($this->normal_balance === 'debit') {
                $balance = $this->opening_balance + $debit - $credit;
            } else {
                $balance = $this->opening_balance + $credit - $debit;
            }

            return [
                'balance' => $balance,
                'debit_total' => $debit,
                'credit_total' => $credit,
                'opening_balance' => $this->opening_balance,
                'normal_balance' => $this->normal_balance
            ];
        });
    }

    public function updateCurrentBalance(): void
    {
        $balanceData = $this->calculateBalance();

        $this->current_balance = $balanceData['balance'];
        $this->balance_updated_at = now();
        $this->saveQuietly(); // Save without triggering events

        // Clear related cache
        Cache::forget("account-balance:{$this->id}");
    }

    public function isDescendantOf(int $parentId): bool
    {
        if ($this->parent_id === null) {
            return false;
        }

        if ($this->parent_id === $parentId) {
            return true;
        }

        $parent = $this->parent;

        if (!$parent) {
            return false;
        }

        return $parent->isDescendantOf($parentId);
    }

    public function wouldCreateCircularReference(int $newParentId): bool
    {
        return $this->isDescendantOf($newParentId);
    }

    public function getAccountTypeCompatibility(string $childType): bool
    {
        $compatibilityMatrix = [
            'asset' => ['asset'],
            'liability' => ['liability'],
            'equity' => ['equity'],
            'revenue' => ['revenue'],
            'expense' => ['expense']
        ];

        return in_array($childType, $compatibilityMatrix[$this->account_type] ?? []);
    }

    public function generateNextChildCode(): string
    {
        $lastChildCode = $this->children()
            ->orderBy('account_code', 'desc')
            ->value('account_code');

        if (!$lastChildCode) {
            $parentCode = explode('-', $this->account_code)[0];
            return "{$parentCode}-1001";
        }

        $parts = explode('-', $lastChildCode);
        $nextCode = (int)$parts[1] + 1;

        return "{$parts[0]}-{$nextCode}";
    }

    public function moveToNewParent(?int $newParentId): bool
    {
        try {
            DB::beginTransaction();

            if ($newParentId && $this->wouldCreateCircularReference($newParentId)) {
                throw new \Exception('Cannot move account: would create circular reference');
            }

            $oldParentId = $this->parent_id;
            $this->parent_id = $newParentId;

            if ($newParentId) {
                $newParent = self::find($newParentId);
                if (!$newParent) {
                    throw new \Exception('New parent account not found');
                }

                if (!$newParent->getAccountTypeCompatibility($this->account_type)) {
                    throw new \Exception('Account type is not compatible with new parent');
                }

                $this->level = $newParent->level + 1;

                if ($this->level > 5) {
                    throw new \Exception('Maximum hierarchy level (5) exceeded');
                }
            } else {
                $this->level = 1;
            }

            $this->save();

            // Update child levels
            $this->updateChildLevels();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateChildLevels(): void
    {
        $children = $this->children;

        foreach ($children as $child) {
            $child->level = $this->level + 1;
            $child->saveQuietly();

            if ($child->children()->count() > 0) {
                $child->updateChildLevels();
            }
        }
    }

    public function getFullHierarchy(): array
    {
        $hierarchy = [];
        $current = $this;

        while ($current) {
            array_unshift($hierarchy, [
                'id' => $current->id,
                'account_code' => $current->account_code,
                'account_name' => $current->account_name,
                'level' => $current->level
            ]);
            $current = $current->parent;
        }

        return $hierarchy;
    }

    // Static Methods
    public static function getAccountTypes(): array
    {
        return [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expense'
        ];
    }

    public static function getNormalBalances(): array
    {
        return [
            'debit' => 'Debit',
            'credit' => 'Credit'
        ];
    }

    public static function validateAccountCode(string $code): bool
    {
        return preg_match('/^[0-9]{1,5}-[0-9]{1,5}$/', $code);
    }

    public static function generateAccountCode(?int $parentId = null): string
    {
        if (!$parentId) {
            // Generate top-level account code
            $lastCode = self::whereNull('parent_id')
                ->orderBy('account_code', 'desc')
                ->value('account_code');

            if (!$lastCode) {
                return '1-1000';
            }

            $parts = explode('-', $lastCode);
            $nextCode = (int)$parts[0] + 1;

            return "{$nextCode}-1000";
        }

        $parent = self::find($parentId);

        if (!$parent) {
            throw new \Exception('Parent account not found');
        }

        return $parent->generateNextChildCode();
    }

    // Events
    protected static function booted()
    {
        static::created(function ($account) {
            $account->updateCurrentBalance();
            Cache::forget('chart-of-accounts-tree');

            // Log audit
            $account->logAudit('created', null, $account->getAttributes());
        });

        static::updated(function ($account) {
            if ($account->wasChanged(['opening_balance', 'parent_id', 'level'])) {
                $account->updateCurrentBalance();
            }
            Cache::forget('chart-of-accounts-tree');

            // Log audit
            $account->logAudit('updated', $account->getOriginal(), $account->getChanges());
        });

        static::deleted(function ($account) {
            Cache::forget('chart-of-accounts-tree');

            // Log audit
            $account->logAudit('deleted', $account->getAttributes(), null);
        });

        static::restored(function ($account) {
            Cache::forget('chart-of-accounts-tree');

            // Log audit
            $account->logAudit('restored', null, $account->getAttributes());
        });
    }

    /**
     * Log audit trail
     */
    protected function logAudit(string $eventType, ?array $oldValues, ?array $newValues): void
    {
        try {
            $user = auth()->user();

            ChartOfAccountAudit::create([
                'chart_of_account_id' => $this->id,
                'event_type' => $eventType,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail audit logging to not disrupt main operation
            \Log::warning('Failed to log audit: ' . $e->getMessage());
        }
    }
}
