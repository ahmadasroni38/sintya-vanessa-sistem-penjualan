<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of stock adjustments.
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::query()
            ->with(['product', 'location', 'creator', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by adjustment type
        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('adjustment_date', [$request->start_date, $request->end_date]);
        }

        // Search by adjustment number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
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
     * Show the form for creating a new stock adjustment.
     */
    public function create()
    {
        $products = Product::active()->orderBy('product_code')->get();
        $locations = Location::active()->orderBy('code')->get();

        return Inertia::render('Dashboard/StockAdjustmentForm', [
            'products' => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'system_quantity' => 'required|numeric|min:0',
            'actual_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Calculate difference and type
        $difference = $validated['actual_quantity'] - $validated['system_quantity'];
        $adjustmentType = $difference >= 0 ? 'increase' : 'decrease';

        $adjustment = StockAdjustment::create([
            'adjustment_date' => $validated['adjustment_date'],
            'product_id' => $validated['product_id'],
            'location_id' => $validated['location_id'],
            'system_quantity' => $validated['system_quantity'],
            'actual_quantity' => $validated['actual_quantity'],
            'difference_quantity' => abs($difference),
            'adjustment_type' => $adjustmentType,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        $adjustment->load(['product', 'location', 'creator']);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Stock Adjustment created successfully.',
                'data' => $adjustment,
            ], 201);
        }

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock Adjustment created successfully.');
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['product', 'location', 'creator', 'approver']);

        // Return JSON for API requests
        if (request()->expectsJson()) {
            return response()->json([
                'data' => $stockAdjustment,
            ]);
        }

        return Inertia::render('Dashboard/StockAdjustmentDetail', [
            'adjustment' => $stockAdjustment,
        ]);
    }

    /**
     * Show the form for editing the specified stock adjustment.
     */
    public function edit(StockAdjustment $stockAdjustment)
    {
        // Only draft adjustments can be edited
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->back()->withErrors(['error' => 'Only draft adjustments can be edited.']);
        }

        $stockAdjustment->load('product', 'location');
        $products = Product::active()->orderBy('product_code')->get();
        $locations = Location::active()->orderBy('code')->get();

        return Inertia::render('Dashboard/StockAdjustmentForm', [
            'adjustment' => $stockAdjustment,
            'products' => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Update the specified stock adjustment.
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
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'system_quantity' => 'required|numeric|min:0',
            'actual_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Calculate difference and type
        $difference = $validated['actual_quantity'] - $validated['system_quantity'];
        $adjustmentType = $difference >= 0 ? 'increase' : 'decrease';

        $stockAdjustment->update([
            'adjustment_date' => $validated['adjustment_date'],
            'product_id' => $validated['product_id'],
            'location_id' => $validated['location_id'],
            'system_quantity' => $validated['system_quantity'],
            'actual_quantity' => $validated['actual_quantity'],
            'difference_quantity' => abs($difference),
            'adjustment_type' => $adjustmentType,
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $stockAdjustment->load(['product', 'location', 'creator']);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Stock Adjustment updated successfully.',
                'data' => $stockAdjustment,
            ]);
        }

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock Adjustment updated successfully.');
    }

    /**
     * Remove the specified stock adjustment.
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

        $stockAdjustment->delete();

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Stock Adjustment deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Stock Adjustment deleted successfully.');
    }

    /**
     * Approve the specified stock adjustment.
     */
    public function approve(Request $request, StockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->post(auth()->id());
            $stockAdjustment->load(['product', 'location', 'creator', 'approver']);

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
        if ($stockAdjustment->status !== 'posted') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only posted adjustments can be cancelled.',
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Only posted adjustments can be cancelled.']);
        }

        DB::beginTransaction();
        try {
            // Remove the stock card entry
            $stockAdjustment->stockCard()->delete();

            $stockAdjustment->update(['status' => 'cancelled']);

            DB::commit();

            $stockAdjustment->load(['product', 'location', 'creator', 'approver']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Adjustment cancelled successfully.',
                    'data' => $stockAdjustment,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Adjustment cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        $increase = StockAdjustment::where('adjustment_type', 'increase')->count();
        $decrease = StockAdjustment::where('adjustment_type', 'decrease')->count();
        $posted = StockAdjustment::where('status', 'posted')->count();

        return response()->json([
            'total_this_month' => $totalThisMonth,
            'pending' => $pending,
            'increase' => $increase,
            'decrease' => $decrease,
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
     * Export adjustments to Excel/CSV.
     */
    public function export(Request $request)
    {
        $query = StockAdjustment::query()
            ->with(['product', 'location', 'creator', 'approver']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
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
                'Product Code',
                'Product Name',
                'Location',
                'System Quantity',
                'Actual Quantity',
                'Difference',
                'Type',
                'Reason',
                'Status',
                'Created By',
                'Approved By',
                'Notes',
            ]);

            // Data
            foreach ($adjustments as $adjustment) {
                fputcsv($file, [
                    $adjustment->adjustment_number,
                    $adjustment->adjustment_date,
                    $adjustment->product->product_code ?? '',
                    $adjustment->product->product_name ?? '',
                    $adjustment->location->name ?? '',
                    $adjustment->system_quantity,
                    $adjustment->actual_quantity,
                    $adjustment->difference_quantity,
                    $adjustment->adjustment_type,
                    $adjustment->reason,
                    $adjustment->status,
                    $adjustment->creator->name ?? '',
                    $adjustment->approver->name ?? '',
                    $adjustment->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
