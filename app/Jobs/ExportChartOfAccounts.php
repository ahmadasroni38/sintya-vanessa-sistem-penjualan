<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ChartOfAccountExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportChartOfAccounts implements ShouldQueue
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
    public $queue = 'exports';

    protected $userId;
    protected $options;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, array $options)
    {
        $this->userId = $userId;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(ChartOfAccountExportService $exportService): void
    {
        try {
            $user = User::find($this->userId);

            if (!$user) {
                Log::error('Export job failed: User not found', ['user_id' => $this->userId]);
                return;
            }

            // Generate export file
            $filename = $exportService->export($this->options);

            // Notify user that export is ready
            $user->notify(new \App\Notifications\ExportReady($filename, $this->options['format']));

            Log::info('Chart of accounts export completed', [
                'user_id' => $this->userId,
                'filename' => $filename,
                'format' => $this->options['format'],
                'filters' => $this->options['filters'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Chart of accounts export failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Notify user about export failure
            if ($user) {
                $user->notify(new \App\Notifications\ExportFailed($e->getMessage(), $this->options['format']));
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Chart of accounts export job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Notify user about permanent failure
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new \App\Notifications\ExportFailed(
                'Export failed permanently. Please try again or contact support.',
                $this->options['format']
            ));
        }
    }
}
