<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Product;
use App\Models\Location;
use App\Http\Requests\StoreStockInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    /**
     * Display a listing of stock in transactions.
     */
    public function index(Request $request)
    {
        $query = StockIn::query()
            ->with(['details.product.unit', 'location', 'creator', 'poster'])
            ->withCount('details');

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
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        // Search by transaction number, supplier, or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('details.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_code', 'like', "%{$search}%")
                            ->orWhere('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'transaction_date');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['transaction_date', 'transaction_number', 'total_price', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('transaction_date', 'desc')
                ->orderBy('transaction_number', 'desc');
        }

        $perPage = $request->input('per_page', 15);
        $stockIns = $query->paginate($perPage);

        return response()->json($stockIns);
    }

    /**
     * Get dropdown options for stock in form.
     */
    public function options()
    {
        $products = Product::active()
            ->with('unit')
            ->select('id', 'product_code', 'product_name', 'unit_id')
            ->orderBy('product_code')
            ->get();

        $locations = Location::active()
            ->select('id', 'code', 'name')
            ->orderBy('code')
            ->get();

        return response()->json([
            'products' => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created stock in.
     */
    public function store(StoreStockInRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create stock in header
            $stockIn = StockIn::create([
                'transaction_date' => $request->transaction_date,
                'location_id' => $request->location_id,
                'supplier_name' => $request->supplier_name,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'total_price' => 0, // Will be calculated from details
            ]);

            // Create stock in details
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $detail = $stockIn->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
                $totalPrice += $detail->total_price;
            }

            // Update total price
            $stockIn->update(['total_price' => $totalPrice]);

            DB::commit();

            $stockIn->load(['details.product.unit', 'location', 'creator']);

            return response()->json([
                'message' => 'Stock In created successfully.',
                'data' => $stockIn,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create stock in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified stock in.
     */
    public function show(StockIn $stockIn)
    {
        $stockIn->load(['details.product.unit', 'location', 'creator', 'poster']);

        return response()->json($stockIn);
    }

    /**
     * Update the specified stock in.
     */
    public function update(StoreStockInRequest $request, StockIn $stockIn)
    {
        // Only draft stock ins can be updated
        if ($stockIn->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft stock in can be updated.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update stock in header
            $stockIn->update([
                'transaction_date' => $request->transaction_date,
                'location_id' => $request->location_id,
                'supplier_name' => $request->supplier_name,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            // Delete old details
            $stockIn->details()->delete();

            // Create new details
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $detail = $stockIn->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                ]);
                $totalPrice += $detail->total_price;
            }

            // Update total price
            $stockIn->update(['total_price' => $totalPrice]);

            DB::commit();

            $stockIn->load(['details.product.unit', 'location', 'creator', 'poster']);

            return response()->json([
                'message' => 'Stock In updated successfully.',
                'data' => $stockIn,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update stock in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified stock in.
     */
    public function destroy(StockIn $stockIn)
    {
        // Only draft stock ins can be deleted
        if ($stockIn->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft stock in can be deleted.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete details first (though cascade will handle it)
            $stockIn->details()->delete();

            // Delete the stock in (soft delete)
            $stockIn->delete();

            DB::commit();

            return response()->json([
                'message' => 'Stock In deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete stock in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Post the specified stock in.
     */
    public function post(StockIn $stockIn)
    {
        try {
            $stockIn->post(auth()->id());
            $stockIn->load(['details.product.unit', 'location', 'creator', 'poster']);

            return response()->json([
                'message' => 'Stock In posted successfully.',
                'data' => $stockIn,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel the specified stock in.
     */
    public function cancel(StockIn $stockIn)
    {
        if ($stockIn->status !== 'posted') {
            return response()->json([
                'message' => 'Only posted stock in can be cancelled.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Remove all stock card entries for this transaction
            \App\Models\StockCard::where('transaction_type', 'stock_in')
                ->where('reference_id', $stockIn->id)
                ->delete();

            // Update status
            $stockIn->update(['status' => 'cancelled']);

            DB::commit();

            $stockIn->load(['details.product.unit', 'location', 'creator', 'poster']);

            return response()->json([
                'message' => 'Stock In cancelled successfully.',
                'data' => $stockIn,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get statistics for stock in.
     */
    public function statistics(Request $request)
    {
        $query = StockIn::query();

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $stats = [
            'total_transactions' => (clone $query)->count(),
            'draft_count' => (clone $query)->where('status', 'draft')->count(),
            'posted_count' => (clone $query)->where('status', 'posted')->count(),
            'cancelled_count' => (clone $query)->where('status', 'cancelled')->count(),
            'total_items' => (clone $query)->where('status', 'posted')->withCount('details')->get()->sum('details_count'),
            'total_value' => (clone $query)->where('status', 'posted')->sum('total_price'),
        ];

        return response()->json($stats);
    }
}
