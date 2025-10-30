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
            ->with(['location', 'creator', 'completer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('opname_date', [$request->start_date, $request->end_date]);
        }

        // Search by opname number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('opname_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Order by opname date and number
        $query->orderBy('opname_date', 'desc')
            ->orderBy('opname_number', 'desc');

        $opnames = $query->paginate($request->per_page ?? 15);

        return Inertia::render('Dashboard/StockOpname', [
            'opnames' => $opnames,
            'filters' => $request->only(['status', 'location_id', 'start_date', 'end_date', 'search']),
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
     * Store a newly created stock opname.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.actual_quantity' => 'required|numeric|min:0',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'opname_date' => $validated['opname_date'],
                'location_id' => $validated['location_id'],
                'notes' => $validated['notes'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['details'] as $detail) {
                $difference = $detail['actual_quantity'] - $detail['system_quantity'];

                $opname->details()->create([
                    'product_id' => $detail['product_id'],
                    'system_quantity' => $detail['system_quantity'],
                    'actual_quantity' => $detail['actual_quantity'],
                    'difference_quantity' => abs($difference),
                    'adjustment_type' => $difference >= 0 ? 'increase' : 'decrease',
                    'notes' => $detail['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('stock-opnames.index')
                ->with('success', 'Stock Opname created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified stock opname.
     */
    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['location', 'creator', 'completer', 'details.product']);

        return Inertia::render('Dashboard/StockOpnameDetail', [
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
     * Update the specified stock opname.
     */
    public function update(Request $request, StockOpname $stockOpname)
    {
        // Only draft opnames can be updated
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->withErrors(['error' => 'Only draft stock opname can be updated.']);
        }

        $validated = $request->validate([
            'opname_date' => 'required|date',
            'location_id' => 'required|exists:locations,id',
            'notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.system_quantity' => 'required|numeric|min:0',
            'details.*.actual_quantity' => 'required|numeric|min:0',
            'details.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $stockOpname->update([
                'opname_date' => $validated['opname_date'],
                'location_id' => $validated['location_id'],
                'notes' => $validated['notes'],
            ]);

            // Delete old details
            $stockOpname->details()->delete();

            // Create new details
            foreach ($validated['details'] as $detail) {
                $difference = $detail['actual_quantity'] - $detail['system_quantity'];

                $stockOpname->details()->create([
                    'product_id' => $detail['product_id'],
                    'system_quantity' => $detail['system_quantity'],
                    'actual_quantity' => $detail['actual_quantity'],
                    'difference_quantity' => abs($difference),
                    'adjustment_type' => $difference >= 0 ? 'increase' : 'decrease',
                    'notes' => $detail['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('stock-opnames.index')
                ->with('success', 'Stock Opname updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified stock opname.
     */
    public function destroy(StockOpname $stockOpname)
    {
        // Only draft opnames can be deleted
        if ($stockOpname->status !== 'draft') {
            return redirect()->back()->withErrors(['error' => 'Only draft stock opname can be deleted.']);
        }

        DB::beginTransaction();
        try {
            $stockOpname->details()->delete();
            $stockOpname->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Stock Opname deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Complete the specified stock opname.
     */
    public function complete(StockOpname $stockOpname)
    {
        try {
            $stockOpname->complete(auth()->id());

            return redirect()->back()->with('success', 'Stock Opname completed successfully. Adjustments have been created for discrepancies.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get all products with current stock for a location.
     */
    public function getProductsForOpname(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $products = Product::active()
            ->orderBy('product_code')
            ->get()
            ->map(function ($product) use ($validated) {
                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'system_quantity' => $product->getCurrentStock($validated['location_id']),
                    'actual_quantity' => 0,
                ];
            });

        return response()->json($products);
    }

    /**
     * Cancel the specified stock opname.
     */
    public function cancel(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'completed') {
            return redirect()->back()->withErrors(['error' => 'Only completed stock opname can be cancelled.']);
        }

        DB::beginTransaction();
        try {
            // Find and cancel related adjustments
            $adjustments = StockAdjustment::where('reference_number', $stockOpname->opname_number)->get();

            foreach ($adjustments as $adjustment) {
                if ($adjustment->status == 'approved') {
                    $adjustment->stockCard()->delete();
                }
                $adjustment->update(['status' => 'cancelled']);
            }

            $stockOpname->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->back()->with('success', 'Stock Opname and related adjustments cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
