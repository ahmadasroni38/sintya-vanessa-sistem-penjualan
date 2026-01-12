<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Jobs\ExportChartOfAccounts;
use App\Jobs\CalculateAccountBalance;
use App\Models\User;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of chart of accounts.
     */
    public function index(Request $request)
    {
        // // Gate::authorize('viewAny', ChartOfAccount::class);

        // Include soft deleted if requested
        $query = $request->filled('with_deleted') && $request->with_deleted
            ? ChartOfAccount::withTrashed()->with('parent')
            : ChartOfAccount::query()->with('parent');

        // Filter by account type
        if ($request->filled('type')) {
            $query->where('account_type', $request->type);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Search by code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort_field', 'account_code');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Check if client wants all data (for client-side pagination)
        if ($request->get('all') === 'true' || !$request->has('per_page')) {
            // Return all data for client-side pagination
            $accounts = $query->get();

            return response()->json([
                'success' => true,
                'data' => $accounts,
                'filters' => $request->only(['type', 'is_active', 'level', 'search', 'with_deleted']),
            ]);
        }

        // Server-side pagination
        $perPage = $request->get('per_page', 15);
        $accounts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $accounts->items(),
            'pagination' => [
                'total' => $accounts->total(),
                'per_page' => $accounts->perPage(),
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'from' => $accounts->firstItem(),
                'to' => $accounts->lastItem(),
            ],
            'filters' => $request->only(['type', 'is_active', 'level', 'search', 'with_deleted']),
        ]);
    }

    /**
     * Get hierarchical tree of accounts.
     */
    public function tree(Request $request)
    {
        // Gate::authorize('viewAny', ChartOfAccount::class);
        $accounts = ChartOfAccount::with('allChildren')
            ->whereNull('parent_id')
            ->orderBy('account_code')
            ->get();

        return response()->json($accounts);
    }

    /**
     * Display the specified chart of account.
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        // Gate::authorize('view', $chartOfAccount);
        $chartOfAccount->load(['parent', 'children']);

        return response()->json([
            'success' => true,
            'data' => $chartOfAccount
        ]);
    }

    /**
     * Store a newly created chart of account.
     */
    public function store(StoreChartOfAccountRequest $request)
    {
        // Gate::authorize('create', ChartOfAccount::class);
        $account = ChartOfAccount::create($request->validated());

        return response()->json([
            'message' => 'Chart of Account created successfully.',
            'data' => $account
        ], 201);
    }

    /**
     * Update the specified chart of account.
     */
    public function update(UpdateChartOfAccountRequest $request, ChartOfAccount $chartOfAccount)
    {
        // Gate::authorize('update', $chartOfAccount);
        $chartOfAccount->update($request->validated());

        return response()->json([
            'message' => 'Chart of Account updated successfully.',
            'data' => $chartOfAccount
        ]);
    }

    /**
     * Remove the specified chart of account.
     */
    public function destroy(ChartOfAccount $chartOfAccount)
    {
        // Gate::authorize('delete', $chartOfAccount);
        // Check if account has children
        if ($chartOfAccount->children()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete account with child accounts.'
            ], 422);
        }

        // Check if account has journal entries
        if ($chartOfAccount->journalEntryDetails()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete account with journal entries.'
            ], 422);
        }

        $chartOfAccount->delete();

        return response()->json([
            'message' => 'Chart of Account deleted successfully.'
        ]);
    }

    /**
     * Get account balance for a specific period.
     */
    public function balance(Request $request, ChartOfAccount $chartOfAccount)
    {
        // Gate::authorize('viewBalance', $chartOfAccount);
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $balance = $chartOfAccount->getBalance(
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );

        return response()->json([
            'account' => $chartOfAccount,
            'balance' => $balance,
            'period' => [
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ],
        ]);
    }
/**
 * Get all active accounts for dropdown/select.
 */
