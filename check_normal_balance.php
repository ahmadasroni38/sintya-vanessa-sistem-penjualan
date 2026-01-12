<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChartOfAccount;

echo "========================================\n";
echo "CEK NORMAL BALANCE AKUN\n";
echo "========================================\n\n";

// Cek akun akumulasi penyusutan
$accounts = ChartOfAccount::where('account_code', 'like', '%-2_10')
    ->get(['account_code', 'account_name', 'account_type', 'normal_balance']);

foreach ($accounts as $account) {
    echo sprintf(
        "%-10s %-40s [%-10s] Normal: %-6s\n",
        $account->account_code,
        $account->account_name,
        strtoupper($account->account_type),
        strtoupper($account->normal_balance)
    );
}

echo "\n========================================\n";
