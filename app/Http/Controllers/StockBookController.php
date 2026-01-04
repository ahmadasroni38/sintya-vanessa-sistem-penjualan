<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockCard;
use App\Models\StockBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockBookExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockBookController extends Controller
{
    /**
     * Get all stock cards with filters
     */
    public function index(Request $request)
    {
        $query = StockCard::with(['product', 'location']);

        // Apply filters
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('product_code', 'like', "%{$search}%")
                        ->orWhere('product_name', 'like', "%{$search}%");
                })
                ->orWhereHas('location', function ($locationQuery) use ($search) {
                    $locationQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Order by date descending
        $query->orderBy('transaction_date', 'desc');

        // Check if pagination is disabled
        if ($request->has('paginate') && $request->paginate === 'false') {
            $stockCards = $query->get();
            return response()->json([
                'data' => $stockCards,
                'total' => $stockCards->count(),
                'per_page' => $stockCards->count(),
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => $stockCards->count(),
            ]);
        }

        // Paginate results
        $perPage = $request->get('per_page', 50);
        $page = $request->get('page', 1);
        $stockCards = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($stockCards);
    }

    /**
     * Get products that have stock
     */
    public function getProductsWithStock()
    {
        $products = Product::whereHas('stockCards')
            ->orWhereHas('stockBalances')
            ->select(['id', 'product_code', 'product_name'])
            ->orderBy('product_name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Products with stock retrieved successfully',
            'data' => $products,
        ]);
    }

    /**
     * Get locations that have stock
     */
    public function getLocationsWithStock()
    {
        $locations = Location::whereHas('stockCards')
            ->orWhereHas('stockBalances')
            ->select(['id', 'code', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Locations with stock retrieved successfully',
            'data' => $locations,
        ]);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $totalTransactions = StockCard::count();
        $totalIn = StockCard::sum('quantity_in');
        $totalOut = StockCard::sum('quantity_out');
        $currentBalance = StockBalance::sum('current_balance');

        // Calculate trends (compare with previous period)
        $previousPeriodStart = now()->subDays(30)->format('Y-m-d');
        $previousPeriodEnd = now()->subDays(1)->format('Y-m-d');

        $previousTransactions = StockCard::whereBetween('transaction_date', [$previousPeriodStart, $previousPeriodEnd])->count();
        $transactionsTrend = $previousTransactions > 0 ? (($totalTransactions - $previousTransactions) / $previousTransactions) * 100 : ($totalTransactions > 0 ? 100.0 : 0.0);

        return response()->json([
            'total_transactions' => $totalTransactions,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'current_balance' => $currentBalance,
            'transactions_trend' => round($transactionsTrend, 2),
            'total_products_with_stock' => Product::whereHas('stockBalances')->count(),
            'total_locations_with_stock' => Location::whereHas('stockBalances')->count(),
            'average_daily_movement' => $totalTransactions > 0 ? round($totalTransactions / 30, 2) : 0,
        ]);
    }

    /**
     * Get ledger data for specific product and location
     */
    public function getLedger(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:locations,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = StockCard::with(['product', 'location'])
            ->where('product_id', $request->product_id)
            ->where('location_id', $request->location_id);

        // Apply date range if provided
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Order by date
        $query->orderBy('transaction_date', 'asc');

        // Get opening balance
        $openingBalance = 0;
        if ($request->has('start_date')) {
            $openingBalance = StockCard::where('product_id', $request->product_id)
                ->where('location_id', $request->location_id)
                ->whereDate('transaction_date', '<', $request->start_date)
                ->orderBy('transaction_date', 'desc')
                ->value('balance') ?? 0;
        }

        // Get transactions
        $perPage = $request->get('per_page', 50);
        $transactions = $query->paginate($perPage);

        // Calculate summary
        $totalIn = $query->sum('quantity_in');
        $totalOut = $query->sum('quantity_out');
        $endingBalance = $openingBalance + $totalIn - $totalOut;

        return response()->json([
            'status' => true,
            'message' => 'Ledger data retrieved successfully',
            'data' => [
                'opening_balance' => $openingBalance,
                'transactions' => $transactions->items(),
                'summary' => [
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'ending_balance' => $endingBalance,
                ],
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
                ],
            ]
        ]);
    }

    /**
     * Get current balances
     */
    public function getCurrentBalances(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        $query = StockBalance::with(['product'])
            ->where('location_id', $request->location_id);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($productQuery) use ($search) {
                $productQuery->where('product_code', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status_filter') && $request->status_filter !== 'all') {
            $statusFilter = $request->status_filter;
            $query->where('status', $statusFilter);
        }

        $balances = $query->get();

        return response()->json([
            'balances' => $balances,
        ]);
    }

    /**
     * Get movement summary
     */
    public function getMovementSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $query = StockCard::with(['product', 'location']);

        // Apply filters
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Apply date range
        $query->whereDate('transaction_date', '>=', $request->start_date);
        $query->whereDate('transaction_date', '<=', $request->end_date);

        // Group by product and location
        $movements = $query->get()
            ->groupBy(function ($item) {
                return $item->product_id . '_' . $item->location_id;
            })
            ->map(function ($group) {
                $firstItem = $group->first();
                $totalIn = $group->sum('quantity_in');
                $totalOut = $group->sum('quantity_out');

                return [
                    'product_id' => $firstItem->product_id,
                    'product_code' => $firstItem->product->product_code,
                    'product_name' => $firstItem->product->product_name,
                    'location_id' => $firstItem->location_id,
                    'location_code' => $firstItem->location->code,
                    'location_name' => $firstItem->location->name,
                    'opening_balance' => $firstItem->balance - $totalIn + $totalOut,
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'ending_balance' => $firstItem->balance,
                    'net_change' => $totalIn - $totalOut,
                ];
            })
            ->values();

        return response()->json($movements);
    }

    /**
     * Export stock book data
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
        ]);

        $query = StockCard::with(['product', 'location']);

        // Apply filters
        if ($request->has('export_type') && $request->export_type === 'current_view') {
            // Apply current view filters
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            if ($request->has('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }

            if ($request->has('transaction_type')) {
                $query->where('transaction_type', $request->transaction_type);
            }
        }

        // Order by date
        $query->orderBy('transaction_date', 'desc');

        // Get data
        $stockCards = $query->get();

        // Prepare export data
        $exportData = $stockCards->map(function ($card) {
            return [
                'Date' => $card->transaction_date,
                'Product Code' => $card->product->product_code,
                'Product Name' => $card->product->product_name,
                'Location' => $card->location->name,
                'Transaction Type' => $card->transaction_type,
                'Reference' => $card->reference_number,
                'Quantity In' => $card->quantity_in,
                'Quantity Out' => $card->quantity_out,
                'Balance' => $card->balance,
                'Created By' => $card->created_by,
                'Notes' => $card->notes,
            ];
        })->toArray();

        // Add summary if requested
        if ($request->has('include_summary') && $request->include_summary) {
            $summary = [
                'Total Transactions' => count($exportData),
                'Total Stock In' => array_sum(array_column($exportData, 'Quantity In')),
                'Total Stock Out' => array_sum(array_column($exportData, 'Quantity Out')),
                'Current Balance' => end($exportData)['Balance'] ?? 0,
            ];

            // Add empty rows for spacing
            array_push($exportData, []);
            array_push($exportData, []);
            array_push($exportData, ['Summary', '', '', '', '', '', '', '', '', '']);

            foreach ($summary as $key => $value) {
                array_push($exportData, [$key, $value]);
            }
        }

        // Add headers if requested
        if ($request->has('include_headers') && $request->include_headers) {
            $headers = array_keys($exportData[0]);
            array_unshift($exportData, $headers);
        }

        $filename = 'stock-book-' . now()->format('Y-m-d') . '.' . $request->format;

        if ($request->format === 'xlsx') {
            return Excel::download(new StockBookExport($exportData), $filename);
        } elseif ($request->format === 'csv') {
            $csv = '';

            // Add headers if requested
            if ($request->has('include_headers') && $request->include_headers) {
                $csv .= implode(',', array_keys($exportData[0])) . "\n";
            }

            foreach ($exportData as $row) {
                $csv .= implode(',', $row) . "\n";
            }

            return new StreamedResponse(
                $csv,
                200,
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        } elseif ($request->format === 'pdf') {
            // For PDF, you would typically use a package like DomPDF or TCPDF
            // This is a placeholder implementation
            return response()->json(['error' => 'PDF export not implemented yet']);
        }
    }
}
