<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OpeningBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Jurnal Umum Saldo Awal untuk semua akun
     * Total Debit = Total Credit = Rp 1,235,000,000
     */
    public function run(): void
    {
        // Tanggal saldo awal (1 Januari 2025)
        $openingDate = Carbon::create(2025, 1, 1);

        // Cek apakah jurnal opening sudah ada (termasuk yang soft deleted)
        $existingEntry = JournalEntry::withTrashed()->where('entry_number', 'JE-202500001')->first();

        if ($existingEntry) {
            echo "========================================\n";
            echo "Jurnal Saldo Awal sudah ada!\n";
            echo "Menghapus data lama secara permanen...\n";
            echo "========================================\n\n";

            // Hapus menggunakan DB facade untuk bypass model events
            DB::table('journal_entry_details')
                ->where('journal_entry_id', $existingEntry->id)
                ->delete();

            DB::table('journal_entries')
                ->where('id', $existingEntry->id)
                ->delete();
        }

        // Buat Journal Entry untuk saldo awal
        $journalEntry = JournalEntry::create([
            'entry_number' => 'JE-202500001',
            'entry_date' => $openingDate,
            'reference_number' => 'OB-2025',
            'description' => 'Jurnal Saldo Awal per 1 Januari 2025',
            'entry_type' => 'opening',
            'status' => 'posted',
            'created_by' => 1, // Assuming user ID 1 exists
            'posted_by' => 1,
            'posted_at' => now(),
        ]);

        // Array saldo awal dengan nominal yang masuk akal dan balance
        $openingBalances = [
            // AKTIVA LANCAR (Current Assets) - DEBIT
            [
                'account_code' => '1-1100',
                'description' => 'Saldo awal Kas',
                'debit' => 50000000, // Rp 50 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-1200',
                'description' => 'Saldo awal Bank',
                'debit' => 200000000, // Rp 200 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-1300',
                'description' => 'Saldo awal Piutang Usaha',
                'debit' => 75000000, // Rp 75 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-1400',
                'description' => 'Saldo awal Persediaan Barang',
                'debit' => 150000000, // Rp 150 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-1500',
                'description' => 'Saldo awal Perlengkapan',
                'debit' => 10000000, // Rp 10 juta
                'credit' => 0,
            ],

            // AKTIVA TETAP (Fixed Assets) - DEBIT
            [
                'account_code' => '1-2100',
                'description' => 'Saldo awal Tanah',
                'debit' => 200000000, // Rp 200 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-2200',
                'description' => 'Saldo awal Bangunan',
                'debit' => 300000000, // Rp 300 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-2210',
                'description' => 'Akumulasi Penyusutan Bangunan',
                'debit' => 0,
                'credit' => 50000000, // Rp 50 juta (kontras/pengurang aktiva)
            ],
            [
                'account_code' => '1-2300',
                'description' => 'Saldo awal Peralatan',
                'debit' => 100000000, // Rp 100 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-2310',
                'description' => 'Akumulasi Penyusutan Peralatan',
                'debit' => 0,
                'credit' => 20000000, // Rp 20 juta (kontras/pengurang aktiva)
            ],
            [
                'account_code' => '1-2400',
                'description' => 'Saldo awal Kendaraan',
                'debit' => 150000000, // Rp 150 juta
                'credit' => 0,
            ],
            [
                'account_code' => '1-2410',
                'description' => 'Akumulasi Penyusutan Kendaraan',
                'debit' => 0,
                'credit' => 30000000, // Rp 30 juta (kontras/pengurang aktiva)
            ],

            // KEWAJIBAN LANCAR (Current Liabilities) - CREDIT
            [
                'account_code' => '2-1100',
                'description' => 'Saldo awal Hutang Usaha',
                'debit' => 0,
                'credit' => 50000000, // Rp 50 juta
            ],
            [
                'account_code' => '2-1200',
                'description' => 'Saldo awal Hutang Gaji',
                'debit' => 0,
                'credit' => 15000000, // Rp 15 juta
            ],
            [
                'account_code' => '2-1300',
                'description' => 'Saldo awal Hutang Pajak',
                'debit' => 0,
                'credit' => 10000000, // Rp 10 juta
            ],

            // KEWAJIBAN JANGKA PANJANG (Long-term Liabilities) - CREDIT
            [
                'account_code' => '2-2100',
                'description' => 'Saldo awal Hutang Bank',
                'debit' => 0,
                'credit' => 100000000, // Rp 100 juta
            ],

            // MODAL (Equity) - CREDIT
            [
                'account_code' => '3-1000',
                'description' => 'Saldo awal Modal Pemilik',
                'debit' => 0,
                'credit' => 960000000, // Rp 960 juta (balancing figure)
            ],
        ];

        // Validasi: Hitung total debit dan credit
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($openingBalances as $balance) {
            $totalDebit += $balance['debit'];
            $totalCredit += $balance['credit'];
        }

        // Tampilkan informasi validasi
        echo "========================================\n";
        echo "VALIDASI JURNAL SALDO AWAL\n";
        echo "========================================\n";
        echo "Total Debit  : Rp " . number_format($totalDebit, 0, ',', '.') . "\n";
        echo "Total Credit : Rp " . number_format($totalCredit, 0, ',', '.') . "\n";
        echo "Selisih      : Rp " . number_format(abs($totalDebit - $totalCredit), 0, ',', '.') . "\n";
        echo "Status       : " . ($totalDebit === $totalCredit ? "✓ BALANCE" : "✗ TIDAK BALANCE") . "\n";
        echo "========================================\n\n";

        // Jika tidak balance, lempar exception
        if ($totalDebit !== $totalCredit) {
            throw new \Exception("Jurnal tidak balance! Debit: {$totalDebit}, Credit: {$totalCredit}");
        }

        // Insert journal entry details
        foreach ($openingBalances as $balance) {
            // Cari account berdasarkan account_code
            $account = ChartOfAccount::where('account_code', $balance['account_code'])->first();

            if (!$account) {
                echo "WARNING: Account {$balance['account_code']} tidak ditemukan. Skip.\n";
                continue;
            }

            // Tentukan transaction_type berdasarkan debit/credit
            $transactionType = $balance['debit'] > 0 ? 'debit' : 'credit';
            $amount = $balance['debit'] > 0 ? $balance['debit'] : $balance['credit'];

            // Buat journal entry detail
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $account->id,
                'transaction_type' => $transactionType,
                'amount' => $amount,
                'debit_amount' => $balance['debit'],
                'credit_amount' => $balance['credit'],
                'description' => $balance['description'],
            ]);

            echo "✓ {$account->account_code} - {$account->account_name}: ";
            if ($balance['debit'] > 0) {
                echo "Debit Rp " . number_format($balance['debit'], 0, ',', '.') . "\n";
            } else {
                echo "Credit Rp " . number_format($balance['credit'], 0, ',', '.') . "\n";
            }
        }

        echo "\n========================================\n";
        echo "Jurnal Saldo Awal berhasil dibuat!\n";
        echo "Entry Number: {$journalEntry->entry_number}\n";
        echo "========================================\n";
    }
}
