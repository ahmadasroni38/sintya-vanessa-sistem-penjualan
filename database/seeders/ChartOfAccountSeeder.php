<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // AKTIVA (ASSET)
            ['code' => '1-0000', 'name' => 'AKTIVA', 'type' => 'asset', 'balance' => 'debit', 'parent' => null, 'level' => 1],

            // Aktiva Lancar
            ['code' => '1-1000', 'name' => 'AKTIVA LANCAR', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-0000', 'level' => 2],
            ['code' => '1-1100', 'name' => 'Kas', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-1000', 'level' => 3],
            ['code' => '1-1200', 'name' => 'Bank', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-1000', 'level' => 3],
            ['code' => '1-1300', 'name' => 'Piutang Usaha', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-1000', 'level' => 3],
            ['code' => '1-1400', 'name' => 'Persediaan Barang', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-1000', 'level' => 3],
            ['code' => '1-1500', 'name' => 'Perlengkapan', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-1000', 'level' => 3],

            // Aktiva Tetap
            ['code' => '1-2000', 'name' => 'AKTIVA TETAP', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-0000', 'level' => 2],
            ['code' => '1-2100', 'name' => 'Tanah', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2200', 'name' => 'Bangunan', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2210', 'name' => 'Akumulasi Penyusutan Bangunan', 'type' => 'asset', 'balance' => 'credit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2300', 'name' => 'Peralatan', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2310', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'asset', 'balance' => 'credit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2400', 'name' => 'Kendaraan', 'type' => 'asset', 'balance' => 'debit', 'parent' => '1-2000', 'level' => 3],
            ['code' => '1-2410', 'name' => 'Akumulasi Penyusutan Kendaraan', 'type' => 'asset', 'balance' => 'credit', 'parent' => '1-2000', 'level' => 3],

            // KEWAJIBAN (LIABILITY)
            ['code' => '2-0000', 'name' => 'KEWAJIBAN', 'type' => 'liability', 'balance' => 'credit', 'parent' => null, 'level' => 1],

            // Kewajiban Lancar
            ['code' => '2-1000', 'name' => 'KEWAJIBAN LANCAR', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-0000', 'level' => 2],
            ['code' => '2-1100', 'name' => 'Hutang Usaha', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-1000', 'level' => 3],
            ['code' => '2-1200', 'name' => 'Hutang Gaji', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-1000', 'level' => 3],
            ['code' => '2-1300', 'name' => 'Hutang Pajak', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-1000', 'level' => 3],

            // Kewajiban Jangka Panjang
            ['code' => '2-2000', 'name' => 'KEWAJIBAN JANGKA PANJANG', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-0000', 'level' => 2],
            ['code' => '2-2100', 'name' => 'Hutang Bank', 'type' => 'liability', 'balance' => 'credit', 'parent' => '2-2000', 'level' => 3],

            // MODAL (EQUITY)
            ['code' => '3-0000', 'name' => 'MODAL', 'type' => 'equity', 'balance' => 'credit', 'parent' => null, 'level' => 1],
            ['code' => '3-1000', 'name' => 'Modal Pemilik', 'type' => 'equity', 'balance' => 'credit', 'parent' => '3-0000', 'level' => 2],
            ['code' => '3-2000', 'name' => 'Prive', 'type' => 'equity', 'balance' => 'debit', 'parent' => '3-0000', 'level' => 2],
            ['code' => '3-3000', 'name' => 'Laba Ditahan', 'type' => 'equity', 'balance' => 'credit', 'parent' => '3-0000', 'level' => 2],

            // PENDAPATAN (REVENUE)
            ['code' => '4-0000', 'name' => 'PENDAPATAN', 'type' => 'revenue', 'balance' => 'credit', 'parent' => null, 'level' => 1],
            ['code' => '4-1000', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'balance' => 'credit', 'parent' => '4-0000', 'level' => 2],
            ['code' => '4-2000', 'name' => 'Pendapatan Jasa', 'type' => 'revenue', 'balance' => 'credit', 'parent' => '4-0000', 'level' => 2],
            ['code' => '4-3000', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'balance' => 'credit', 'parent' => '4-0000', 'level' => 2],

            // BEBAN (EXPENSE)
            ['code' => '5-0000', 'name' => 'BEBAN', 'type' => 'expense', 'balance' => 'debit', 'parent' => null, 'level' => 1],

            // Beban Operasional
            ['code' => '5-1000', 'name' => 'BEBAN OPERASIONAL', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-0000', 'level' => 2],
            ['code' => '5-1100', 'name' => 'Beban Gaji', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1200', 'name' => 'Beban Listrik', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1300', 'name' => 'Beban Air', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1400', 'name' => 'Beban Telepon', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1500', 'name' => 'Beban Transportasi', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1600', 'name' => 'Beban Perlengkapan', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],
            ['code' => '5-1700', 'name' => 'Beban Penyusutan', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-1000', 'level' => 3],

            // Beban Lain-lain
            ['code' => '5-2000', 'name' => 'BEBAN LAIN-LAIN', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-0000', 'level' => 2],
            ['code' => '5-2100', 'name' => 'Beban Bunga', 'type' => 'expense', 'balance' => 'debit', 'parent' => '5-2000', 'level' => 3],
        ];

        $accountMap = [];

        foreach ($accounts as $account) {
            $parentId = null;
            if ($account['parent']) {
                $parentId = $accountMap[$account['parent']] ?? null;
            }

            $created = ChartOfAccount::create([
                'account_code' => $account['code'],
                'account_name' => $account['name'],
                'account_type' => $account['type'],
                'normal_balance' => $account['balance'],
                'parent_id' => $parentId,
                'level' => $account['level'],
                'is_active' => true,
                'opening_balance' => 0,
            ]);

            $accountMap[$account['code']] = $created->id;
        }

        $this->command->info('Chart of Accounts seeded successfully!');
    }
}
