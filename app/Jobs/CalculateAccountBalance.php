<?php

namespace App\Jobs;

use App\Models\ChartOfAccount;
use App\Models\AccountBalanceHistory;
use App\Services\ChartOfAccountBalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateAccountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = [30, 60, 120];

    /**
     * The queue the job should be sent to.
     */
    public $queue = 'balances';

    protected $accountId;
    protected $startDate;
    protected $endDate;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $accountId, ?string $startDate = null, ?string $endDate = null, ?int $userId = null)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userId = $userId ?? auth()->id();
    }

    /**
     * Execute the job.
     */
    public function handle(ChartOfAccountBalanceService $balanceService): void
    {
        try {
            $account = ChartOfAccount::find($this->accountId);

            if (!$account) {
                Log::error('Balance calculation job failed: Account not found', [
                    'account_id' => $this->accountId
                ]);
                return;
            }

            // Calculate balance for the specified period
            $balanceData = $balanceService->calculateBalance($account, [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate
            ]);

            // Save balance history
            AccountBalanceHistory::create([
                'chart_of_account_id' => $this->accountId,
                'balance' => $balanceData['balance'],
                'debit_total' => $balanceData['debit_total'],
                'credit_total' => $balanceData['credit_total'],
                'period_start' => $this->startDate ?? now()->startOfMonth()->format('Y-m-d'),
                'period_end' => $this->endDate ?? now()->endOfMonth()->format('Y-m-d'),
                'calculated_by' => $this->userId,
            ]);

            // Update account's current balance and timestamp
            $account->current_balance = $balanceData['balance'];
            $account->balance_updated_at = now();
            $account->saveQuietly();

            Log::info('Account balance calculated and saved', [
                'account_id' => $this->accountId,
                'account_code' => $account->account_code,
                'balance' => $balanceData['balance'],
                'period_start' => $this->startDate,
                'period_end' => $this->endDate,
                'calculated_by' => $this->userId,
            ]);

        } catch (\Exception $e) {
            Log::error('Balance calculation job failed', [
                'account_id' => $this->accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Balance calculation job failed permanently', [
            'account_id' => $this->accountId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }
}
