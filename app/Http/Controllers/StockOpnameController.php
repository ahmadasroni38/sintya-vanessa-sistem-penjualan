<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of stock opnames.
     */
    public function index(Request $request)
    {
        $query = StockOpname::query()
            ->with(['location', 'creator', 'completer', 'details.product']);

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

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('opname_date', [$request->start_date, $request->end_date]);
        }

        // Search by opname number or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('opname_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Order by opname date and number
        $query->orderBy('opname_date', 'desc')
            ->orderBy('opname_number', 'desc');

        $opnames = $query->paginate($request->per_page ?? 15);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $opnames->items(),
                'meta' => [
                    'current_page' => $opnames->currentPage(),
                    'last_page' => $opnames->lastPage(),
                    'per_page' => $opnames->perPage(),
                    'total' => $opnames->total(),
                ],
            ]);
        }

        return Inertia::render('Dashboard/Warehouse/StockOpname', [
            'opnames' => $opnames,
            'filters' => $request->only(['status', 'location_id', 'product_id', 'start_date', 'end_date', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new stock opname.
     */
    public function create()
    {
        $locations = Location::active()->orderBy('code')->get();

        return Inertia::render('Dashboard/StockOpnameForm', [
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created stock opname with multiple products.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.physical_quantity' => 'required|numeric|min:0',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create master opname
            $opname = StockOpname::create([
                'opname_date' => $validated['opname_date'],
                'location_id' => $validated['location_id'],
                'total_items' => count($validated['details']),
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create details (boot method will auto-calculate difference and type)
            foreach ($validated['details'] as $detailData) {
                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $detailData['product_id'],
                    'system_quantity' => $detailData['system_quantity'],
                    'physical_quantity' => $detailData['physical_quantity'],
                    'notes' => $detailData['notes'] ?? null,
                ]);
            }

            DB::commit();

            $opname->load(['location', 'creator', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname created successfully.',
                    'data' => $opname,
                ], 201);
            }

            return redirect()->route('stock-opnames.index')
                ->with('success', 'Stock Opname created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create opname: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified stock opname.
     */
    public function show(Request $request, StockOpname $stockOpname)
    {
        $stockOpname->load(['location', 'creator', 'completer', 'details.product']);

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $stockOpname,
            ]);
        }

        return Inertia::render('Dashboard/Warehouse/StockOpnameDetail', [
            'opname' => $stockOpname,
        ]);
    }

    /**
     * Show the form for editing the specified stock opname.
     */
    public function edit(StockOpname $stockOpname)
    {
        // Only draft opnames can be edited
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->withErrors(['error' => 'Only draft stock opname can be edited.']);
        }

        $stockOpname->load(['location', 'details.product']);
        $locations = Location::active()->orderBy('code')->get();

        return Inertia::render('Dashboard/StockOpnameForm', [
            'opname' => $stockOpname,
            'locations' => $locations,
        ]);
    }

    /**
     * Update the specified stock opname (draft/in_progress only).
     */
    public function update(Request $request, StockOpname $stockOpname)
    {
        // Only draft/in_progress opnames can be updated
        if (!in_array($stockOpname->status, ['draft', 'in_progress'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only draft or in-progress opnames can be updated.',
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Only draft or in-progress opnames can be updated.']);
        }

        $validated = $request->validate([
            'opname_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id' => 'nullable|exists:stock_opname_details,id',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.physical_quantity' => 'required|numeric|min:0',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update master
            $stockOpname->update([
                'opname_date' => $validated['opname_date'],
                'location_id' => $validated['location_id'],
                'total_items' => count($validated['details']),
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete old details
            $stockOpname->details()->delete();

            // Create new details
            foreach ($validated['details'] as $detailData) {
                StockOpnameDetail::create([
                    'stock_opname_id' => $stockOpname->id,
                    'product_id' => $detailData['product_id'],
                    'system_quantity' => $detailData['system_quantity'],
                    'physical_quantity' => $detailData['physical_quantity'],
                    'notes' => $detailData['notes'] ?? null,
                ]);
            }

            DB::commit();

            $stockOpname->load(['location', 'creator', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname updated successfully.',
                    'data' => $stockOpname,
                ]);
            }

            return redirect()->route('stock-opnames.index')
                ->with('success', 'Stock Opname updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update opname: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified stock opname (draft only).
     */
    public function destroy(Request $request, StockOpname $stockOpname)
    {
        // Only draft opnames can be deleted
        if ($stockOpname->status !== 'draft') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Only draft opnames can be deleted.',
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Only draft opnames can be deleted.']);
        }

        DB::beginTransaction();
        try {
            // Delete details first (cascade should handle this, but explicit for clarity)
            $stockOpname->details()->delete();

            // Delete master
            $stockOpname->delete();

            DB::commit();

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname deleted successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'Stock Opname deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete opname: ' . $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Complete the specified stock opname and create adjustment.
     */
    public function complete(Request $request, StockOpname $stockOpname)
    {
        try {
            $stockOpname->complete(auth()->id());
            $stockOpname->load(['location', 'creator', 'completer', 'details.product']);

            // Get the created adjustment if any
            $adjustment = null;
            if ($stockOpname->details()->where('difference_quantity', '!=', 0)->exists()) {
                $adjustment = StockAdjustment::where('description', 'like', "%{$stockOpname->opname_number}%")
                    ->with('details.product')
                    ->latest()
                    ->first();
            }

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname completed successfully.',
                    'data' => $stockOpname,
                    'adjustment' => $adjustment,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Opname completed successfully. Adjustments have been created for discrepancies.');
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
     * Start counting: draft → in_progress
     */
    public function startCounting(Request $request, StockOpname $stockOpname)
    {
        try {
            $stockOpname->startCounting(auth()->id());
            $stockOpname->load(['location', 'creator', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname counting started successfully.',
                    'data' => $stockOpname,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Opname counting started successfully.');
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
     * Cancel the specified stock opname.
     */
    public function cancel(Request $request, StockOpname $stockOpname)
    {
        try {
            $stockOpname->cancel();
            $stockOpname->load(['location', 'creator', 'completer', 'details.product']);

            // Return JSON for API requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Stock Opname cancelled successfully.',
                    'data' => $stockOpname,
                ]);
            }

            return redirect()->back()->with('success', 'Stock Opname cancelled successfully.');
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
     * Get statistics for stock opnames.
     */
    public function statistics(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $totalThisMonth = StockOpname::whereBetween('opname_date', [$startDate, $endDate])->count();
        $draft = StockOpname::where('status', 'draft')->count();
        $inProgress = StockOpname::where('status', 'in_progress')->count();
        $completed = StockOpname::where('status', 'completed')->count();

        $itemsCounted = StockOpnameDetail::whereHas('stockOpname', function($q) {
            $q->where('status', 'completed');
        })->count();

        return response()->json([
            'total_this_month' => $totalThisMonth,
            'draft' => $draft,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'items_counted' => $itemsCounted,
        ]);
    }

    /**
     * Bulk complete opnames.
     */
    public function bulkComplete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:stock_opnames,id',
        ]);

        DB::beginTransaction();
        try {
            $completed = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                try {
                    $opname = StockOpname::findOrFail($id);

                    if ($opname->status !== 'in_progress') {
                        $failed[] = [
                            'id' => $id,
                            'number' => $opname->opname_number,
                            'reason' => 'Only in-progress opnames can be completed',
                        ];
                        continue;
                    }

                    $opname->complete(auth()->id());
                    $completed++;
                } catch (\Exception $e) {
                    $failed[] = [
                        'id' => $id,
                        'number' => $opname->opname_number ?? 'Unknown',
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'message' => "{$completed} opname(s) completed successfully",
                'completed' => $completed,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk completion failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete opnames.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:stock_opnames,id',
        ]);

        DB::beginTransaction();
        try {
            $deleted = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                $opname = StockOpname::findOrFail($id);

                if ($opname->status !== 'draft') {
                    $failed[] = [
                        'id' => $id,
                        'number' => $opname->opname_number,
                        'reason' => 'Only draft opnames can be deleted',
                    ];
                    continue;
                }

                $opname->details()->delete();
                $opname->delete();
                $deleted++;
            }

            DB::commit();

            return response()->json([
                'message' => "{$deleted} opname(s) deleted successfully",
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
     * Export opnames to CSV (Master-Detail).
     */
    public function export(Request $request)
    {
        $query = StockOpname::query()
            ->with(['location', 'creator', 'completer', 'details.product']);

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
            $query->whereBetween('opname_date', [$request->start_date, $request->end_date]);
        }

        // Export specific IDs if provided
        if ($request->filled('ids')) {
            $query->whereIn('id', $request->ids);
        }

        $opnames = $query->orderBy('opname_date', 'desc')->get();

        // Create CSV
        $filename = 'stock_opnames_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($opnames) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Opname Number',
                'Date',
                'Location',
                'Product Code',
                'Product Name',
                'System Qty',
                'Physical Qty',
                'Difference',
                'Type',
                'Status',
                'Description',
                'Created By',
                'Completed By',
                'Notes',
            ]);

            // Data - one row per detail
            foreach ($opnames as $opname) {
                foreach ($opname->details as $detail) {
                    fputcsv($file, [
                        $opname->opname_number,
                        $opname->opname_date,
                        $opname->location->name ?? '',
                        $detail->product->product_code ?? '',
                        $detail->product->product_name ?? '',
                        $detail->system_quantity,
                        $detail->physical_quantity,
                        $detail->difference_quantity,
                        $detail->adjustment_type ?? '',
                        $opname->status,
                        $opname->description ?? '',
                        $opname->creator->name ?? '',
                        $opname->completer->name ?? '',
                        $opname->notes ?? '',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
