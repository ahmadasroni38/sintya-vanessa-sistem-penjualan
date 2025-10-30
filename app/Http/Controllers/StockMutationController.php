<?php

namespace App\Http\Controllers;

use App\Models\StockMutation;
use App\Models\StockMutationDetail;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMutationController extends Controller
{
    /**
     * Display a listing of stock mutations.
     */
    public function index(Request $request)
    {
        $query = StockMutation::query()
            ->with(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter', 'approver', 'completer'])
            ->withCount('details');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by from location
        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        // Filter by to location
        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
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

        // Search by transaction number, reference, or product
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('details.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_code', 'like', "%{$search}%")
                            ->orWhere('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'transaction_date');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['transaction_date', 'transaction_number', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('transaction_date', 'desc')
                ->orderBy('transaction_number', 'desc');
        }

        $perPage = $request->input('per_page', 15);
        $mutations = $query->paginate($perPage);

        // Transform the data to include location names and creator name
        $transformedMutations = collect($mutations->items())->map(function ($mutation) {
            return [
                'id' => $mutation->id,
                'transaction_number' => $mutation->transaction_number,
                'transaction_date' => $mutation->transaction_date,
                'from_location_id' => $mutation->from_location_id,
                'from_location_name' => $mutation->fromLocation ? $mutation->fromLocation->name : null,
                'to_location_id' => $mutation->to_location_id,
                'to_location_name' => $mutation->toLocation ? $mutation->toLocation->name : null,
                'reference_number' => $mutation->reference_number,
                'notes' => $mutation->notes,
                'status' => $mutation->status,
                'created_by' => $mutation->creator ? $mutation->creator->name : null,
                'details_count' => $mutation->details_count,
                'total_quantity' => $mutation->total_quantity,
                'total_items' => $mutation->total_items,
                'created_at' => $mutation->created_at,
                'updated_at' => $mutation->updated_at,
                'submitted_by' => $mutation->submitted_by,
                'submitted_at' => $mutation->submitted_at,
                'approved_by' => $mutation->approved_by,
                'approved_at' => $mutation->approved_at,
                'completed_by' => $mutation->completed_by,
                'completed_at' => $mutation->completed_at,
                'rejection_reason' => $mutation->rejection_reason,
            ];
        });

        // Create a new pagination object with transformed data
        $transformedPagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedMutations,
            $mutations->total(),
            $mutations->perPage(),
            $mutations->currentPage(),
            [
                'path' => $mutations->path(),
                'pageName' => 'page',
            ]
        );

        return response()->json($transformedPagination);
    }

    /**
     * Get dropdown options for stock mutation form.
     */
    public function options(Request $request)
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

        // If from_location_id is provided, get available stock for products
        if ($request->filled('from_location_id')) {
            $fromLocationId = $request->from_location_id;

            $products = $products->map(function ($product) use ($fromLocationId) {
                $lastCard = StockCard::where('product_id', $product->id)
                    ->where('location_id', $fromLocationId)
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $product->available_stock = $lastCard ? $lastCard->balance : 0;
                return $product;
            });
        }

        return response()->json([
            'products' => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Get available stock for a specific product at a location.
     */
    public function checkStock(Request $request)
    {
        $productId = $request->input('product_id');
        $locationId = $request->input('location_id');

        $lastCard = StockCard::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $availableStock = $lastCard ? $lastCard->balance : 0;

        return response()->json([
            'available_stock' => $availableStock,
        ]);
    }

    /**
     * Store a newly created stock mutation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create stock mutation header
            $mutation = StockMutation::create([
                'transaction_date' => $request->transaction_date,
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create stock mutation details with available stock check
            foreach ($request->items as $item) {
                // Get available stock at from_location
                $lastCard = StockCard::where('product_id', $item['product_id'])
                    ->where('location_id', $request->from_location_id)
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $availableStock = $lastCard ? $lastCard->balance : 0;

                $mutation->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'available_stock' => $availableStock,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            $mutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator']);

            return response()->json([
                'message' => 'Stock Mutation created successfully.',
                'data' => $mutation,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create stock mutation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified stock mutation.
     */
    public function show(StockMutation $stockMutation)
    {
        $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter', 'approver', 'completer']);

        return response()->json($stockMutation);
    }

    /**
     * Update the specified stock mutation.
     */
    public function update(Request $request, StockMutation $stockMutation)
    {
        // Only draft mutations can be updated
        if ($stockMutation->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft mutations can be updated.',
            ], 422);
        }

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update stock mutation header
            $stockMutation->update([
                'transaction_date' => $request->transaction_date,
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            // Delete old details
            $stockMutation->details()->delete();

            // Create new details
            foreach ($request->items as $item) {
                // Get available stock at from_location
                $lastCard = StockCard::where('product_id', $item['product_id'])
                    ->where('location_id', $request->from_location_id)
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $availableStock = $lastCard ? $lastCard->balance : 0;

                $stockMutation->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'available_stock' => $availableStock,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator']);

            return response()->json([
                'message' => 'Stock Mutation updated successfully.',
                'data' => $stockMutation,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update stock mutation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified stock mutation.
     */
    public function destroy(StockMutation $stockMutation)
    {
        // Only draft mutations can be deleted
        if ($stockMutation->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft mutations can be deleted.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete details first (though cascade will handle it)
            $stockMutation->details()->delete();

            // Delete the stock mutation (soft delete)
            $stockMutation->delete();

            DB::commit();

            return response()->json([
                'message' => 'Stock Mutation deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete stock mutation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit the specified stock mutation for approval.
     */
    public function submit(StockMutation $stockMutation)
    {
        try {
            $stockMutation->submit(auth()->id());
            $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter']);

            return response()->json([
                'message' => 'Stock Mutation submitted successfully.',
                'data' => $stockMutation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Approve the specified stock mutation.
     */
    public function approve(StockMutation $stockMutation)
    {
        try {
            $stockMutation->approve(auth()->id());
            $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter', 'approver']);

            return response()->json([
                'message' => 'Stock Mutation approved successfully.',
                'data' => $stockMutation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Complete the specified stock mutation.
     */
    public function complete(StockMutation $stockMutation)
    {
        try {
            $stockMutation->complete(auth()->id());
            $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter', 'approver', 'completer']);

            return response()->json([
                'message' => 'Stock Mutation completed successfully.',
                'data' => $stockMutation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel the specified stock mutation.
     */
    public function cancel(Request $request, StockMutation $stockMutation)
    {
        try {
            $reason = $request->input('reason');
            $stockMutation->cancel($reason);
            $stockMutation->load(['details.product.unit', 'fromLocation', 'toLocation', 'creator', 'submitter', 'approver']);

            return response()->json([
                'message' => 'Stock Mutation cancelled successfully.',
                'data' => $stockMutation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get statistics for stock mutations.
     */
    public function statistics(Request $request)
    {
        $query = StockMutation::query();

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        // Get total for current month
        $currentMonthQuery = StockMutation::query()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year);

        $stats = [
            'total_this_month' => $currentMonthQuery->count(),
            'total_transactions' => (clone $query)->count(),
            'draft_count' => (clone $query)->where('status', 'draft')->count(),
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'approved_count' => (clone $query)->where('status', 'approved')->count(),
            'completed_count' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_count' => (clone $query)->where('status', 'cancelled')->count(),
            'total_items' => (clone $query)->where('status', 'completed')->withCount('details')->get()->sum('details_count'),
        ];

        return response()->json($stats);
    }
}
