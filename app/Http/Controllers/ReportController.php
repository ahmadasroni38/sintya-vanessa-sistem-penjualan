<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Generate Neraca Lajur (Worksheet)
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

        $data = [];

        foreach ($accounts as $account) {
            $balance = $account->getBalance($validated['start_date'], $validated['end_date']);

            // Classify into financial statement categories
            $neracaDebit = 0;
            $neracaCredit = 0;
            $labaRugiDebit = 0;
            $labaRugiCredit = 0;

            if (in_array($account->account_type, ['asset', 'expense'])) {
                if ($account->account_type == 'asset') {
                    $neracaDebit = $balance >= 0 ? $balance : 0;
                    $neracaCredit = $balance < 0 ? abs($balance) : 0;
                } else { // expense
                    $labaRugiDebit = $balance >= 0 ? $balance : 0;
                    $labaRugiCredit = $balance < 0 ? abs($balance) : 0;
                }
            } else {
                if (in_array($account->account_type, ['liability', 'equity', 'revenue'])) {
                    if ($account->account_type == 'revenue') {
                        $labaRugiCredit = $balance >= 0 ? $balance : 0;
                        $labaRugiDebit = $balance < 0 ? abs($balance) : 0;
                    } else { // liability or equity
                        $neracaCredit = $balance >= 0 ? $balance : 0;
                        $neracaDebit = $balance < 0 ? abs($balance) : 0;
                    }
                }
            }

            $data[] = [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'balance' => $balance,
                'neraca_debit' => $neracaDebit,
                'neraca_credit' => $neracaCredit,
                'laba_rugi_debit' => $labaRugiDebit,
                'laba_rugi_credit' => $labaRugiCredit,
            ];
        }

        return Inertia::render('Dashboard/Reports/NeracaLajur', [
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
