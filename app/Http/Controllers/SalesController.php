<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Location;
use App\Models\Customer;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of sales transactions.
     */
    public function index(Request $request)
    {
        $query = Sale::query()
            ->with(['details.product.unit', 'location', 'customer', 'creator', 'poster'])
            ->withCount('details');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
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

        // Search by transaction number, customer, or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('customer_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details.product', function ($productQuery) use ($search) {
                        $productQuery->where('product_code', 'like', "%{$search}%")
                            ->orWhere('product_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'transaction_date');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['transaction_date', 'transaction_number', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('transaction_date', 'desc')
                ->orderBy('transaction_number', 'desc');
        }

        $perPage = $request->input('per_page', 15);
        $sales = $query->paginate($perPage);

        return response()->json($sales);
    }

    /**
     * Get dropdown options for sale form.
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

        $customers = Customer::active()
            ->select('id', 'customer_code', 'customer_name')
            ->orderBy('customer_code')
            ->get();

        return response()->json([
            'products' => $products,
            'locations' => $locations,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created sale.
     */
    public function store(StoreSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create sale header
            $sale = Sale::create([
                'transaction_date' => $request->transaction_date,
                'customer_id' => $request->customer_id,
                'location_id' => $request->location_id,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => $request->paid_amount ?? 0,
                'change_amount' => $request->change_amount ?? 0,
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create sale details
            foreach ($request->items as $item) {
                $sale->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'tax_percent' => $item['tax_percent'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $sale->calculateTotal();

            DB::commit();

            $sale->load(['details.product.unit', 'location', 'customer', 'creator']);

            return response()->json([
                'message' => 'Sale created successfully.',
                'data' => $sale,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load(['details.product.unit', 'location', 'customer', 'creator', 'poster']);

        return response()->json($sale);
    }

    /**
     * Update the specified sale.
     */
    public function update(StoreSaleRequest $request, Sale $sale)
    {
        // Only draft sales can be updated
        if ($sale->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft sales can be updated.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update sale header
            $sale->update([
                'transaction_date' => $request->transaction_date,
                'customer_id' => $request->customer_id,
                'location_id' => $request->location_id,
                'paid_amount' => $request->paid_amount ?? 0,
                'change_amount' => $request->change_amount ?? 0,
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
            ]);

            // Delete old details
            $sale->details()->delete();

            // Create new details
            foreach ($request->items as $item) {
                $sale->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'tax_percent' => $item['tax_percent'] ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Calculate totals
            $sale->calculateTotal();

            DB::commit();

            $sale->load(['details.product.unit', 'location', 'customer', 'creator', 'poster']);

            return response()->json([
                'message' => 'Sale updated successfully.',
                'data' => $sale,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        // Only draft sales can be deleted
        if ($sale->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft sales can be deleted.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete details first (though cascade will handle it)
            $sale->details()->delete();

            // Delete the sale (soft delete)
            $sale->delete();

            DB::commit();

            return response()->json([
                'message' => 'Sale deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Post the specified sale.
     */
    public function post(Sale $sale)
    {
        try {
            $sale->post(auth()->id());
            $sale->load(['details.product.unit', 'location', 'customer', 'creator', 'poster']);

            return response()->json([
                'message' => 'Sale posted successfully.',
                'data' => $sale,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel the specified sale.
     */
    public function cancel(Sale $sale)
    {
        if ($sale->status !== 'posted') {
            return response()->json([
                'message' => 'Only posted sales can be cancelled.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Remove all stock card entries for this transaction
            \App\Models\StockCard::where('transaction_type', 'sale')
                ->where('reference_id', $sale->id)
                ->delete();

            // Remove journal entry
            \App\Models\JournalEntry::where('reference_type', 'sale')
                ->where('reference_id', $sale->id)
                ->delete();

            // Update status
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            $sale->load(['details.product.unit', 'location', 'customer', 'creator', 'poster']);

            return response()->json([
                'message' => 'Sale cancelled successfully.',
                'data' => $sale,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get statistics for sales.
     */
    public function statistics(Request $request)
    {
        $query = Sale::query();

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
            'total_revenue' => (clone $query)->where('status', 'posted')->sum('total_amount'),
            'total_customers' => (clone $query)->where('status', 'posted')->distinct('customer_id')->count('customer_id'),
        ];

        return response()->json($stats);
    }

    /**
     * Export sales to CSV
     */
    public function export(Request $request)
    {
        $query = Sale::query()
            ->with(['customer', 'location', 'creator', 'poster']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('customer_code', 'like', "%{$search}%");
                    });
            });
        }

        // Export specific IDs if provided
        if ($request->filled('ids')) {
            $query->whereIn('id', $request->ids);
        }

        $query->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_number', 'desc');

        return Sale::exportToCSV($query);
    }

    /**
     * Bulk post sales
     */
    public function bulkPost(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:sales,id',
        ]);

        DB::beginTransaction();
        try {
            $posted = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                try {
                    $sale = Sale::findOrFail($id);

                    if ($sale->status !== 'draft') {
                        $failed[] = [
                            'id' => $id,
                            'number' => $sale->transaction_number,
                            'reason' => 'Only draft sales can be posted',
                        ];
                        continue;
                    }

                    $sale->post(auth()->id());
                    $posted++;
                } catch (\Exception $e) {
                    $failed[] = [
                        'id' => $id,
                        'number' => $sale->transaction_number ?? 'Unknown',
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'message' => "{$posted} sale(s) posted successfully",
                'posted' => $posted,
                'failed' => $failed,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk posting failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete sales
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:sales,id',
        ]);

        DB::beginTransaction();
        try {
            $deleted = 0;
            $failed = [];

            foreach ($validated['ids'] as $id) {
                $sale = Sale::findOrFail($id);

                if ($sale->status !== 'draft') {
                    $failed[] = [
                        'id' => $id,
                        'number' => $sale->transaction_number,
                        'reason' => 'Only draft sales can be deleted',
                    ];
                    continue;
                }

                $sale->details()->delete();
                $sale->delete();
                $deleted++;
            }

            DB::commit();

            return response()->json([
                'message' => "{$deleted} sale(s) deleted successfully",
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
}
