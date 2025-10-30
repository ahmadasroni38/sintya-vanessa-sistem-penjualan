<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccountBalanceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'chart_of_account_id',
        'balance',
        'debit_total',
        'credit_total',
        'period_start',
        'period_end',
        'calculated_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'debit_total' => 'decimal:2',
        'credit_total' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'calculated_by' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'period_start',
        'period_end',
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    // Scopes
    public function scopeForPeriod(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->where('period_start', '>=', $startDate)
                    ->where('period_end', '<=', $endDate);
    }

    public function scopeByAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('chart_of_account_id', $accountId);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('period_end', 'desc');
    }

    public function scopeByCalculatedBy(Builder $query, string $userId): Builder
    {
        return $query->where('calculated_by', $userId);
    }

    // Accessors
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2, ',', '.');
    }

    public function getFormattedDebitTotalAttribute(): string
    {
        return number_format($this->debit_total, 2, ',', '.');
    }

    public function getFormattedCreditTotalAttribute(): string
    {
        return number_format($this->credit_total, 2, ',', '.');
    }

    public function getPeriodLabelAttribute(): string
    {
        $start = $this->period_start->format('M Y');
        $end = $this->period_end->format('M Y');

        return $start === $end ? $start : $start . ' - ' . $end;
    }

    public function getDaysInPeriodAttribute(): int
    {
        return $this->period_start->diffInDays($this->period_end);
    }

    // Static Methods
    public static function getBalanceForPeriod(int $accountId, string $startDate, string $endDate): ?self
    {
        return self::byAccount($accountId)
            ->forPeriod($startDate, $endDate)
            ->latest()
            ->first();
    }

    public static function getBalanceTrend(int $accountId, int $months = 12): array
    {
        $endDate = now()->subMonths($months);
        $startDate = $endDate->copy()->subMonths($months);

        $histories = self::byAccount($accountId)
            ->forPeriod($startDate, $endDate)
            ->orderBy('period_end')
            ->get();

        return $histories->map(function ($history) {
            return [
                'period' => $history->period_label,
                'balance' => $history->formatted_balance,
                'debit_total' => $history->formatted_debit_total,
                'credit_total' => $history->formatted_credit_total,
                'period_start' => $history->period_start->format('Y-m-d'),
                'period_end' => $history->period_end->format('Y-m-d'),
            ];
        })->toArray();
    }

    public static function calculateMonthlyBalances(int $accountId, int $year): array
    {
        $startDate = now()->startOfYear()->copy()->subYear(1);
        $endDate = now()->endOfYear();

        $histories = self::byAccount($accountId)
            ->whereYear('period_end', $year)
            ->orderBy('period_end')
            ->get();

        $monthlyBalances = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStart = $startDate->copy()->addMonths($month - 1);
            $monthEnd = $startDate->copy()->addMonths($month)->endOfMonth();

            $monthHistory = $histories->firstWhere('period_end', '>=', $monthStart)
                                        ->where('period_end', '<=', $monthEnd);

            if ($monthHistory) {
                $monthlyBalances[] = [
                    'month' => $monthStart->format('F Y'),
                    'balance' => $monthHistory->formatted_balance,
                    'debit_total' => $monthHistory->formatted_debit_total,
                    'credit_total' => $monthHistory->formatted_credit_total,
                ];
            } else {
                // Get previous month balance
                $previousBalance = end($monthlyBalances) ? end($monthlyBalances)['balance'] : 0;

                $monthlyBalances[] = [
                    'month' => $monthStart->format('F Y'),
                    'balance' => $previousBalance,
                    'debit_total' => 0,
                    'credit_total' => 0,
                ];
            }
        }

        return $monthlyBalances;
    }

    public static function reconcileBalances(int $accountId, string $startDate, string $endDate): array
    {
        $history = self::getBalanceForPeriod($accountId, $startDate, $endDate);

        if (!$history) {
            return [
                'reconciled' => false,
                'message' => 'No balance history found for the specified period',
                'period_start' => $startDate,
                'period_end' => $endDate,
            ];
        }

        // Get current account balance
        $currentAccount = ChartOfAccount::find($accountId);
        $currentBalance = $currentAccount ? $currentAccount->current_balance : 0;

        // Calculate expected balance from history
        $expectedBalance = $history->opening_balance +
                           $history->debit_total -
                           $history->credit_total;

        $difference = abs($currentBalance - $expectedBalance);
        $isReconciled = $difference < 0.01; // Allow small rounding differences

        return [
            'reconciled' => $isReconciled,
            'current_balance' => $currentBalance,
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'difference_percentage' => $expectedBalance > 0 ? ($difference / $expectedBalance) * 100 : 0,
            'message' => $isReconciled
                ? 'Balances are reconciled'
                : sprintf(
                    'Balance difference of %.2f detected. Current: %.2f, Expected: %.2f',
                    $currentBalance,
                    $expectedBalance,
                    $difference
                ),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'history' => $history,
        ];
    }
}
