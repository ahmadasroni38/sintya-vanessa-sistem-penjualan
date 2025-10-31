<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of stock adjustments (Master-Detail).
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::query()
            ->with(['location', 'creator', 'approver', 'details.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by product (search in details)
        if ($request->filled('product_id')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by adjustment type (search in details)
        if ($request->filled('adjustment_type')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('adjustment_type', $request->adjustment_type);
            });
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('adjustment_date', [$request->start_date, $request->end_date]);
        }

        // Search by adjustment number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Order by adjustment date and number
        $query->orderBy('adjustment_date', 'desc')
            ->orderBy('adjustment_number', 'desc');

        $adjustments = $query->paginate($request->per_page ?? 15);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $adjustments->items(),
                'meta' => [
                    'current_page' => $adjustments->currentPage(),
                    'last_page' => $adjustments->lastPage(),
                    'per_page' => $adjustments->perPage(),
                    'total' => $adjustments->total(),
                ],
            ]);
        }

        return Inertia::render('Dashboard/StockAdjustment', [
            'adjustments' => $adjustments,
            'filters' => $request->only(['status', 'adjustment_type', 'location_id', 'product_id', 'start_date', 'end_date', 'search']),
        ]);
    }

    /**
     * Store a newly created stock adjustment with multiple products.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.actual_quantity' => 'required|numeric|min:0',
            'details.*.reason' => 'nullable|string',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create master adjustment
            $adjustment = StockAdjustment::create([
                'adjustment_date' => $validated['adjustment_date'],
                'location_id' => $validated['location_id'],
                'total_items' => count($validated['details']),
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create details
            foreach ($validated['details'] as $detailData) {
                $difference = $detailData['actual_quantity'] - $detailData['system_quantity'];
                $type = $difference >= 0 ? 'increase' : 'decrease';

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $detailData['product_id'],
                    'system_quantity' => $detailData['system_quantity'],
                    'actual_quantity' => $detailData['actual_quantity'],
                    'difference_quantity' => $difference,
                    'adjustment_type' => $type,
                    'reason' => $detailData['reason'] ?? null,
                    'notes' => $detailData['notes'] ?? null,
                ]);
            }

            DB::commit();

            $adjustment->load(['location', 'creator', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment created successfully.',
                    'data' => $adjustment,
                ], 201);
            }

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock Adjustment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create adjustment: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show(Request $request, StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['location', 'creator', 'approver', 'details.product']);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $stockAdjustment,
            ]);
        }

        return Inertia::render('Dashboard/StockAdjustmentDetail', [
            'adjustment' => $stockAdjustment,
        ]);
    }

    /**
     * Update the specified stock adjustment (draft only).
     */
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        // Only draft adjustments can be updated
        if ($stockAdjustment->status !== 'draft') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only draft adjustments can be updated.',
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Only draft adjustments can be updated.']);
        }

        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id' => 'nullable|exists:stock_adjustment_details,id',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.actual_quantity' => 'required|numeric|min:0',
            'details.*.reason' => 'nullable|string',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update master
            $stockAdjustment->update([
                'adjustment_date' => $validated['adjustment_date'],
                'location_id' => $validated['location_id'],
                'total_items' => count($validated['details']),
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete old details
            $stockAdjustment->details()->delete();

            // Create new details
            foreach ($validated['details'] as $detailData) {
                $difference = $detailData['actual_quantity'] - $detailData['system_quantity'];
                $type = $difference >= 0 ? 'increase' : 'decrease';

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'product_id' => $detailData['product_id'],
                    'system_quantity' => $detailData['system_quantity'],
                    'actual_quantity' => $detailData['actual_quantity'],
                    'difference_quantity' => $difference,
                    'adjustment_type' => $type,
                    'reason' => $detailData['reason'] ?? null,
                    'notes' => $detailData['notes'] ?? null,
                ]);
            }

            DB::commit();

            $stockAdjustment->load(['location', 'creator', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment updated successfully.',
                    'data' => $stockAdjustment,
                ]);
            }

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock Adjustment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update adjustment: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified stock adjustment (draft only).
     */
    public function destroy(Request $request, StockAdjustment $stockAdjustment)
    {
        // Only draft adjustments can be deleted
        if ($stockAdjustment->status !== 'draft') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only draft adjustments can be deleted.',
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Only draft adjustments can be deleted.']);
        }

        DB::beginTransaction();
        try {
            // Delete details first (cascade should handle this, but explicit for clarity)
            $stockAdjustment->details()->delete();

            // Delete master
            $stockAdjustment->delete();

            DB::commit();

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment deleted successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Stock Adjustment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete adjustment: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Approve the specified stock adjustment and create stock cards.
     */
    public function approve(Request $request, StockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->post(auth()->id());
            $stockAdjustment->load(['location', 'creator', 'approver', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment approved and posted successfully.',
                    'data' => $stockAdjustment,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Adjustment approved and posted successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel the specified stock adjustment.
     */
    public function cancel(Request $request, StockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->cancel();
            $stockAdjustment->load(['location', 'creator', 'approver', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment cancelled successfully.',
                    'data' => $stockAdjustment,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Adjustment cancelled successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Auto-calculate system quantity based on stock card.
     */
    public function calculateSystemQuantity(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $product = Product::find($validated['product_id']);
        $systemQuantity = $product->getCurrentStock($validated['location_id']);

        return response()->json([
            'system_quantity' => $systemQuantity,
        ]);
    }

    /**
     * Get statistics for stock adjustments.
     */
    public function statistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalThisMonth = StockAdjustment::whereBetween('adjustment_date', [$startDate, $endDate])->count();
        $pending = StockAdjustment::where('status', 'draft')->count();

        // Count by checking details for type
        $increaseCount = StockAdjustmentDetail::whereHas('stockAdjustment', function($q) {
            $q->where('status', '!=', 'cancelled');
        })->where('adjustment_type', 'increase')->count();

        $decreaseCount = StockAdjustmentDetail::whereHas('stockAdjustment', function($q) {
            $q->where('status', '!=', 'cancelled');
        })->where('adjustment_type', 'decrease')->count();

        $posted = StockAdjustment::where('status', 'posted')->count();

        return response()->json([
            'total_this_month' => $totalThisMonth,
            'pending' => $pending,
            'increase' => $increaseCount,
            'decrease' => $decreaseCount,
            'posted' => $posted,
        ]);
    }

    /**
     * Bulk approve adjustments.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:stock_adjustments,id',
        ]);

        DB::beginTransaction();
        try {
            $approved = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                try {
                    $adjustment = StockAdjustment::findOrFail($id);

                    if ($adjustment->status !== 'draft') {
                        $failed[] = [
                            'id' => $id,
                            'number' => $adjustment->adjustment_number,
                            'reason' => 'Only draft adjustments can be approved',
                        ];
                        continue;
                    }

                    $adjustment->post(auth()->id());
                    $approved++;
                } catch (\Exception $e) {
                    $failed[] = [
                        'id' => $id,
                        'number' => $adjustment->adjustment_number ?? 'Unknown',
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'message' => "{$approved} adjustment(s) approved successfully",
                'approved' => $approved,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk approval failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete adjustments.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:stock_adjustments,id',
        ]);

        DB::beginTransaction();
        try {
            $deleted = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                $adjustment = StockAdjustment::findOrFail($id);

                if ($adjustment->status !== 'draft') {
                    $failed[] = [
                        'id' => $id,
                        'number' => $adjustment->adjustment_number,
                        'reason' => 'Only draft adjustments can be deleted',
                    ];
                    continue;
                }

                $adjustment->details()->delete();
                $adjustment->delete();
                $deleted++;
            }

            DB::commit();

            return response()->json([
                'message' => "{$deleted} adjustment(s) deleted successfully",
                'deleted' => $deleted,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk deletion failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Export adjustments to CSV (Master-Detail).
     */
    public function export(Request $request)
    {
        $query = StockAdjustment::query()
            ->with(['location', 'creator', 'approver', 'details.product']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('product_id')) {
            $query->whereHas('details', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('adjustment_date', [$request->start_date, $request->end_date]);
        }

        // Export specific IDs if provided
        if ($request->filled('ids')) {
            $query->whereIn('id', $request->ids);
        }

        $adjustments = $query->orderBy('adjustment_date', 'desc')->get();

        // Create CSV
        $filename = 'stock_adjustments_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($adjustments) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Adjustment Number',
                'Date',
                'Location',
                'Product Code',
                'Product Name',
                'System Qty',
                'Actual Qty',
                'Difference',
                'Type',
                'Item Reason',
                'Status',
                'Description',
                'Created By',
                'Approved By',
                'Notes',
            ]);

            // Data - one row per detail
            foreach ($adjustments as $adjustment) {
                foreach ($adjustment->details as $detail) {
                    fputcsv($file, [
                        $adjustment->adjustment_number,
                        $adjustment->adjustment_date,
                        $adjustment->location->name ?? '',
                        $detail->product->product_code ?? '',
                        $detail->product->product_name ?? '',
                        $detail->system_quantity,
                        $detail->actual_quantity,
                        $detail->difference_quantity,
                        $detail->adjustment_type,
                        $detail->reason ?? '',
                        $adjustment->status,
                        $adjustment->description ?? '',
                        $adjustment->creator->name ?? '',
                        $adjustment->approver->name ?? '',
                        $adjustment->notes ?? '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
