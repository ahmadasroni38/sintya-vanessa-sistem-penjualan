<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\AccountBalanceHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChartOfAccountBalanceService
{
    /**
     * Calculate balance for a specific account and period
     *
     * @param ChartOfAccount $account
     * @param array $options Calculation options
     * @return array Balance calculation results
     */
    public function calculateBalance(ChartOfAccount $account, array $options = []): array
    {
        try {
            $startDate = $options['start_date'] ?? null;
            $endDate = $options['end_date'] ?? null;

            // Get journal entry details for the period
            $query = $account->journalEntryDetails()
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
            if ($account->normal_balance === 'debit') {
                $balance = $account->opening_balance + $debit - $credit;
            } else {
                $balance = $account->opening_balance + $credit - $debit;
            }

            return [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'opening_balance' => $account->opening_balance,
                'debit_total' => $debit,
                'credit_total' => $credit,
                'balance' => $balance,
                'normal_balance' => $account->normal_balance,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'calculated_at' => now()->toDateTimeString(),
            ];

        } catch (\Exception $e) {
            Log::error('Balance calculation failed', [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'options' => $options
            ]);

            throw $e;
        }
    }

    /**
     * Calculate balances for multiple accounts
     *
     * @param array $accountIds
     * @param array $options
     * @return array
     */
    public function calculateMultipleBalances(array $accountIds, array $options = []): array
    {
        try {
            $startDate = $options['start_date'] ?? null;
            $endDate = $options['end_date'] ?? null;

            $accounts = ChartOfAccount::whereIn('id', $accountIds)
                ->with(['journalEntryDetails' => function ($query) {
                    $query->whereHas('journalEntry', function ($q) {
                        $q->where('status', 'posted');
                    });
                }])
                ->get();

            $balances = [];

            foreach ($accounts as $account) {
                $balanceData = $this->calculateBalance($account, [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

                $balances[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'debit_total' => $balanceData['debit_total'],
                    'credit_total' => $balanceData['credit_total'],
                    'normal_balance' => $account->normal_balance,
                ];
            }

            return [
                'balances' => $balances,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'calculated_at' => now()->toDateTimeString(),
            ];

        } catch (\Exception $e) {
            Log::error('Multiple balance calculation failed', [
                'account_ids' => $accountIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'options' => $options
            ]);

            throw $e;
        }
    }

    /**
     * Save balance history for an account
     *
     * @param int $accountId
     * @param array $balanceData
     * @return AccountBalanceHistory
     */
    public function saveBalanceHistory(int $accountId, array $balanceData): AccountBalanceHistory
    {
        try {
            return AccountBalanceHistory::create([
                'chart_of_account_id' => $accountId,
                'balance' => $balanceData['balance'],
                'debit_total' => $balanceData['debit_total'],
                'credit_total' => $balanceData['credit_total'],
                'period_start' => $balanceData['period_start'],
                'period_end' => $balanceData['period_end'],
                'calculated_by' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save balance history', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'balance_data' => $balanceData
            ]);

            throw $e;
        }
    }

    /**
     * Get balance history for an account
     *
     * @param int $accountId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBalanceHistory(int $accountId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $query = AccountBalanceHistory::where('chart_of_account_id', $accountId);

            // Apply filters
            if (!empty($filters['period_start'])) {
                $query->where('period_start', '>=', $filters['period_start']);
            }

            if (!empty($filters['period_end'])) {
                $query->where('period_end', '<=', $filters['period_end']);
            }

            if (!empty($filters['calculated_by'])) {
                $query->where('calculated_by', $filters['calculated_by']);
            }

            return $query->orderBy('period_end', 'desc')->get();

        } catch (\Exception $e) {
            Log::error('Failed to get balance history', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $filters
            ]);

            throw $e;
        }
    }

    /**
     * Reconcile account balances
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function reconcileBalances(int $accountId, string $startDate, string $endDate): array
    {
        try {
            $account = ChartOfAccount::find($accountId);

            if (!$account) {
                throw new \Exception('Account not found');
            }

            // Get balance history for the period
            $balanceHistory = $this->getBalanceHistory($accountId, [
                'period_start' => $startDate,
                'period_end' => $endDate
            ]);

            if ($balanceHistory->isEmpty()) {
                return [
                    'reconciled' => false,
                    'message' => 'No balance history found for the specified period',
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                ];
            }

            // Get the latest balance history entry
            $latestBalance = $balanceHistory->first();

            if (!$latestBalance) {
                return [
                    'reconciled' => false,
                    'message' => 'No balance history found for the specified period',
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                ];
            }

            // Get current account balance
            $currentBalance = $account->current_balance;

            // Calculate expected balance from history
            $expectedBalance = $latestBalance->opening_balance +
                               $latestBalance->debit_total -
                               $latestBalance->credit_total;

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
                'latest_history' => $latestBalance,
            ];

        } catch (\Exception $e) {
            Log::error('Balance reconciliation failed', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Get balance trends for an account
     *
     * @param int $accountId
     * @param int $months Number of months to analyze
     * @return array
     */
    public function getBalanceTrends(int $accountId, int $months = 12): array
    {
        try {
            $endDate = now()->endOfMonth();
            $startDate = now()->subMonths($months)->startOfMonth();

            $balanceHistory = $this->getBalanceHistory($accountId, [
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
            ]);

            $trends = [];
            $monthlyBalances = [];

            // Group by month
            $monthlyData = $balanceHistory->groupBy(function ($item) {
                return $item->period_end->format('Y-m');
            })->map(function ($group, $month) {
                $latestBalance = $group->first();

                return [
                    'month' => $month,
                    'balance' => $latestBalance ? $latestBalance->balance : 0,
                    'period_start' => $group->first()->period_start,
                    'period_end' => $group->first()->period_end,
                ];
            });

            foreach ($monthlyData as $data) {
                $monthlyBalances[] = [
                    'month' => $data['month'],
                    'balance' => $data['balance'],
                    'period_start' => $data['period_start'],
                    'period_end' => $data['period_end'],
                ];
            }

            // Calculate trends
            for ($i = 1; $i < count($monthlyBalances); $i++) {
                if ($i > 1) {
                    $prevBalance = $monthlyBalances[$i - 2]['balance'] ?? 0;
                    $currentBalance = $monthlyBalances[$i - 1]['balance'] ?? 0;
                    $change = $currentBalance - $prevBalance;
                    $changePercent = $prevBalance != 0 ? ($change / $prevBalance) * 100 : 0;

                    $trends[] = [
                        'month' => $monthlyBalances[$i]['month'],
                        'balance' => $monthlyBalances[$i]['balance'],
                        'change_from_previous' => $change,
                        'change_percent' => $changePercent,
                        'trend' => $change > 0 ? 'increasing' : ($change < 0 ? 'decreasing' : 'stable'),
                    ];
                }
            }

            return [
                'trends' => $trends,
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
                'months_analyzed' => $months,
            ];

        } catch (\Exception $e) {
            Log::error('Balance trend analysis failed', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'months' => $months,
            ]);

            throw $e;
        }
    }

    /**
     * Generate balance report for multiple accounts
     *
     * @param array $accountIds
     * @param array $options
     * @return array
     */
    public function generateBalanceReport(array $accountIds, array $options = []): array
    {
        try {
            $startDate = $options['start_date'] ?? null;
            $endDate = $options['end_date'] ?? null;
            $format = $options['format'] ?? 'array';

            $accounts = ChartOfAccount::whereIn('id', $accountIds)->get();
            $report = [];

            foreach ($accounts as $account) {
                $balanceData = $this->calculateBalance($account, [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

                $report[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'normal_balance' => $account->normal_balance,
                    'opening_balance' => $account->opening_balance,
                    'current_balance' => $balanceData['balance'],
                    'debit_total' => $balanceData['debit_total'],
                    'credit_total' => $balanceData['credit_total'],
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                ];
            }

            // Calculate totals
            $totals = [
                'total_opening_balance' => $report->sum('opening_balance'),
                'total_current_balance' => $report->sum('current_balance'),
                'total_debit_total' => $report->sum('debit_total'),
                'total_credit_total' => $report->sum('credit_total'),
            ];

            return [
                'report' => $report,
                'totals' => $totals,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'generated_at' => now()->toDateTimeString(),
                'format' => $format,
            ];

        } catch (\Exception $e) {
            Log::error('Balance report generation failed', [
                'account_ids' => $accountIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'options' => $options
            ]);

            throw $e;
        }
    }
}
