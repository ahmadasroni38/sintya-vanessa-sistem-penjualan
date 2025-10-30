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
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock Adjustment created successfully.');
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['product', 'location', 'creator', 'approver']);

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
            return redirect()->back()->withErrors(['error' => 'Only draft adjustments can be updated.']);
        }

        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'system_quantity' => 'required|numeric|min:0',
            'actual_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string',
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
        ]);

        return redirect()->route('stock-adjustments.index')
            ->with('success', 'Stock Adjustment updated successfully.');
    }

    /**
     * Remove the specified stock adjustment.
     */
    public function destroy(StockAdjustment $stockAdjustment)
    {
        // Only draft adjustments can be deleted
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->back()->withErrors(['error' => 'Only draft adjustments can be deleted.']);
        }

        $stockAdjustment->delete();

        return redirect()->back()->with('success', 'Stock Adjustment deleted successfully.');
    }

    /**
     * Approve the specified stock adjustment.
     */
    public function approve(StockAdjustment $stockAdjustment)
    {
        try {
            $stockAdjustment->post(auth()->id());

            return redirect()->back()->with('success', 'Stock Adjustment approved and posted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel the specified stock adjustment.
     */
    public function cancel(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'approved') {
            return redirect()->back()->withErrors(['error' => 'Only approved adjustments can be cancelled.']);
        }

        DB::beginTransaction();
        try {
            // Remove the stock card entry
            $stockAdjustment->stockCard()->delete();

            $stockAdjustment->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Stock Adjustment cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
}