public function active()
{
    // Gate::authorize('viewAny', ChartOfAccount::class);

    $accounts = ChartOfAccount::active()
        ->orderBy('account_code')
        ->get(['id', 'account_code', 'account_name', 'account_type', 'normal_balance']);

    return response()->json($accounts);
}

/**
 * Get balance history for chart of account.
 */
public function balanceHistory(Request $request, ChartOfAccount $chartOfAccount)
{
    // Gate::authorize('view', $chartOfAccount);

    $validated = $request->validate([
        'per_page' => 'nullable|integer|min:1|max:100',
        'period_start' => 'nullable|date',
        'period_end' => 'nullable|date|after_or_equal:period_start'
    ]);

    $query = $chartOfAccount->balanceHistories();

    // Apply filters
    if (!empty($validated['period_start'])) {
        $query->where('period_start', '>=', $validated['period_start']);
    }

    if (!empty($validated['period_end'])) {
        $query->where('period_end', '<=', $validated['period_end']);
    }

    $histories = $query->orderBy('period_end', 'desc')
        ->paginate($validated['per_page'] ?? 15);

    return response()->json([
        'success' => true,
        'data' => $histories->items(),
        'pagination' => [
            'total' => $histories->total(),
            'per_page' => $histories->perPage(),
            'current_page' => $histories->currentPage(),
            'last_page' => $histories->lastPage(),
            'from' => $histories->firstItem(),
            'to' => $histories->lastItem(),
        ],
        'filters' => $validated
    ]);
}

/**
 * Calculate balance for chart of account.
 */
