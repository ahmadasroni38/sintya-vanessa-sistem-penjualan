<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;

echo "========================================\n";
echo "LAPORAN NERACA LAJUR\n";
echo "========================================\n\n";

// Parameter date range
$startDate = '2025-01-01';
$endDate = '2025-12-31';

echo "Periode: {$startDate} s/d {$endDate}\n\n";

// Ambil semua akun aktif dengan balance
$accounts = ChartOfAccount::where('is_active', true)
    ->orderBy('account_code')
    ->get();

echo "Total Akun: " . $accounts->count() . "\n\n";

// Header tabel
echo str_pad("Kode Akun", 12) . " ";
echo str_pad("Nama Akun", 40) . " ";
echo str_pad("Tipe", 10) . " ";
echo str_pad("Saldo Awal", 18, " ", STR_PAD_LEFT) . " ";
echo str_pad("Neraca (D)", 18, " ", STR_PAD_LEFT) . " ";
echo str_pad("Neraca (C)", 18, " ", STR_PAD_LEFT) . " ";
echo str_pad("L/R (D)", 18, " ", STR_PAD_LEFT) . " ";
echo str_pad("L/R (C)", 18, " ", STR_PAD_LEFT) . "\n";
echo str_repeat("=", 160) . "\n";

// Inisialisasi totals
$totalNeracaDebit = 0;
$totalNeracaCredit = 0;
$totalLabaRugiDebit = 0;
$totalLabaRugiCredit = 0;

// Loop setiap akun
foreach ($accounts as $account) {
    // Hitung balance dari journal entry details yang posted
    $journalDetails = \App\Models\JournalEntryDetail::whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
        $query->where('status', 'posted')
            ->whereBetween('entry_date', [$startDate, $endDate]);
    })
        ->where('account_id', $account->id)
        ->get();

    $debitTotal = $journalDetails->where('transaction_type', 'debit')->sum('amount');
    $creditTotal = $journalDetails->where('transaction_type', 'credit')->sum('amount');

    // Hitung balance berdasarkan normal balance
    if ($account->normal_balance === 'debit') {
        $balance = $debitTotal - $creditTotal;
    } else {
        $balance = $creditTotal - $debitTotal;
    }

    // Skip jika balance = 0
    if ($balance == 0) {
        continue;
    }

    // Klasifikasi ke Neraca atau Laba Rugi
    $neracaDebit = 0;
    $neracaCredit = 0;
    $labaRugiDebit = 0;
    $labaRugiCredit = 0;

    if (in_array($account->account_type, ['asset', 'liability', 'equity'])) {
        // Masuk ke Neraca (Balance Sheet)
        // Logic berdasarkan normal balance dan balance value
        if ($balance > 0) {
            // Balance positif (sesuai normal balance)
            if ($account->normal_balance === 'debit') {
                $neracaDebit = $balance;
            } else {
                $neracaCredit = $balance;
            }
        } else {
            // Balance negatif (berlawanan dengan normal balance)
            if ($account->normal_balance === 'debit') {
                $neracaCredit = abs($balance);
            } else {
                $neracaDebit = abs($balance);
            }
        }
    } else {
        // Masuk ke Laba Rugi (Income Statement)
        if ($balance > 0) {
            if ($account->account_type === 'revenue') {
                $labaRugiCredit = $balance;
            } else {
                $labaRugiDebit = $balance;
            }
        } else {
            if ($account->account_type === 'revenue') {
                $labaRugiDebit = abs($balance);
            } else {
                $labaRugiCredit = abs($balance);
            }
        }
    }

    // Tampilkan baris akun
    echo str_pad($account->account_code, 12) . " ";
    echo str_pad(substr($account->account_name, 0, 40), 40) . " ";
    echo str_pad(strtoupper($account->account_type), 10) . " ";
    echo str_pad(number_format($balance, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . " ";
    echo str_pad($neracaDebit > 0 ? number_format($neracaDebit, 0, ',', '.') : '-', 18, " ", STR_PAD_LEFT) . " ";
    echo str_pad($neracaCredit > 0 ? number_format($neracaCredit, 0, ',', '.') : '-', 18, " ", STR_PAD_LEFT) . " ";
    echo str_pad($labaRugiDebit > 0 ? number_format($labaRugiDebit, 0, ',', '.') : '-', 18, " ", STR_PAD_LEFT) . " ";
    echo str_pad($labaRugiCredit > 0 ? number_format($labaRugiCredit, 0, ',', '.') : '-', 18, " ", STR_PAD_LEFT) . "\n";

    // Accumulate totals
    $totalNeracaDebit += $neracaDebit;
    $totalNeracaCredit += $neracaCredit;
    $totalLabaRugiDebit += $labaRugiDebit;
    $totalLabaRugiCredit += $labaRugiCredit;
}

// Footer dengan total
echo str_repeat("=", 160) . "\n";
echo str_pad("TOTAL", 62) . " ";
echo str_pad("", 18) . " ";
echo str_pad(number_format($totalNeracaDebit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . " ";
echo str_pad(number_format($totalNeracaCredit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . " ";
echo str_pad(number_format($totalLabaRugiDebit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . " ";
echo str_pad(number_format($totalLabaRugiCredit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . "\n";

echo "\n========================================\n";
echo "VERIFIKASI BALANCE\n";
echo "========================================\n";
echo "Neraca Debit:      Rp " . str_pad(number_format($totalNeracaDebit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . "\n";
echo "Neraca Credit:     Rp " . str_pad(number_format($totalNeracaCredit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . "\n";
echo "Selisih Neraca:    Rp " . str_pad(number_format(abs($totalNeracaDebit - $totalNeracaCredit), 0, ',', '.'), 18, " ", STR_PAD_LEFT);
echo " " . ($totalNeracaDebit === $totalNeracaCredit ? "✓" : "✗") . "\n\n";

echo "L/R Debit:         Rp " . str_pad(number_format($totalLabaRugiDebit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . "\n";
echo "L/R Credit:        Rp " . str_pad(number_format($totalLabaRugiCredit, 0, ',', '.'), 18, " ", STR_PAD_LEFT) . "\n";
echo "Selisih L/R:       Rp " . str_pad(number_format($totalLabaRugiCredit - $totalLabaRugiDebit, 0, ',', '.'), 18, " ", STR_PAD_LEFT);
if ($totalLabaRugiCredit > $totalLabaRugiDebit) {
    echo " (LABA)\n";
} elseif ($totalLabaRugiCredit < $totalLabaRugiDebit) {
    echo " (RUGI)\n";
} else {
    echo " (BREAK-EVEN)\n";
}

echo "========================================\n";
