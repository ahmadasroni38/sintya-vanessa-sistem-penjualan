<?php

namespace App\Http\Controllers;

use App\Models\StockCard;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockCardController extends Controller
{
    /**
     * Display stock card (Buku Stock) report.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'location_id' => 'nullable|exists:locations,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = StockCard::query()->with(['product', 'location']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        // Order by transaction date and created_at
        $query->orderBy('transaction_date')
            ->orderBy('created_at');

        $stockCards = $query->paginate($request->per_page ?? 50);

        $products = Product::active()->orderBy('product_code')->get();
        $locations = Location::active()->orderBy('code')->get();

        return Inertia::render('Dashboard/StockCard', [
            'stock_cards' => $stockCards,
            'products' => $products,
            'locations' => $locations,
            'filters' => $request->only(['product_id', 'location_id', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Get stock card details for a specific product and location.
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $product = Product::with('category', 'unit')->findOrFail($validated['product_id']);
        $location = Location::findOrFail($validated['location_id']);

        $query = StockCard::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']]);
        }

        $stockCards = $query->orderBy('transaction_date')
            ->orderBy('created_at')
            ->get();

        // Calculate opening balance (balance before start_date if date filter applied)
        $openingBalance = 0;
        if ($request->filled('start_date')) {
            $lastCardBeforeStart = StockCard::where('product_id', $validated['product_id'])
                ->where('location_id', $validated['location_id'])
                ->where('transaction_date', '<', $validated['start_date'])
                ->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            $openingBalance = $lastCardBeforeStart ? $lastCardBeforeStart->balance : 0;
        }

        // Calculate summary
        $totalIn = $stockCards->sum('quantity_in');
        $totalOut = $stockCards->sum('quantity_out');
        $currentBalance = $stockCards->last()->balance ?? $openingBalance;

        return Inertia::render('Dashboard/StockCardDetail', [
            'product' => $product,
            'location' => $location,
            'stock_cards' => $stockCards,
            'opening_balance' => $openingBalance,
            'summary' => [
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_balance' => $currentBalance,
            ],
            'period' => [
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ],
        ]);
    }

    /**
     * Get stock movement summary grouped by transaction type.
     */
    public function summary(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'location_id' => 'nullable|exists:locations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $query = StockCard::query()
            ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']]);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Group by transaction type
        $summary = $query->selectRaw('
            transaction_type,
            COUNT(*) as transaction_count,
            SUM(quantity_in) as total_in,
            SUM(quantity_out) as total_out
        ')
        ->groupBy('transaction_type')
        ->get();

        return response()->json([
            'summary' => $summary,
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
        ]);
    }

    /**
     * Export stock card to Excel/PDF (placeholder).
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        // TODO: Implement Excel/PDF export
        // This is a placeholder for future implementation

        return redirect()->back()->with('info', 'Export feature will be implemented in the next phase.');
    }

    /**
     * Get current stock balance for all products in a location.
     */
    public function balances(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $products = Product::active()
            ->orderBy('product_code')
            ->get()
            ->map(function ($product) use ($validated) {
                $currentStock = $product->getCurrentStock($validated['location_id']);

                return [
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'current_stock' => $currentStock,
                    'minimum_stock' => $product->minimum_stock,
                    'maximum_stock' => $product->maximum_stock,
                    'is_below_minimum' => $product->isBelowMinimum($validated['location_id']),
                    'is_above_maximum' => $product->isAboveMaximum($validated['location_id']),
                ];
            });

        $location = Location::findOrFail($validated['location_id']);

        return Inertia::render('Dashboard/StockBalances', [
            'location' => $location,
            'products' => $products,
        ]);
    }
}