public function calculateBalance(Request $request, ChartOfAccount $chartOfAccount)
{
    // Gate::authorize('view', $chartOfAccount);

    $validated = $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'async' => 'nullable|boolean'
    ]);

    $async = $validated['async'] ?? false;

    if ($async) {
        // Dispatch balance calculation job
        $user = auth()->user();
        CalculateAccountBalance::dispatch($chartOfAccount->id, [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balance calculation job has been queued. You will be notified when it\'s ready.',
            'job_id' => uniqid()
        ]);
    } else {
        // Synchronous calculation
        try {
            $balanceService = new \App\Services\ChartOfAccountBalanceService();

            $balanceData = $balanceService->calculateBalance($chartOfAccount, [
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null
            ]);

            // Save balance history (update if exists, create if not)
            $startDate = $validated['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
            $endDate = $validated['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

            \App\Models\AccountBalanceHistory::updateOrCreate(
                [
                    'chart_of_account_id' => $chartOfAccount->id,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                ],
                [
                    'balance' => $balanceData['balance'],
                    'debit_total' => $balanceData['debit_total'],
                    'credit_total' => $balanceData['credit_total'],
                    'calculated_by' => auth()->id(),
                ]
            );

            // Update account's current balance
            $chartOfAccount->current_balance = $balanceData['balance'];
            $chartOfAccount->balance_updated_at = now();
            $chartOfAccount->saveQuietly();

            return response()->json([
                'success' => true,
                'message' => 'Balance calculated successfully',
                'data' => $balanceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate balance: ' . $e->getMessage()
            ], 500);
        }
    }
}

/**
 * Get audit history for chart of account.
 */
public function audits(Request $request, ChartOfAccount $chartOfAccount)
{
    // Gate::authorize('view', $chartOfAccount);

    $validated = $request->validate([
        'per_page' => 'nullable|integer|min:1|max:100',
        'event_type' => 'nullable|in:created,updated,deleted,restored',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date'
    ]);

    $query = $chartOfAccount->audits();

    // Apply filters
    if (!empty($validated['event_type'])) {
        $query->where('event_type', $validated['event_type']);
    }

    if (!empty($validated['start_date'])) {
        $query->where('created_at', '>=', $validated['start_date']);
    }

    if (!empty($validated['end_date'])) {
        $query->where('created_at', '<=', $validated['end_date']);
    }

    $audits = $query->orderBy('created_at', 'desc')
        ->paginate($validated['per_page'] ?? 15);

    return response()->json([
        'success' => true,
        'data' => $audits->items(),
        'pagination' => [
            'total' => $audits->total(),
            'per_page' => $audits->perPage(),
            'current_page' => $audits->currentPage(),
            'last_page' => $audits->lastPage(),
            'from' => $audits->firstItem(),
            'to' => $audits->lastItem(),
        ],
        'filters' => $validated
    ]);
}

/**
 * Restore a soft deleted chart of account.
 */
public function restore($id)
{
    $chartOfAccount = ChartOfAccount::withTrashed()->findOrFail($id);
    // Gate::authorize('restore', $chartOfAccount);

    $chartOfAccount->restore();

    return response()->json([
        'success' => true,
        'message' => 'Chart of Account restored successfully.',
        'data' => $chartOfAccount->fresh()
    ]);
}

/**
 * Move account to a new parent.
 */
public function move(Request $request, ChartOfAccount $chartOfAccount)
{
    // Gate::authorize('update', $chartOfAccount);

    $validated = $request->validate([
        'new_parent_id' => 'nullable|exists:chart_of_accounts,id'
    ]);

    try {
        $chartOfAccount->moveToNewParent($validated['new_parent_id']);

        return response()->json([
            'success' => true,
            'message' => 'Account moved successfully.',
            'data' => $chartOfAccount->fresh(['parent', 'children'])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

/**
 * Generate account code.
 */
public function generateCode(Request $request)
{
    // Gate::authorize('create', ChartOfAccount::class);

    $validated = $request->validate([
        'parent_id' => 'nullable|exists:chart_of_accounts,id'
    ]);

    try {
        $code = ChartOfAccount::generateAccountCode($validated['parent_id'] ?? null);

        return response()->json([
            'success' => true,
            'code' => $code
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

/**
 * Export chart of accounts.
 */
public function export(Request $request)
{
    // Gate::authorize('export', ChartOfAccount::class);

    $validated = $request->validate([
        'format' => 'required|in:excel,pdf',
        'filters' => 'nullable|array',
        'filters.type' => 'nullable|string|in:asset,liability,equity,revenue,expense',
        'filters.status' => 'nullable|string|in:active,inactive',
        'filters.search' => 'nullable|string|max:255',
        'filters.start_date' => 'nullable|date',
        'filters.end_date' => 'nullable|date|after_or_equal:filters.start_date'
    ]);

    // Dispatch export job
    $user = auth()->user();
    ExportChartOfAccounts::dispatch($user->id, $validated);

    return response()->json([
        'success' => true,
        'message' => 'Export job has been queued. You will be notified when it\'s ready.',
        'job_id' => uniqid()
    ]);
}

/**
 * Get export file for download.
 */
public function downloadExport(Request $request, $filename)
{
    // Gate::authorize('export', ChartOfAccount::class);

    $filepath = storage_path('app/exports/' . $filename);

    if (!file_exists($filepath)) {
        return response()->json([
            'success' => false,
            'message' => 'Export file not found.'
        ], 404);
    }

    return response()->download($filepath, $filename);
}

/**
 * Get export status.
 */
public function getExports(Request $request)
{
    // Gate::authorize('export', ChartOfAccount::class);

    $user = auth()->user();

    // This would typically query a database table for export tracking
    // For now, return a mock response
    $exports = [
        [
            'id' => 1,
            'filename' => 'chart-of-accounts-2024-01-15-10-30-00.xlsx',
            'format' => 'excel',
            'status' => 'completed',
            'created_at' => now()->subMinutes(30)->toDateTimeString(),
            'size' => 15420
        ],
        [
            'id' => 2,
            'filename' => 'chart-of-accounts-2024-01-10-14-00-00.pdf',
            'format' => 'pdf',
            'status' => 'failed',
            'created_at' => now()->subHours(2)->toDateTimeString(),
            'size' => 8750
        ]
    ];

    return response()->json([
        'success' => true,
        'data' => $exports
    ]);
}
}
