<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Generate Neraca Lajur (Worksheet)
     *
     * Neraca Lajur format:
     * 1. Saldo Awal (Opening Balance)
     * 2. Penyesuaian Debit/Kredit (Adjustments from adjustment entries)
     * 3. Saldo Disesuaikan (Adjusted Balance)
     * 4. Neraca Debit/Kredit (Balance Sheet items)
     * 5. Laba Rugi Debit/Kredit (Income Statement items)
     */
    public function neracaLajur(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $accounts = ChartOfAccount::active()
            ->orderBy('account_code')
            ->get();

        Log::info('NeracaLajur: Total accounts found', ['count' => $accounts->count()]);

        $data = [];

        foreach ($accounts as $account) {
            // Get opening balance (balance before period starts)
            // We need to get all transactions before start_date
            $openingBalanceData = $account->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) use ($validated) {
                    $q->where('status', 'posted')
                      ->where('entry_date', '<', $validated['start_date']);
                })
                ->selectRaw('
                    transaction_type,
                    SUM(amount) as total
                ')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $openingDebit = $openingBalanceData['debit'] ?? 0;
            $openingCredit = $openingBalanceData['credit'] ?? 0;

            // Calculate opening balance based on normal balance
            if ($account->normal_balance === 'debit') {
                $openingBalance = $account->opening_balance + $openingDebit - $openingCredit;
            } else {
                $openingBalance = $account->opening_balance + $openingCredit - $openingDebit;
            }

            // Get ALL transactions within date range
            $allPeriodData = $account->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) use ($validated) {
                    $q->where('status', 'posted')
                      ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                })
                ->selectRaw('
                    transaction_type,
                    SUM(amount) as total
                ')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $allPeriodDebit = $allPeriodData['debit'] ?? 0;
            $allPeriodCredit = $allPeriodData['credit'] ?? 0;

            // Get adjustment entries (adjustment type) within period
            $adjustmentQuery = $account->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) use ($validated) {
                    $q->where('status', 'posted')
                      ->where('entry_type', 'adjustment')
                      ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                });

            $adjustmentResults = $adjustmentQuery->selectRaw('
                    transaction_type,
                    SUM(amount) as total
                ')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $adjustmentDebit = $adjustmentResults['debit'] ?? 0;
            $adjustmentCredit = $adjustmentResults['credit'] ?? 0;

            // Calculate regular transactions (all transactions - adjustments)
            $regularDebit = $allPeriodDebit - $adjustmentDebit;
            $regularCredit = $allPeriodCredit - $adjustmentCredit;

            // Calculate adjusted balance (opening + regular transactions + adjustments)
            if ($account->normal_balance === 'debit') {
                $adjustedBalance = $openingBalance + $regularDebit - $regularCredit + $adjustmentDebit - $adjustmentCredit;
            } else {
                $adjustedBalance = $openingBalance + $regularCredit - $regularDebit + $adjustmentCredit - $adjustmentDebit;
            }

            // Get final balance for reference
            $balanceData = $account->calculateBalance($validated['start_date'], $validated['end_date']);
            $balance = $balanceData['balance'];

            // Skip accounts with zero balance (both opening and adjusted balance)
            // Use abs() to handle floating point comparison
            if (abs($adjustedBalance) < 0.01 && abs($openingBalance) < 0.01) {
                continue;
            }

            // Classify into Neraca (Balance Sheet) or Laba Rugi (Income Statement)
            // Based on normal_balance for accurate classification
            $saldoDisesuaikanDebit = 0;
            $saldoDisesuaikanCredit = 0;
            $neracaDebit = 0;
            $neracaCredit = 0;
            $labaRugiDebit = 0;
            $labaRugiCredit = 0;

            // Determine Saldo Disesuaikan column based on normal balance
            if ($adjustedBalance > 0) {
                // Positive balance (follows normal balance)
                if ($account->normal_balance === 'debit') {
                    $saldoDisesuaikanDebit = $adjustedBalance;
                } else {
                    $saldoDisesuaikanCredit = $adjustedBalance;
                }
            } else {
                // Negative balance (opposite of normal balance)
                if ($account->normal_balance === 'debit') {
                    $saldoDisesuaikanCredit = abs($adjustedBalance);
                } else {
                    $saldoDisesuaikanDebit = abs($adjustedBalance);
                }
            }

            // Classify to Neraca or Laba Rugi based on account type
            if (in_array($account->account_type, ['asset', 'liability', 'equity'])) {
                // Goes to Neraca (Balance Sheet)
                $neracaDebit = $saldoDisesuaikanDebit;
                $neracaCredit = $saldoDisesuaikanCredit;
            } else {
                // Goes to Laba Rugi (Income Statement) - revenue & expense
                $labaRugiDebit = $saldoDisesuaikanDebit;
                $labaRugiCredit = $saldoDisesuaikanCredit;
            }

            $data[] = [
                'id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'normal_balance' => $account->normal_balance,

                // Column 1: Saldo Awal
                'opening_balance' => $openingBalance,

                // Column 2-3: Penyesuaian (Adjustments)
                'adjustment_debit' => $adjustmentDebit,
                'adjustment_credit' => $adjustmentCredit,

                // Column 4-5: Saldo Disesuaikan (Adjusted Balance)
                'adjusted_debit' => $saldoDisesuaikanDebit,
                'adjusted_credit' => $saldoDisesuaikanCredit,

                // Column 6-7: Neraca (Balance Sheet)
                'neraca_debit' => $neracaDebit,
                'neraca_credit' => $neracaCredit,

                // Column 8-9: Laba Rugi (Income Statement)
                'laba_rugi_debit' => $labaRugiDebit,
                'laba_rugi_credit' => $labaRugiCredit,

                // For reference - include regular transactions (non-adjustment)
                'regular_debit' => $regularDebit,
                'regular_credit' => $regularCredit,
                'balance' => $balance,
                'adjusted_balance' => $adjustedBalance,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
        ]);
    }

    /**
     * Generate Neraca (Balance Sheet)
     */
    public function neraca(Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
        ]);

        $assets = ChartOfAccount::active()
            ->where('account_type', 'asset')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $account->getBalance(null, $validated['end_date']),
                    'level' => $account->level,
                ];
            });

        $liabilities = ChartOfAccount::active()
            ->where('account_type', 'liability')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $account->getBalance(null, $validated['end_date']),
                    'level' => $account->level,
                ];
            });

        $equity = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $account->getBalance(null, $validated['end_date']),
                    'level' => $account->level,
                ];
            });

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        return Inertia::render('Dashboard/Reports/Neraca', [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totals' => [
                'assets' => $totalAssets,
                'liabilities' => $totalLiabilities,
                'equity' => $totalEquity,
                'liabilities_equity' => $totalLiabilities + $totalEquity,
            ],
            'period' => [
                'end_date' => $validated['end_date'],
            ],
        ]);
    }

    /**
     * Generate Laba Rugi (Income Statement)
     */
    public function labaRugi(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $account->getBalance($validated['start_date'], $validated['end_date']),
                    'level' => $account->level,
                ];
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $account->getBalance($validated['start_date'], $validated['end_date']),
                    'level' => $account->level,
                ];
            });

        $totalRevenue = $revenues->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        return Inertia::render('Dashboard/Reports/LabaRugi', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'totals' => [
                'revenue' => $totalRevenue,
                'expenses' => $totalExpenses,
                'net_income' => $netIncome,
            ],
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
        ]);
    }

    /**
     * Generate Perubahan Modal (Statement of Changes in Equity)
     */
    public function perubahanModal(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get modal pemilik (owner's equity)
        $modalPemilik = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->where('account_name', 'like', '%Modal Pemilik%')
            ->first();

        // Get prive (drawings)
        $prive = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->where('account_name', 'like', '%Prive%')
            ->first();

        // Get laba ditahan (retained earnings)
        $labaDitahan = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->where('account_name', 'like', '%Laba Ditahan%')
            ->first();

        // Calculate net income from income statement
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($validated) {
                return $account->getBalance($validated['start_date'], $validated['end_date']);
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($validated) {
                return $account->getBalance($validated['start_date'], $validated['end_date']);
            });

        $netIncome = $revenues - $expenses;

        $beginningBalance = $modalPemilik ? $modalPemilik->opening_balance : 0;
        $priveAmount = $prive ? $prive->getBalance($validated['start_date'], $validated['end_date']) : 0;
        $endingBalance = $beginningBalance + $netIncome - $priveAmount;

        return Inertia::render('Dashboard/Reports/PerubahanModal', [
            'beginning_balance' => $beginningBalance,
            'net_income' => $netIncome,
            'prive' => $priveAmount,
            'ending_balance' => $endingBalance,
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
        ]);
    }

    /**
     * Generate Arus Kas (Cash Flow Statement)
     */
    public function arusKas(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get all cash and bank accounts
        $cashAccounts = ChartOfAccount::active()
            ->where('account_type', 'asset')
            ->where(function ($q) {
                $q->where('account_name', 'like', '%Kas%')
                    ->orWhere('account_name', 'like', '%Bank%');
            })
            ->get();

        $cashFlow = [];
        $beginningBalance = 0;
        $endingBalance = 0;

        foreach ($cashAccounts as $account) {
            $beginning = $account->opening_balance;
            $ending = $account->getBalance(null, $validated['end_date']);
            $movement = $account->getBalance($validated['start_date'], $validated['end_date']);

            $cashFlow[] = [
                'code' => $account->account_code,
                'name' => $account->account_name,
                'beginning_balance' => $beginning,
                'movement' => $movement,
                'ending_balance' => $ending,
            ];

            $beginningBalance += $beginning;
            $endingBalance += $ending;
        }

        // Calculate net income for operating activities
        $netIncome = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($validated) {
                return $account->getBalance($validated['start_date'], $validated['end_date']);
            }) - ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($validated) {
                return $account->getBalance($validated['start_date'], $validated['end_date']);
            });

        return Inertia::render('Dashboard/Reports/ArusKas', [
            'cash_accounts' => $cashFlow,
            'beginning_balance' => $beginningBalance,
            'ending_balance' => $endingBalance,
            'net_change' => $endingBalance - $beginningBalance,
            'net_income' => $netIncome,
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
        ]);
    }
}
