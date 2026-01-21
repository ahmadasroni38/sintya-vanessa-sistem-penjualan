<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockCard;
use App\Models\StockBalance;
use App\Models\Sale;
use App\Models\SaleDetail;
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
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('search')) {
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
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($productQuery) use ($search) {
                $productQuery->where('product_code', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status_filter') && $request->status_filter !== 'all') {
            $statusFilter = $request->status_filter;
            $query->where('status', $statusFilter);
        }

        $balances = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Current balances retrieved successfully',
            'data' => [
                'balances' => $balances,
            ],
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
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('location_id')) {
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
        if ($request->filled('export_type') && $request->export_type === 'current_view') {
            // Apply current view filters
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }

            if ($request->filled('transaction_type')) {
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

    /**
     * Get best selling products
     */
    public function getBestSelling(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $request->get('limit', 10);

        $bestSelling = SaleDetail::select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT sale_details.sale_id) as transaction_count'),
                DB::raw('SUM(sale_details.total_price) as total_sales')
            )
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->where('sales.status', 'posted')
            ->whereNull('sales.deleted_at')
            ->whereBetween('sales.transaction_date', [$request->start_date, $request->end_date])
            ->groupBy('sale_details.product_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);
                return [
                    'product_id' => $item->product_id,
                    'product_code' => $product->product_code ?? '',
                    'product_name' => $product->product_name ?? '',
                    'total_quantity' => (float) $item->total_quantity,
                    'transaction_count' => (int) $item->transaction_count,
                    'total_sales' => (float) $item->total_sales,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Best selling products retrieved successfully',
            'data' => $bestSelling,
        ]);
    }

    /**
     * Get slow moving products (least sold or not sold)
     */
    public function getSlowMoving(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $request->get('limit', 10);

        // Get all active products with their sales data
        $salesData = SaleDetail::select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT sale_details.sale_id) as transaction_count'),
                DB::raw('SUM(sale_details.total_price) as total_sales')
            )
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'posted')
            ->whereNull('sales.deleted_at')
            ->whereBetween('sales.transaction_date', [$request->start_date, $request->end_date])
            ->groupBy('sale_details.product_id')
            ->get()
            ->keyBy('product_id');

        // Get all active products
        $products = Product::where('is_active', true)
            ->select('id', 'product_code', 'product_name')
            ->get();

        // Combine with stock balance data
        $slowMoving = $products->map(function ($product) use ($salesData) {
            $sales = $salesData->get($product->id);
            $currentStock = StockBalance::where('product_id', $product->id)
                ->sum('current_balance');

            return [
                'product_id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'current_stock' => (float) $currentStock,
                'total_quantity' => $sales ? (float) $sales->total_quantity : 0,
                'transaction_count' => $sales ? (int) $sales->transaction_count : 0,
                'total_sales' => $sales ? (float) $sales->total_sales : 0,
            ];
        })
        ->sortBy('total_quantity')
        ->take($limit)
        ->values();

        return response()->json([
            'status' => true,
            'message' => 'Slow moving products retrieved successfully',
            'data' => $slowMoving,
        ]);
    }

    /**
     * Get sales recap grouped by period, category, or location
     */
    public function getSalesRecap(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'nullable|in:daily,weekly,monthly,category,location',
        ]);

        $groupBy = $request->get('group_by', 'daily');

        switch ($groupBy) {
            case 'daily':
                $salesRecap = DB::table('sales')
                    ->selectRaw('DATE(transaction_date) as group_key, COUNT(*) as transaction_count, SUM(total_amount) as total_sales')
                    ->where('status', 'posted')
                    ->whereNull('deleted_at')
                    ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
                    ->groupByRaw('DATE(transaction_date)')
                    ->orderByRaw('DATE(transaction_date) asc')
                    ->get();

                // Calculate total quantity from details
                $salesRecap = $salesRecap->map(function ($item) {
                    $totalQty = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'posted')
                        ->whereNull('sales.deleted_at')
                        ->whereDate('sales.transaction_date', $item->group_key)
                        ->sum('sale_details.quantity');

                    return [
                        'group_key' => $item->group_key,
                        'group_label' => date('d M Y', strtotime($item->group_key)),
                        'transaction_count' => (int) $item->transaction_count,
                        'total_quantity' => (float) $totalQty,
                        'total_sales' => (float) $item->total_sales,
                    ];
                });
                break;

            case 'weekly':
                $salesRecap = DB::table('sales')
                    ->selectRaw('YEARWEEK(transaction_date, 1) as group_key, MIN(transaction_date) as week_start, COUNT(*) as transaction_count, SUM(total_amount) as total_sales')
                    ->where('status', 'posted')
                    ->whereNull('deleted_at')
                    ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
                    ->groupByRaw('YEARWEEK(transaction_date, 1)')
                    ->orderByRaw('YEARWEEK(transaction_date, 1) asc')
                    ->get();

                $salesRecap = $salesRecap->map(function ($item) {
                    $weekStart = date('d M Y', strtotime($item->week_start));
                    $weekEnd = date('d M Y', strtotime($item->week_start . ' +6 days'));

                    $totalQty = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'posted')
                        ->whereNull('sales.deleted_at')
                        ->whereRaw('YEARWEEK(sales.transaction_date, 1) = ?', [$item->group_key])
                        ->sum('sale_details.quantity');

                    return [
                        'group_key' => $item->group_key,
                        'group_label' => $weekStart . ' - ' . $weekEnd,
                        'transaction_count' => (int) $item->transaction_count,
                        'total_quantity' => (float) $totalQty,
                        'total_sales' => (float) $item->total_sales,
                    ];
                });
                break;

            case 'monthly':
                $salesRecap = DB::table('sales')
                    ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as group_key, COUNT(*) as transaction_count, SUM(total_amount) as total_sales')
                    ->where('status', 'posted')
                    ->whereNull('deleted_at')
                    ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
                    ->groupByRaw('DATE_FORMAT(transaction_date, "%Y-%m")')
                    ->orderByRaw('DATE_FORMAT(transaction_date, "%Y-%m") asc')
                    ->get();

                $salesRecap = $salesRecap->map(function ($item) {
                    $totalQty = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'posted')
                        ->whereNull('sales.deleted_at')
                        ->whereRaw('DATE_FORMAT(sales.transaction_date, "%Y-%m") = ?', [$item->group_key])
                        ->sum('sale_details.quantity');

                    return [
                        'group_key' => $item->group_key,
                        'group_label' => date('F Y', strtotime($item->group_key . '-01')),
                        'transaction_count' => (int) $item->transaction_count,
                        'total_quantity' => (float) $totalQty,
                        'total_sales' => (float) $item->total_sales,
                    ];
                });
                break;

            case 'category':
                $salesRecap = SaleDetail::select(
                        'products.category_id as group_key',
                        'product_categories.category_name as group_label',
                        DB::raw('COUNT(DISTINCT sale_details.sale_id) as transaction_count'),
                        DB::raw('SUM(sale_details.quantity) as total_quantity'),
                        DB::raw('SUM(sale_details.total_price) as total_sales')
                    )
                    ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
                    ->join('products', 'sale_details.product_id', '=', 'products.id')
                    ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
                    ->where('sales.status', 'posted')
                    ->whereNull('sales.deleted_at')
                    ->whereBetween('sales.transaction_date', [$request->start_date, $request->end_date])
                    ->groupBy('products.category_id', 'product_categories.category_name')
                    ->orderByDesc('total_sales')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'group_key' => $item->group_key ?? 'uncategorized',
                            'group_label' => $item->group_label ?? 'Tanpa Kategori',
                            'transaction_count' => (int) $item->transaction_count,
                            'total_quantity' => (float) $item->total_quantity,
                            'total_sales' => (float) $item->total_sales,
                        ];
                    });
                break;

            case 'location':
                $salesRecap = DB::table('sales')
                    ->selectRaw('location_id as group_key, COUNT(*) as transaction_count, SUM(total_amount) as total_sales')
                    ->where('status', 'posted')
                    ->whereNull('deleted_at')
                    ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
                    ->groupBy('location_id')
                    ->orderByDesc('total_sales')
                    ->get();

                $salesRecap = $salesRecap->map(function ($item) use ($request) {
                    $location = Location::find($item->group_key);

                    $totalQty = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->where('sales.status', 'posted')
                        ->whereNull('sales.deleted_at')
                        ->where('sales.location_id', $item->group_key)
                        ->whereBetween('sales.transaction_date', [$request->start_date, $request->end_date])
                        ->sum('sale_details.quantity');

                    return [
                        'group_key' => $item->group_key,
                        'group_label' => $location ? $location->name : 'Unknown Location',
                        'transaction_count' => (int) $item->transaction_count,
                        'total_quantity' => (float) $totalQty,
                        'total_sales' => (float) $item->total_sales,
                    ];
                });
                break;
        }

        return response()->json([
            'status' => true,
            'message' => 'Sales recap retrieved successfully',
            'data' => $salesRecap,
        ]);
    }
}
