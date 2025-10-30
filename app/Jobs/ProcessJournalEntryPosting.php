<?php

namespace App\Jobs;

use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Jobs\CalculateAccountBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessJournalEntryPosting implements ShouldQueue
{
    use Dispatchable, Queueable;

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
    public $queue = 'journal-posting';

    protected $journalEntryId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $journalEntryId)
    {
        $this->journalEntryId = $journalEntryId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $journalEntry = JournalEntry::find($this->journalEntryId);

            if (!$journalEntry) {
                Log::error('Journal entry not found for posting', [
                    'journal_entry_id' => $this->journalEntryId,
                ]);
                return;
            }

            if ($journalEntry->status !== 'draft') {
                Log::warning('Journal entry is not in draft status', [
                    'journal_entry_id' => $this->journalEntryId,
                    'current_status' => $journalEntry->status,
                ]);
                return;
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Update journal entry status to posted
                $journalEntry->update([
                    'status' => 'posted',
                    'posted_by' => $journalEntry->created_by, // Use original creator as poster
                    'posted_at' => now(),
                ]);

                // Update account balances for all affected accounts
                $affectedAccounts = $journalEntry->details()
                    ->with('account')
                    ->get()
                    ->pluck('account');

                foreach ($affectedAccounts as $account) {
                    if ($account) {
                        // Dispatch balance calculation job
                        CalculateAccountBalance::dispatch($account->id);
                    }
                }

                // Commit transaction
                DB::commit();

                Log::info('Journal entry posted successfully', [
                    'journal_entry_id' => $this->journalEntryId,
                    'entry_number' => $journalEntry->entry_number,
                    'affected_accounts' => $affectedAccounts->pluck('id')->toArray(),
                ]);

            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();

                Log::error('Failed to post journal entry', [
                    'journal_entry_id' => $this->journalEntryId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Retry the job
                $this->release(30);
            }

        } catch (\Exception $e) {
            Log::error('Unexpected error in journal entry posting job', [
                'journal_entry_id' => $this->journalEntryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Retry the job
            $this->release(30);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Journal entry posting job failed permanently', [
            'journal_entry_id' => $this->journalEntryId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
