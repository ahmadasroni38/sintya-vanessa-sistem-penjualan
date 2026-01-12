<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of journal entries.
     */
    public function index(Request $request)
    {
        // Gate::authorize('viewAny', JournalEntry::class);

        $query = JournalEntry::query()->withFullDetails();

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by entry type
        if ($request->filled('entry_type')) {
            $query->byType($request->entry_type);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->byYear($request->year);
        }

        // Filter by month
        if ($request->filled('year') && $request->filled('month')) {
            $query->byMonth($request->year, $request->month);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Include soft deleted if requested
        if ($request->filled('with_deleted') && $request->with_deleted) {
            $query->withTrashed();
        }

        // Sorting - support both sort_field and sort_by
        $sortField = $request->get('sort_by', $request->get('sort_field', 'entry_date'));
        $sortDirection = $request->get('sort_direction', 'desc');

        // Map frontend column keys to database columns
        $sortableFields = [
            'entry_number' => 'entry_number',
            'entry_date' => 'entry_date',
            'description' => 'description',
            'status' => 'status',
            'total_amount' => 'entry_date', // Default to entry_date for total_amount since it's calculated
        ];

        $sortField = $sortableFields[$sortField] ?? 'entry_date';

        if ($sortField === 'entry_date') {
            $query->orderBy('entry_date', $sortDirection)
                ->orderBy('entry_number', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Check if client wants all data (for client-side pagination)
        if ($request->get('all') === 'true') {
            // Return all data for client-side pagination
            $entries = $query->get();

            return response()->json([
                'success' => true,
                'data' => $entries,
                'filters' => $request->only(['status', 'entry_type', 'start_date', 'end_date', 'year', 'month', 'search', 'with_deleted']),
            ]);
        }

        // Server-side pagination
        $perPage = $request->get('per_page', 10);
        $entries = $query->paginate($perPage);

        // Return in Laravel pagination format that frontend expects
        return response()->json([
            'success' => true,
            'data' => $entries->items(),
            'current_page' => $entries->currentPage(),
            'per_page' => $entries->perPage(),
            'total' => $entries->total(),
            'last_page' => $entries->lastPage(),
            'from' => $entries->firstItem(),
            'to' => $entries->lastItem(),
            'filters' => $request->only(['status', 'entry_type', 'start_date', 'end_date', 'year', 'month', 'search', 'with_deleted']),
        ]);
    }

    /**
     * Store a newly created journal entry.
     */
    public function store(Request $request)
    {
        // Gate::authorize('create', JournalEntry::class);

        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50',
            'description' => 'required|string|max:1000',
            'entry_type' => 'required|in:general,adjustment,closing,opening',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.transaction_type' => 'required|in:debit,credit',
            'details.*.amount' => 'required|numeric|min:0.01',
            'details.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Validate that total debit equals total credit
        $totalDebit = collect($validated['details'])
            ->where('transaction_type', 'debit')
            ->sum('amount');

        $totalCredit = collect($validated['details'])
            ->where('transaction_type', 'credit')
            ->sum('amount');

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Journal entry is not balanced',
                'errors' => [
                    'details' => ["Total debit (Rp " . number_format($totalDebit, 2) . ") must equal total credit (Rp " . number_format($totalCredit, 2) . ")"]
                ]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::create([
                'entry_date' => $validated['entry_date'],
                'reference_number' => $validated['reference_number'],
                'description' => $validated['description'],
                'entry_type' => $validated['entry_type'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['details'] as $detail) {
                $journalEntry->details()->create([
                    'account_id' => $detail['account_id'],
                    'transaction_type' => $detail['transaction_type'],
                    'amount' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            DB::commit();

            // Load relationships
            $journalEntry->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry created successfully',
                'data' => $journalEntry
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create journal entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified journal entry.
     */
    public function show(JournalEntry $journalEntry)
    {
        // Gate::authorize('view', $journalEntry);

        $journalEntry->load(['creator', 'poster', 'details.account']);

        return response()->json([
            'success' => true,
            'data' => $journalEntry
        ]);
    }

    /**
     * Update the specified journal entry.
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        // Gate::authorize('update', $journalEntry);

        // Only draft entries can be updated
        if ($journalEntry->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft entries can be updated'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50',
            'description' => 'required|string|max:1000',
            'entry_type' => 'required|in:general,adjustment,closing,opening',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.transaction_type' => 'required|in:debit,credit',
            'details.*.amount' => 'required|numeric|min:0.01',
            'details.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Validate balance
        $totalDebit = collect($validated['details'])
            ->where('transaction_type', 'debit')
            ->sum('amount');

        $totalCredit = collect($validated['details'])
            ->where('transaction_type', 'credit')
            ->sum('amount');

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Journal entry is not balanced',
                'errors' => [
                    'details' => ["Total debit (Rp " . number_format($totalDebit, 2) . ") must equal total credit (Rp " . number_format($totalCredit, 2) . ")"]
                ]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $journalEntry->update([
                'entry_date' => $validated['entry_date'],
                'reference_number' => $validated['reference_number'],
                'description' => $validated['description'],
                'entry_type' => $validated['entry_type'],
            ]);

            // Delete old details and create new ones
            $journalEntry->details()->delete();

            foreach ($validated['details'] as $detail) {
                $journalEntry->details()->create([
                    'account_id' => $detail['account_id'],
                    'transaction_type' => $detail['transaction_type'],
                    'amount' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            $journalEntry->refresh();
            $journalEntry->total_debit = $totalDebit;
            $journalEntry->total_credit = $totalCredit;
            $journalEntry->save();
            $journalEntry->refresh();

            DB::commit();

            // Reload relationships
            $journalEntry->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry updated successfully',
                'data' => $journalEntry
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update journal entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified journal entry.
     */
    public function destroy(JournalEntry $journalEntry)
    {
        // Gate::authorize('delete', $journalEntry);

        // Only draft entries can be deleted
        if ($journalEntry->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft entries can be deleted'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $journalEntry->details()->delete();
            $journalEntry->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Journal entry deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete journal entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post the specified journal entry.
     */
    public function post(JournalEntry $journalEntry)
    {
        // Gate::authorize('post', $journalEntry);

        try {
            $journalEntry->post(auth()->id());

            // Reload relationships
            $journalEntry->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry posted successfully',
                'data' => $journalEntry
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel the specified journal entry.
     */
    public function cancel(Request $request, JournalEntry $journalEntry)
    {
        // Gate::authorize('cancel', $journalEntry);

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $journalEntry->cancel($request->reason);

            // Reload relationships
            $journalEntry->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry cancelled successfully',
                'data' => $journalEntry
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Create a reversal entry.
     */
    public function reverse(Request $request, JournalEntry $journalEntry)
    {
        // Gate::authorize('reverse', $journalEntry);

        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string|max:1000',
            'reversal_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reversalDate = $request->reversal_date ? new \DateTime($request->reversal_date) : null;
            $reversal = $journalEntry->reverse($request->description, $reversalDate);

            // Load relationships
            $reversal->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Reversal entry created successfully',
                'data' => $reversal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Duplicate the specified journal entry.
     */
    public function duplicate(Request $request, JournalEntry $journalEntry)
    {
        // Gate::authorize('duplicate', $journalEntry);

        $validator = Validator::make($request->all(), [
            'new_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $newDate = $request->new_date ? new \DateTime($request->new_date) : null;
            $duplicate = $journalEntry->duplicate($newDate);

            // Load relationships
            $duplicate->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry duplicated successfully',
                'data' => $duplicate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a soft deleted journal entry.
     */
    public function restore($id)
    {
        $journalEntry = JournalEntry::withTrashed()->findOrFail($id);
        // Gate::authorize('restore', $journalEntry);

        try {
            $journalEntry->restore();

            // Load relationships
            $journalEntry->load(['creator', 'poster', 'details.account']);

            return response()->json([
                'success' => true,
                'message' => 'Journal entry restored successfully',
                'data' => $journalEntry
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore journal entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics.
     */
    public function statistics(Request $request)
    {
        // Gate::authorize('viewAny', JournalEntry::class);

        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $startDate = $request->start_date ? new \DateTime($request->start_date) : null;
            $endDate = $request->end_date ? new \DateTime($request->end_date) : null;

            $statistics = JournalEntry::getStatistics($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate next entry number.
     */
    public function generateNumber(Request $request)
    {
        // Gate::authorize('create', JournalEntry::class);

        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $year = $request->year ?? date('Y');
            $entryNumber = JournalEntry::generateEntryNumber($year);

            return response()->json([
                'success' => true,
                'entry_number' => $entryNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate entry number',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
