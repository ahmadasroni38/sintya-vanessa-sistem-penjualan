<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Location;
use App\Models\Customer;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\StockCard;
use App\Models\StockBalance;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and overview data.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Base statistics (visible to all roles)
        $statistics = [
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('is_active', true)->count(),
                'inactive' => Product::where('is_active', false)->count(),
            ],
            'product_categories' => [
                'total' => ProductCategory::count(),
                'distribution' => ProductCategory::withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'name' => $category->name,
                            'count' => $category->products_count,
                        ];
                    }),
            ],
            'locations' => [
                'total' => Location::count(),
                'with_stock' => Location::has('stockCards')->count(),
                'distribution' => Location::withCount('stockCards')
                    ->orderBy('stock_cards_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($location) {
                        return [
                            'name' => $location->name,
                            'count' => $location->stock_cards_count,
                        ];
                    }),
            ],
            'customers' => [
                'total' => Customer::count(),
                'active' => Customer::active()->count(),
            ],
            'stock' => [
                'total_transactions' => StockCard::count(),
                'total_in' => StockCard::sum('quantity_in'),
                'total_out' => StockCard::sum('quantity_out'),
                'current_balance' => StockBalance::sum('current_balance'),
            ],
        ];

        // Admin-only statistics
        if ($user->hasPermission('view_users') || $user->hasPermission('manage_users')) {
            $statistics['users'] = [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'inactive' => User::where('status', 'inactive')->count(),
            ];
            $statistics['roles'] = [
                'total' => Role::count(),
            ];
            $statistics['permissions'] = [
                'total' => Permission::count(),
            ];
        }

        // Work Orders statistics (if table exists)
        if (DB::getSchemaBuilder()->hasTable('work_orders')) {
            $workOrdersQuery = DB::table('work_orders');

            // Filter by user if not admin
            if (!$user->hasPermission('manage_work_orders')) {
                $workOrdersQuery->where('assigned_to', $user->id);
            }

            $statistics['work_orders'] = [
                'total' => (clone $workOrdersQuery)->count(),
                'draft' => (clone $workOrdersQuery)->where('status', 'draft')->count(),
                'pending' => (clone $workOrdersQuery)->where('status', 'pending')->count(),
                'assigned' => (clone $workOrdersQuery)->where('status', 'assigned')->count(),
                'in_progress' => (clone $workOrdersQuery)->where('status', 'in_progress')->count(),
                'on_hold' => (clone $workOrdersQuery)->where('status', 'on_hold')->count(),
                'completed' => (clone $workOrdersQuery)->where('status', 'completed')->count(),
                'cancelled' => (clone $workOrdersQuery)->where('status', 'cancelled')->count(),
            ];
        }

        // Preventive Maintenance statistics (if table exists)
        if (DB::getSchemaBuilder()->hasTable('preventive_maintenances')) {
            $pmQuery = DB::table('preventive_maintenances');

            // Filter by user if not admin
            if (!$user->hasPermission('manage_preventive_maintenance')) {
                $pmQuery->where('assigned_to', $user->id);
            }

            $statistics['preventive_maintenance'] = [
                'total' => (clone $pmQuery)->count(),
                'scheduled' => (clone $pmQuery)->where('status', 'scheduled')->count(),
                'in_progress' => (clone $pmQuery)->where('status', 'in_progress')->count(),
                'completed' => (clone $pmQuery)->where('status', 'completed')->count(),
                'overdue' => (clone $pmQuery)
                    ->where('status', '!=', 'completed')
                    ->where('next_due_date', '<', now())
                    ->count(),
            ];
        }

        // Repair Requests statistics (if table exists)
        if (DB::getSchemaBuilder()->hasTable('repair_requests')) {
            $repairQuery = DB::table('repair_requests');

            // Filter by user if not admin
            if (!$user->hasPermission('manage_repair_requests')) {
                $repairQuery->where('requested_by', $user->id);
            }

            $statistics['repair_requests'] = [
                'total' => (clone $repairQuery)->count(),
                'pending' => (clone $repairQuery)->where('status', 'pending')->count(),
                'approved' => (clone $repairQuery)->where('status', 'approved')->count(),
                'in_progress' => (clone $repairQuery)->where('status', 'in_progress')->count(),
                'completed' => (clone $repairQuery)->where('status', 'completed')->count(),
                'rejected' => (clone $repairQuery)->where('status', 'rejected')->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get chart data for dashboard.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $user = auth()->user();
        $months = 6; // Last 6 months

        $chartData = [];

        // Work Orders trend (last 6 months)
        if (DB::getSchemaBuilder()->hasTable('work_orders')) {
            $workOrdersQuery = DB::table('work_orders')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subMonths($months))
                ->groupBy('month')
                ->orderBy('month');

            if (!$user->hasPermission('manage_work_orders')) {
                $workOrdersQuery->where('assigned_to', $user->id);
            }

            $chartData['work_orders_trend'] = $workOrdersQuery->get();
        }

        // Products by category (pie chart)
        $chartData['products_by_category'] = ProductCategory::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return [
                    'label' => $category->name,
                    'value' => $category->products_count,
                ];
            });

        // Stock transactions by type (bar chart)
        $chartData['stock_transactions_by_type'] = StockCard::select('transaction_type', DB::raw('count(*) as count'))
            ->groupBy('transaction_type')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->transaction_type)),
                    'value' => $item->count,
                ];
            });

        // Stock movement trend (line chart)
        $chartData['stock_movement_trend'] = StockCard::select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('SUM(quantity_in) as total_in'),
                DB::raw('SUM(quantity_out) as total_out')
            )
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total_in' => $item->total_in,
                    'total_out' => $item->total_out,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }

    /**
     * Get recent activity for dashboard.
     */
    public function getRecentActivity(Request $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $request->input('limit', 10);

        $activities = [];

        // Recent Work Orders
        if (DB::getSchemaBuilder()->hasTable('work_orders')) {
            $workOrdersQuery = DB::table('work_orders')
                ->select('id', 'title', 'status', 'priority', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            if (!$user->hasPermission('manage_work_orders')) {
                $workOrdersQuery->where('assigned_to', $user->id);
            }

            $activities['recent_work_orders'] = $workOrdersQuery->get();
        }

        // Recent Repair Requests
        if (DB::getSchemaBuilder()->hasTable('repair_requests')) {
            $repairQuery = DB::table('repair_requests')
                ->select('id', 'issue', 'status', 'priority', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            if (!$user->hasPermission('manage_repair_requests')) {
                $repairQuery->where('requested_by', $user->id);
            }

            $activities['recent_repair_requests'] = $repairQuery->get();
        }

        // Recent Products (Admin only)
        if ($user->hasPermission('manage_products')) {
            $activities['recent_products'] = Product::select('id', 'product_name', 'product_code', 'is_active', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        // Recent Stock Transactions
        $activities['recent_stock_transactions'] = StockCard::with(['product', 'location'])
            ->select('id', 'product_id', 'location_id', 'transaction_type', 'quantity_in', 'quantity_out', 'transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'product_name' => $transaction->product->product_name ?? 'Unknown',
                    'location_name' => $transaction->location->name ?? 'Unknown',
                    'transaction_type' => $transaction->transaction_type,
                    'quantity_in' => $transaction->quantity_in,
                    'quantity_out' => $transaction->quantity_out,
                    'transaction_date' => $transaction->transaction_date,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
