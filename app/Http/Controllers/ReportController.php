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
                $balanceData = $account->calculateBalance(null, $validated['end_date']);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                    'normal_balance' => $account->normal_balance,
                ];
            });

        $liabilities = ChartOfAccount::active()
            ->where('account_type', 'liability')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                $balanceData = $account->calculateBalance(null, $validated['end_date']);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                    'normal_balance' => $account->normal_balance,
                ];
            });

        $equity = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                $balanceData = $account->calculateBalance(null, $validated['end_date']);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                    'normal_balance' => $account->normal_balance,
                ];
            });

        // Calculate net income from revenue and expense accounts
        // Get all revenue accounts balance
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($validated) {
                $balanceData = $account->calculateBalance(null, $validated['end_date']);
                return $balanceData['balance'];
            });

        // Get all expense accounts balance
        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($validated) {
                $balanceData = $account->calculateBalance(null, $validated['end_date']);
                return $balanceData['balance'];
            });

        // Net income = Revenue - Expenses
        $netIncome = $revenues - $expenses;

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        return response()->json([
            'success' => true,
            'data' => [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'net_income' => $netIncome,
                'totals' => [
                    'assets' => $totalAssets,
                    'liabilities' => $totalLiabilities,
                    'equity' => $totalEquity,
                    'liabilities_equity' => $totalLiabilities + $totalEquity + $netIncome,
                ],
                'period' => [
                    'end_date' => $validated['end_date'],
                ],
            ],
        ]);
    }

    /**
     * Export Neraca (Balance Sheet)
     */
    public function exportNeraca(Request $request)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
            'format' => 'required|in:pdf,xlsx',
        ]);

        $endDate = $validated['end_date'];
        $format = $validated['format'];

        $assets = ChartOfAccount::active()
            ->where('account_type', 'asset')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($endDate) {
                $balanceData = $account->calculateBalance(null, $endDate);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                ];
            });

        $liabilities = ChartOfAccount::active()
            ->where('account_type', 'liability')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($endDate) {
                $balanceData = $account->calculateBalance(null, $endDate);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                ];
            });

        $equity = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($endDate) {
                $balanceData = $account->calculateBalance(null, $endDate);
                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balanceData['balance'],
                    'level' => $account->level,
                ];
            });

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        $data = [
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
                'end_date' => $endDate,
            ],
        ];

        if ($format === 'pdf') {
            // Generate PDF
            $pdf = \PDF::loadView('reports.neraca', $data);
            $filename = 'neraca_' . $endDate . '.pdf';
            return $pdf->download($filename);
        } else {
            // Generate Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'NERACA');
            $sheet->setCellValue('A2', 'Per Tanggal: ' . $endDate);
            $sheet->setCellValue('A3', '');

            // Assets section
            $row = 4;
            $sheet->setCellValue('A' . $row, 'AKTIVA');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Nama Akun');
            $sheet->setCellValue('C' . $row, 'Saldo');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($assets as $asset) {
                $sheet->setCellValue('A' . $row, $asset['code']);
                $sheet->setCellValue('B' . $row, $asset['name']);
                $sheet->setCellValue('C' . $row, $asset['balance']);
                $row++;
            }

            $sheet->setCellValue('B' . $row, 'JUMLAH AKTIVA');
            $sheet->setCellValue('C' . $row, $totalAssets);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row += 2;

            // Liabilities & Equity section
            $sheet->setCellValue('A' . $row, 'KEWAJIBAN DAN EKUITAS');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Nama Akun');
            $sheet->setCellValue('C' . $row, 'Saldo');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($liabilities as $liability) {
                $sheet->setCellValue('A' . $row, $liability['code']);
                $sheet->setCellValue('B' . $row, $liability['name']);
                $sheet->setCellValue('C' . $row, $liability['balance']);
                $row++;
            }

            foreach ($equity as $eq) {
                $sheet->setCellValue('A' . $row, $eq['code']);
                $sheet->setCellValue('B' . $row, $eq['name']);
                $sheet->setCellValue('C' . $row, $eq['balance']);
                $row++;
            }

            $sheet->setCellValue('B' . $row, 'JUMLAH KEWAJIBAN DAN EKUITAS');
            $sheet->setCellValue('C' . $row, $totalLiabilities + $totalEquity);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

            // Auto-size columns
            foreach (['A', 'B', 'C'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'neraca_' . $endDate . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
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

        // For Income Statement, we only get transactions within the period
        // Revenue and Expense accounts don't carry forward opening balance
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                // Get only period transactions (no opening balance)
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;

                // For revenue accounts (credit normal balance), balance = credit - debit
                $balance = $credit - $debit;

                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                    'level' => $account->level,
                ];
            })
            ->filter(function ($account) {
                // Only include accounts with transactions
                return abs($account['balance']) >= 0.01;
            })
            ->values();

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($validated) {
                // Get only period transactions (no opening balance)
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;

                // For expense accounts (debit normal balance), balance = debit - credit
                $balance = $debit - $credit;

                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                    'level' => $account->level,
                ];
            })
            ->filter(function ($account) {
                // Only include accounts with transactions
                return abs($account['balance']) >= 0.01;
            })
            ->values();

        $totalRevenue = $revenues->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        return response()->json([
            'success' => true,
            'data' => [
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
            ],
        ]);
    }

    /**
     * Export Laba Rugi (Income Statement)
     */
    public function exportLabaRugi(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,xlsx',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $format = $validated['format'];

        // Get revenues
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;
                $balance = $credit - $debit;

                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                    'level' => $account->level,
                ];
            })
            ->filter(function ($account) {
                return abs($account['balance']) >= 0.01;
            })
            ->values();

        // Get expenses
        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;
                $balance = $debit - $credit;

                return [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                    'level' => $account->level,
                ];
            })
            ->filter(function ($account) {
                return abs($account['balance']) >= 0.01;
            })
            ->values();

        $totalRevenue = $revenues->sum('balance');
        $totalExpenses = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpenses;

        $data = [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'totals' => [
                'revenue' => $totalRevenue,
                'expenses' => $totalExpenses,
                'net_income' => $netIncome,
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        if ($format === 'pdf') {
            // Generate PDF
            $pdf = \PDF::loadView('reports.laba-rugi', $data);
            $filename = 'laba_rugi_' . $startDate . '_' . $endDate . '.pdf';
            return $pdf->download($filename);
        } else {
            // Generate Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'LAPORAN LABA RUGI');
            $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
            $sheet->setCellValue('A3', '');

            // Revenue section
            $row = 4;
            $sheet->setCellValue('A' . $row, 'PENDAPATAN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Nama Akun');
            $sheet->setCellValue('C' . $row, 'Jumlah');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($revenues as $revenue) {
                $sheet->setCellValue('A' . $row, $revenue['code']);
                $sheet->setCellValue('B' . $row, $revenue['name']);
                $sheet->setCellValue('C' . $row, $revenue['balance']);
                $row++;
            }

            $sheet->setCellValue('B' . $row, 'JUMLAH PENDAPATAN');
            $sheet->setCellValue('C' . $row, $totalRevenue);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row += 2;

            // Expenses section
            $sheet->setCellValue('A' . $row, 'BEBAN');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Nama Akun');
            $sheet->setCellValue('C' . $row, 'Jumlah');
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($expenses as $expense) {
                $sheet->setCellValue('A' . $row, $expense['code']);
                $sheet->setCellValue('B' . $row, $expense['name']);
                $sheet->setCellValue('C' . $row, $expense['balance']);
                $row++;
            }

            $sheet->setCellValue('B' . $row, 'JUMLAH BEBAN');
            $sheet->setCellValue('C' . $row, $totalExpenses);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
            $row += 2;

            // Net Income
            $sheet->setCellValue('B' . $row, 'LABA (RUGI) BERSIH');
            $sheet->setCellValue('C' . $row, $netIncome);
            $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

            // Auto-size columns
            foreach (['A', 'B', 'C'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'laba_rugi_' . $startDate . '_' . $endDate . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
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

        // Calculate net income from income statement (period transactions only)
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($validated) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;
                return $credit - $debit; // Revenue balance
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($validated) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('
                        transaction_type,
                        SUM(amount) as total
                    ')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                $debit = $results['debit'] ?? 0;
                $credit = $results['credit'] ?? 0;
                return $debit - $credit; // Expense balance
            });

        $netIncome = $revenues - $expenses;

        // Get all equity accounts balance at the beginning of period
        $equityAccounts = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->get();

        $beginningBalance = 0;
        $priveAmount = 0;
        $additionalInvestment = 0;

        foreach ($equityAccounts as $account) {
            // Get balance before period starts (opening balance + transactions before start_date)
            $balanceData = $account->calculateBalance(null, date('Y-m-d', strtotime($validated['start_date'] . ' -1 day')));
            $accountBeginningBalance = $balanceData['balance'];

            // Get transactions during period
            $periodQuery = $account->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) use ($validated) {
                    $q->where('status', 'posted')
                      ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                });

            $periodResults = $periodQuery->selectRaw('
                    transaction_type,
                    SUM(amount) as total
                ')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $periodDebit = $periodResults['debit'] ?? 0;
            $periodCredit = $periodResults['credit'] ?? 0;
            $periodChange = $periodCredit - $periodDebit; // Equity normal balance is credit

            // Classify based on account name
            if (stripos($account->account_name, 'Prive') !== false ||
                stripos($account->account_name, 'Penarikan') !== false ||
                stripos($account->account_name, 'Drawing') !== false) {
                // Prive account - debit increases prive (reduces equity)
                $priveAmount += ($periodDebit - $periodCredit);
            } elseif (stripos($account->account_name, 'Modal') !== false ||
                      stripos($account->account_name, 'Capital') !== false) {
                // Capital account
                $beginningBalance += $accountBeginningBalance;
                // Additional investment during period (credit increases capital)
                if ($periodChange > 0) {
                    $additionalInvestment += $periodChange;
                }
            } elseif (stripos($account->account_name, 'Laba Ditahan') !== false ||
                      stripos($account->account_name, 'Retained Earnings') !== false) {
                // Retained earnings
                $beginningBalance += $accountBeginningBalance;
            } else {
                // Other equity accounts
                $beginningBalance += $accountBeginningBalance;
            }
        }

        // Calculate ending balance
        // Beginning Balance + Net Income + Additional Investment - Prive = Ending Balance
        $endingBalance = $beginningBalance + $netIncome + $additionalInvestment - $priveAmount;

        return response()->json([
            'success' => true,
            'data' => [
                'beginning_balance' => $beginningBalance,
                'net_income' => $netIncome,
                'additional_investment' => $additionalInvestment,
                'prive' => $priveAmount,
                'ending_balance' => $endingBalance,
                'period' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
            ],
        ]);
    }

    /**
     * Export Perubahan Modal (Statement of Changes in Equity)
     */
    public function exportPerubahanModal(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,xlsx',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $format = $validated['format'];

        // Calculate net income
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['credit'] ?? 0) - ($results['debit'] ?? 0);
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['debit'] ?? 0) - ($results['credit'] ?? 0);
            });

        $netIncome = $revenues - $expenses;

        // Get equity accounts
        $equityAccounts = ChartOfAccount::active()
            ->where('account_type', 'equity')
            ->get();

        $beginningBalance = 0;
        $priveAmount = 0;
        $additionalInvestment = 0;

        foreach ($equityAccounts as $account) {
            $balanceData = $account->calculateBalance(null, date('Y-m-d', strtotime($startDate . ' -1 day')));
            $accountBeginningBalance = $balanceData['balance'];

            $periodQuery = $account->journalEntryDetails()
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'posted')
                      ->whereBetween('entry_date', [$startDate, $endDate]);
                });

            $periodResults = $periodQuery->selectRaw('transaction_type, SUM(amount) as total')
                ->groupBy('transaction_type')
                ->pluck('total', 'transaction_type');

            $periodDebit = $periodResults['debit'] ?? 0;
            $periodCredit = $periodResults['credit'] ?? 0;
            $periodChange = $periodCredit - $periodDebit;

            if (stripos($account->account_name, 'Prive') !== false ||
                stripos($account->account_name, 'Penarikan') !== false ||
                stripos($account->account_name, 'Drawing') !== false) {
                $priveAmount += ($periodDebit - $periodCredit);
            } elseif (stripos($account->account_name, 'Modal') !== false ||
                      stripos($account->account_name, 'Capital') !== false) {
                $beginningBalance += $accountBeginningBalance;
                if ($periodChange > 0) {
                    $additionalInvestment += $periodChange;
                }
            } elseif (stripos($account->account_name, 'Laba Ditahan') !== false ||
                      stripos($account->account_name, 'Retained Earnings') !== false) {
                $beginningBalance += $accountBeginningBalance;
            } else {
                $beginningBalance += $accountBeginningBalance;
            }
        }

        $endingBalance = $beginningBalance + $netIncome + $additionalInvestment - $priveAmount;

        $data = [
            'beginning_balance' => $beginningBalance,
            'net_income' => $netIncome,
            'additional_investment' => $additionalInvestment,
            'prive' => $priveAmount,
            'ending_balance' => $endingBalance,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        if ($format === 'pdf') {
            // Generate PDF
            $pdf = \PDF::loadView('reports.perubahan-modal', $data);
            $filename = 'perubahan_modal_' . $startDate . '_' . $endDate . '.pdf';
            return $pdf->download($filename);
        } else {
            // Generate Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'LAPORAN PERUBAHAN MODAL');
            $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
            $sheet->setCellValue('A3', '');

            // Content
            $row = 4;
            $sheet->setCellValue('A' . $row, 'KETERANGAN');
            $sheet->setCellValue('B' . $row, 'JUMLAH (Rp)');
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Saldo Awal Modal');
            $sheet->setCellValue('B' . $row, $beginningBalance);
            $row++;

            $sheet->setCellValue('A' . $row, 'Laba Bersih Periode Ini');
            $sheet->setCellValue('B' . $row, $netIncome);
            $row++;

            if ($additionalInvestment > 0) {
                $sheet->setCellValue('A' . $row, 'Tambahan Investasi Pemilik');
                $sheet->setCellValue('B' . $row, $additionalInvestment);
                $row++;
            }

            $sheet->setCellValue('A' . $row, 'Prive (Penarikan oleh Pemilik)');
            $sheet->setCellValue('B' . $row, -$priveAmount);
            $row++;

            $row++;
            $sheet->setCellValue('A' . $row, 'Saldo Akhir Modal');
            $sheet->setCellValue('B' . $row, $endingBalance);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);

            // Auto-size columns
            foreach (['A', 'B'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'perubahan_modal_' . $startDate . '_' . $endDate . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
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

        // Calculate net income from income statement (period transactions only)
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($validated) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['credit'] ?? 0) - ($results['debit'] ?? 0);
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($validated) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($validated) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$validated['start_date'], $validated['end_date']]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['debit'] ?? 0) - ($results['credit'] ?? 0);
            });

        $netIncome = $revenues - $expenses;

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
            // Get balance before period starts
            $beginningBalanceData = $account->calculateBalance(null, date('Y-m-d', strtotime($validated['start_date'] . ' -1 day')));
            $beginning = $beginningBalanceData['balance'];

            // Get balance at end of period
            $endingBalanceData = $account->calculateBalance(null, $validated['end_date']);
            $ending = $endingBalanceData['balance'];

            // Movement during period = ending - beginning
            $movement = $ending - $beginning;

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

        $netChange = $endingBalance - $beginningBalance;

        return response()->json([
            'success' => true,
            'data' => [
                'cash_accounts' => $cashFlow,
                'beginning_balance' => $beginningBalance,
                'ending_balance' => $endingBalance,
                'net_change' => $netChange,
                'net_income' => $netIncome,
                'period' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
            ],
        ]);
    }

    /**
     * Export Arus Kas (Cash Flow Statement)
     */
    public function exportArusKas(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,xlsx',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $format = $validated['format'];

        // Calculate net income
        $revenues = ChartOfAccount::active()
            ->where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['credit'] ?? 0) - ($results['debit'] ?? 0);
            });

        $expenses = ChartOfAccount::active()
            ->where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                $query = $account->journalEntryDetails()
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->where('status', 'posted')
                          ->whereBetween('entry_date', [$startDate, $endDate]);
                    });

                $results = $query->selectRaw('transaction_type, SUM(amount) as total')
                    ->groupBy('transaction_type')
                    ->pluck('total', 'transaction_type');

                return ($results['debit'] ?? 0) - ($results['credit'] ?? 0);
            });

        $netIncome = $revenues - $expenses;

        // Get cash accounts
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
            $beginningBalanceData = $account->calculateBalance(null, date('Y-m-d', strtotime($startDate . ' -1 day')));
            $beginning = $beginningBalanceData['balance'];

            $endingBalanceData = $account->calculateBalance(null, $endDate);
            $ending = $endingBalanceData['balance'];

            $movement = $ending - $beginning;

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

        $netChange = $endingBalance - $beginningBalance;

        $data = [
            'cash_accounts' => $cashFlow,
            'beginning_balance' => $beginningBalance,
            'ending_balance' => $endingBalance,
            'net_change' => $netChange,
            'net_income' => $netIncome,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        if ($format === 'pdf') {
            // Generate PDF
            $pdf = \PDF::loadView('reports.arus-kas', $data);
            $filename = 'arus_kas_' . $startDate . '_' . $endDate . '.pdf';
            return $pdf->download($filename);
        } else {
            // Generate Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'LAPORAN ARUS KAS');
            $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
            $sheet->setCellValue('A3', '');

            // Summary section
            $row = 4;
            $sheet->setCellValue('A' . $row, 'Saldo Awal Kas');
            $sheet->setCellValue('B' . $row, $beginningBalance);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Laba Bersih');
            $sheet->setCellValue('B' . $row, $netIncome);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kenaikan/Penurunan Kas Bersih');
            $sheet->setCellValue('B' . $row, $netChange);
            $row++;

            $sheet->setCellValue('A' . $row, 'Saldo Akhir Kas');
            $sheet->setCellValue('B' . $row, $endingBalance);
            $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
            $row += 2;

            // Cash accounts detail
            $sheet->setCellValue('A' . $row, 'RINCIAN REKENING KAS');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->setCellValue('A' . $row, 'Kode');
            $sheet->setCellValue('B' . $row, 'Nama Rekening');
            $sheet->setCellValue('C' . $row, 'Saldo Awal');
            $sheet->setCellValue('D' . $row, 'Pergerakan');
            $sheet->setCellValue('E' . $row, 'Saldo Akhir');
            $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($cashFlow as $account) {
                $sheet->setCellValue('A' . $row, $account['code']);
                $sheet->setCellValue('B' . $row, $account['name']);
                $sheet->setCellValue('C' . $row, $account['beginning_balance']);
                $sheet->setCellValue('D' . $row, $account['movement']);
                $sheet->setCellValue('E' . $row, $account['ending_balance']);
                $row++;
            }

            // Auto-size columns
            foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'arus_kas_' . $startDate . '_' . $endDate . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }
    }
}
