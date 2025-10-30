<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryApproval;
use App\Models\JournalEntryAttachment;
use App\Models\JournalEntryRevision;
use App\Jobs\CalculateAccountBalance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class JournalEntryService
{
    /**
     * Create a new journal entry with details
     *
     * @param array $data
     * @param array $details
     * @return JournalEntry
     * @throws \Exception
     */
    public function createJournalEntry(array $data, array $details): JournalEntry
    {
        try {
            DB::beginTransaction();

            // Validate balance before creating
            $this->validateBalance($details);

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_date' => $data['entry_date'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'],
                'entry_type' => $data['entry_type'] ?? 'general',
                'currency' => $data['currency'] ?? 'IDR',
                'exchange_rate' => $data['exchange_rate'] ?? 1.0000,
                'metadata' => $data['metadata'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Create journal entry details
            foreach ($details as $detail) {
                $journalEntry->details()->create([
                    'account_id' => $detail['account_id'],
                    'transaction_type' => $detail['transaction_type'],
                    'amount' => $detail['amount'],
                    'debit_amount' => $detail['transaction_type'] === 'debit' ? $detail['amount'] : 0,
                    'credit_amount' => $detail['transaction_type'] === 'credit' ? $detail['amount'] : 0,
                    'description' => $detail['description'] ?? null,
                    'quantity' => $detail['quantity'] ?? null,
                    'unit_price' => $detail['unit_price'] ?? null,
                    'tax_rate' => $detail['tax_rate'] ?? null,
                    'tax_amount' => $detail['tax_amount'] ?? 0,
                    'department_id' => $detail['department_id'] ?? null,
                    'project_id' => $detail['project_id'] ?? null,
                    'reconciliation_id' => $detail['reconciliation_id'] ?? null,
                ]);
            }

            // Update totals
            $this->updateTotals($journalEntry);

            DB::commit();

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry created successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'created_by' => auth()->id(),
            ]);

            return $journalEntry->fresh(['details.account']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create journal entry', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'details' => $details,
            ]);

            throw $e;
        }
    }

    /**
     * Update an existing journal entry
     *
     * @param JournalEntry $journalEntry
     * @param array $data
     * @param array $details
     * @return JournalEntry
     * @throws \Exception
     */
    public function updateJournalEntry(JournalEntry $journalEntry, array $data, array $details): JournalEntry
    {
        try {
            if (!$journalEntry->can_edit) {
                throw new \Exception('Journal entry cannot be edited in current status');
            }

            DB::beginTransaction();

            // Store old values for revision
            $oldValues = $journalEntry->getOriginal();
            $oldDetails = $journalEntry->details()->get()->toArray();

            // Update journal entry
            $journalEntry->update([
                'entry_date' => $data['entry_date'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'],
                'entry_type' => $data['entry_type'] ?? 'general',
                'currency' => $data['currency'] ?? 'IDR',
                'exchange_rate' => $data['exchange_rate'] ?? 1.0000,
                'metadata' => $data['metadata'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // Delete old details and create new ones
            $journalEntry->details()->delete();
            foreach ($details as $detail) {
                $journalEntry->details()->create([
                    'account_id' => $detail['account_id'],
                    'transaction_type' => $detail['transaction_type'],
                    'amount' => $detail['amount'],
                    'debit_amount' => $detail['transaction_type'] === 'debit' ? $detail['amount'] : 0,
                    'credit_amount' => $detail['transaction_type'] === 'credit' ? $detail['amount'] : 0,
                    'description' => $detail['description'] ?? null,
                    'quantity' => $detail['quantity'] ?? null,
                    'unit_price' => $detail['unit_price'] ?? null,
                    'tax_rate' => $detail['tax_rate'] ?? null,
                    'tax_amount' => $detail['tax_amount'] ?? 0,
                    'department_id' => $detail['department_id'] ?? null,
                    'project_id' => $detail['project_id'] ?? null,
                    'reconciliation_id' => $detail['reconciliation_id'] ?? null,
                ]);
            }

            // Update totals
            $this->updateTotals($journalEntry);

            // Create revision
            $changes = $this->calculateChanges($oldValues, $data);
            if (!empty($changes)) {
                $journalEntry->createRevision($changes, $data['revision_notes'] ?? null);
            }

            DB::commit();

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry updated successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'updated_by' => auth()->id(),
            ]);

            return $journalEntry->fresh(['details.account']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'details' => $details,
            ]);

            throw $e;
        }
    }

    /**
     * Post a journal entry
     *
     * @param JournalEntry $journalEntry
     * @param int|null $userId
     * @return JournalEntry
     * @throws \Exception
     */
    public function postJournalEntry(JournalEntry $journalEntry, ?int $userId = null): JournalEntry
    {
        try {
            return $journalEntry->post($userId);
        } catch (\Exception $e) {
            Log::error('Failed to post journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Approve a journal entry
     *
     * @param JournalEntry $journalEntry
     * @param int $userId
     * @param string|null $notes
     * @return JournalEntry
     * @throws \Exception
     */
    public function approveJournalEntry(JournalEntry $journalEntry, int $userId, ?string $notes = null): JournalEntry
    {
        try {
            if (!in_array($journalEntry->status, ['draft', 'pending_approval'])) {
                throw new \Exception('Journal entry cannot be approved in current status');
            }

            DB::beginTransaction();

            // Create approval record
            $journalEntry->approvals()->create([
                'user_id' => $userId,
                'status' => 'approved',
                'notes' => $notes,
                'approved_at' => now(),
            ]);

            // Update journal entry
            $journalEntry->update([
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
                'updated_by' => $userId,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry approved successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'approved_by' => $userId,
            ]);

            return $journalEntry->fresh();

        } catch (\Exception $e) {
            Log::error('Failed to approve journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Reject a journal entry
     *
     * @param JournalEntry $journalEntry
     * @param int $userId
     * @param string $notes
     * @return JournalEntry
     * @throws \Exception
     */
    public function rejectJournalEntry(JournalEntry $journalEntry, int $userId, string $notes): JournalEntry
    {
        try {
            if (!in_array($journalEntry->status, ['draft', 'pending_approval'])) {
                throw new \Exception('Journal entry cannot be rejected in current status');
            }

            DB::beginTransaction();

            // Create approval record
            $journalEntry->approvals()->create([
                'user_id' => $userId,
                'status' => 'rejected',
                'notes' => $notes,
            ]);

            // Update journal entry
            $journalEntry->update([
                'status' => 'rejected',
                'updated_by' => $userId,
            ]);

            DB::commit();

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry rejected successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'rejected_by' => $userId,
            ]);

            return $journalEntry->fresh();

        } catch (\Exception $e) {
            Log::error('Failed to reject journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Cancel a journal entry
     *
     * @param JournalEntry $journalEntry
     * @param int|null $userId
     * @return JournalEntry
     * @throws \Exception
     */
    public function cancelJournalEntry(JournalEntry $journalEntry, ?int $userId = null): JournalEntry
    {
        try {
            if ($journalEntry->status !== 'posted') {
                throw new \Exception('Only posted entries can be cancelled');
            }

            $journalEntry->update([
                'status' => 'cancelled',
                'updated_by' => $userId,
            ]);

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry cancelled successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'cancelled_by' => $userId,
            ]);

            return $journalEntry->fresh();

        } catch (\Exception $e) {
            Log::error('Failed to cancel journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Delete a journal entry
     *
     * @param JournalEntry $journalEntry
     * @return bool
     * @throws \Exception
     */
    public function deleteJournalEntry(JournalEntry $journalEntry): bool
    {
        try {
            if (!$journalEntry->can_delete) {
                throw new \Exception('Journal entry cannot be deleted in current status');
            }

            DB::beginTransaction();

            // Delete related records
            $journalEntry->details()->delete();
            $journalEntry->approvals()->delete();
            $journalEntry->attachments()->delete();
            $journalEntry->revisions()->delete();

            // Delete journal entry
            $journalEntry->delete();

            DB::commit();

            // Clear cache
            Cache::forget('journal-entries-list');

            Log::info('Journal entry deleted successfully', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'deleted_by' => auth()->id(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete journal entry', [
                'journal_entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Upload attachment to journal entry
     *
     * @param JournalEntry $journalEntry
     * @param \Illuminate\Http\UploadedFile $file
     * @return JournalEntryAttachment
     * @throws \Exception
     */
    public function uploadAttachment(JournalEntry $journalEntry, $file): JournalEntryAttachment
    {
        try {
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('journal-attachments', $filename, 'public');

            return $journalEntry->attachments()->create([
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to upload attachment', [
                'journal_entry_id' => $journalEntry->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Delete attachment
     *
     * @param JournalEntryAttachment $attachment
     * @return bool
     * @throws \Exception
     */
    public function deleteAttachment(JournalEntryAttachment $attachment): bool
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Delete attachment record
            $attachment->delete();

            Log::info('Attachment deleted successfully', [
                'attachment_id' => $attachment->id,
                'filename' => $attachment->filename,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete attachment', [
                'attachment_id' => $attachment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Validate journal entry balance
     *
     * @param array $details
     * @throws \Exception
     */
    private function validateBalance(array $details): void
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $detail) {
            if ($detail['transaction_type'] === 'debit') {
                $totalDebit += $detail['amount'];
            } else {
                $totalCredit += $detail['amount'];
            }
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception("Journal entry is not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}");
        }
    }

    /**
     * Update journal entry totals
     *
     * @param JournalEntry $journalEntry
     * @return void
     */
    private function updateTotals(JournalEntry $journalEntry): void
    {
        $totalDebit = $journalEntry->details()->where('transaction_type', 'debit')->sum('amount');
        $totalCredit = $journalEntry->details()->where('transaction_type', 'credit')->sum('amount');

        $journalEntry->update([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);
    }

    /**
     * Calculate changes for revision
     *
     * @param array $oldValues
     * @param array $newValues
     * @return array
     */
    private function calculateChanges(array $oldValues, array $newValues): array
    {
        $changes = [];

        foreach ($newValues as $key => $value) {
            if (array_key_exists($key, $oldValues) && $oldValues[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get trial balance for a period
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getTrialBalance(?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $query = ChartOfAccount::with(['journalEntryDetails' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');

                    if ($startDate) {
                        $q->whereDate('entry_date', '>=', $startDate);
                    }

                    if ($endDate) {
                        $q->whereDate('entry_date', '<=', $endDate);
                    }
                });
            }]);

            $accounts = $query->get();
            $trialBalance = [];

            foreach ($accounts as $account) {
                $debitTotal = $account->journalEntryDetails
                    ->where('transaction_type', 'debit')
                    ->sum('amount');

                $creditTotal = $account->journalEntryDetails
                    ->where('transaction_type', 'credit')
                    ->sum('amount');

                $balance = $account->opening_balance + $debitTotal - $creditTotal;

                $trialBalance[] = [
                    'account_id' => $account->id,
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'normal_balance' => $account->normal_balance,
                    'opening_balance' => $account->opening_balance,
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal,
                    'balance' => $balance,
                ];
            }

            return [
                'trial_balance' => $trialBalance,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'generated_at' => now()->toDateTimeString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate trial balance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Get general ledger for an account
     *
     * @param int $accountId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getGeneralLedger(int $accountId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $account = ChartOfAccount::findOrFail($accountId);

            $detailsQuery = JournalEntryDetail::with(['journalEntry'])
                ->where('account_id', $accountId)
                ->whereHas('journalEntry', function ($query) {
                    $query->where('status', 'posted');
                });

            if ($startDate) {
                $detailsQuery->whereHas('journalEntry', function ($query) use ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                });
            }

            if ($endDate) {
                $detailsQuery->whereHas('journalEntry', function ($query) use ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                });
            }

            $details = $detailsQuery->orderBy('journal_entry_id')->get();

            $runningBalance = $account->opening_balance;
            $ledger = [];

            foreach ($details as $detail) {
                if ($detail->transaction_type === 'debit') {
                    $runningBalance += $detail->amount;
                } else {
                    $runningBalance -= $detail->amount;
                }

                $ledger[] = [
                    'date' => $detail->journalEntry->entry_date,
                    'entry_number' => $detail->journalEntry->entry_number,
                    'description' => $detail->description ?? $detail->journalEntry->description,
                    'transaction_type' => $detail->transaction_type,
                    'amount' => $detail->amount,
                    'debit_amount' => $detail->debit_amount,
                    'credit_amount' => $detail->credit_amount,
                    'running_balance' => $runningBalance,
                ];
            }

            return [
                'account' => $account,
                'ledger' => $ledger,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'opening_balance' => $account->opening_balance,
                'closing_balance' => $runningBalance,
                'generated_at' => now()->toDateTimeString(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate general ledger', [
                'account_id' => $accountId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            throw $e;
        }
    }

    /**
     * Get journal entries with filtering and pagination
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws \Exception
     */
    public function getJournalEntries(array $params = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        try {
            $query = JournalEntry::with(['creator', 'poster', 'details.account']);

            // Filter by status
            if (!empty($params['status'])) {
                $query->where('status', $params['status']);
            }

            // Filter by entry type
            if (!empty($params['entry_type'])) {
                $query->where('entry_type', $params['entry_type']);
            }

            // Filter by date range
            if (!empty($params['start_date']) && !empty($params['end_date'])) {
                $query->whereBetween('entry_date', [$params['start_date'], $params['end_date']]);
            }

            // Search by entry number or description
            if (!empty($params['search'])) {
                $search = $params['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('entry_number', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Order by entry date and entry number
            $query->orderBy('entry_date', 'desc')
                  ->orderBy('entry_number', 'desc');

            return $query->paginate($params['per_page'] ?? 15);

        } catch (\Exception $e) {
            Log::error('Failed to fetch journal entries', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params,
            ]);

            throw $e;
        }
    }

    /**
     * Get active accounts for journal entry form
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getActiveAccounts(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return ChartOfAccount::active()
                    ->orderBy('account_code')
                    ->get(['id', 'account_code', 'account_name', 'account_type', 'normal_balance']);

        } catch (\Exception $e) {
            Log::error('Failed to fetch active accounts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get journal entry with details
     *
     * @param int $journalEntryId
     * @return JournalEntry
     * @throws \Exception
     */
    public function getJournalEntryWithDetails(int $journalEntryId): JournalEntry
    {
        try {
            return JournalEntry::with(['details.account', 'creator', 'poster', 'approver'])
                    ->findOrFail($journalEntryId);

        } catch (\Exception $e) {
            Log::error('Failed to fetch journal entry', [
                'journal_entry_id' => $journalEntryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
