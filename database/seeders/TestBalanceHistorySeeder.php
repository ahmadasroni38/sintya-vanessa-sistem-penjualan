<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\AccountBalanceHistory;

class TestBalanceHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get first account
        $account = ChartOfAccount::first();

        if (!$account) {
            $this->command->error('No accounts found. Please create an account first.');
            return;
        }

        $this->command->info("Creating test balance history for account: {$account->account_code} - {$account->account_name}");

        // Create test balance history entries
        AccountBalanceHistory::create([
            'chart_of_account_id' => $account->id,
            'balance' => 1500000.00,
            'debit_total' => 2000000.00,
            'credit_total' => 500000.00,
            'period_start' => now()->startOfMonth()->format('Y-m-d'),
            'period_end' => now()->endOfMonth()->format('Y-m-d'),
            'calculated_by' => 1,
        ]);

        $this->command->info('Balance history created successfully!');
        $this->command->info('You can now test the balance history feature in the Chart of Accounts page.');
    }
}
