<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

echo "========================================\n";
echo "VERIFIKASI DATA SALDO AWAL\n";
echo "========================================\n\n";

// Hitung total journal entries
$totalJE = JournalEntry::count();
$totalJED = JournalEntryDetail::count();

echo "Total Journal Entries: {$totalJE}\n";
echo "Total Journal Entry Details: {$totalJED}\n\n";

// Ambil opening balance entry
$openingEntry = JournalEntry::with('details.account')
    ->where('entry_type', 'opening')
    ->first();

if ($openingEntry) {
    echo "========================================\n";
    echo "JURNAL SALDO AWAL\n";
    echo "========================================\n";
    echo "Entry Number: {$openingEntry->entry_number}\n";
    echo "Entry Date: {$openingEntry->entry_date}\n";
    echo "Status: {$openingEntry->status}\n";
    echo "Total Detail Lines: {$openingEntry->details->count()}\n\n";

    $totalDebit = $openingEntry->details->sum('debit_amount');
    $totalCredit = $openingEntry->details->sum('credit_amount');

    echo "Total Debit:  Rp " . number_format($totalDebit, 0, ',', '.') . "\n";
    echo "Total Credit: Rp " . number_format($totalCredit, 0, ',', '.') . "\n";
    echo "Selisih:      Rp " . number_format(abs($totalDebit - $totalCredit), 0, ',', '.') . "\n";
    echo "Status:       " . ($totalDebit === $totalCredit ? "✓ BALANCE" : "✗ TIDAK BALANCE") . "\n\n";

    echo "========================================\n";
    echo "DETAIL TRANSAKSI\n";
    echo "========================================\n\n";

    foreach ($openingEntry->details as $detail) {
        $account = $detail->account;
        echo sprintf(
            "%-10s %-40s %s Rp %s\n",
            $account->account_code,
            $account->account_name,
            $detail->transaction_type === 'debit' ? 'D' : 'C',
            number_format($detail->amount, 0, ',', '.')
        );
    }

    echo "\n========================================\n";
    echo "RINGKASAN PER TIPE AKUN\n";
    echo "========================================\n\n";

    $groupedByType = $openingEntry->details->groupBy(function ($detail) {
        return $detail->account->account_type;
    });

    foreach ($groupedByType as $type => $details) {
        $totalDebitType = $details->where('transaction_type', 'debit')->sum('amount');
        $totalCreditType = $details->where('transaction_type', 'credit')->sum('amount');

        echo strtoupper($type) . "\n";
        echo "  Debit:  Rp " . number_format($totalDebitType, 0, ',', '.') . "\n";
        echo "  Credit: Rp " . number_format($totalCreditType, 0, ',', '.') . "\n";
        echo "  Net:    Rp " . number_format($totalDebitType - $totalCreditType, 0, ',', '.') . "\n\n";
    }

} else {
    echo "✗ Jurnal Saldo Awal tidak ditemukan.\n";
}

echo "========================================\n";
