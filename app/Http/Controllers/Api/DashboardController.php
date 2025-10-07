<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            'assets' => [
                'total' => Asset::count(),
                'active' => Asset::where('status', 'active')->count(),
                'maintenance' => Asset::where('status', 'maintenance')->count(),
                'retired' => Asset::where('status', 'retired')->count(),
                'by_condition' => Asset::select('condition', DB::raw('count(*) as count'))
                    ->groupBy('condition')
                    ->get()
                    ->pluck('count', 'condition'),
            ],
            'asset_categories' => [
                'total' => AssetCategory::count(),
                'distribution' => AssetCategory::withCount('assets')
                    ->orderBy('assets_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'name' => $category->name,
                            'count' => $category->assets_count,
                            'color' => $category->color,
                        ];
                    }),
            ],
            'locations' => [
                'total' => Location::count(),
                'with_assets' => Location::has('assets')->count(),
                'distribution' => Location::withCount('assets')
                    ->orderBy('assets_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($location) {
                        return [
                            'name' => $location->name,
                            'count' => $location->assets_count,
                        ];
                    }),
            ],
            'vendors' => [
                'total' => Vendor::count(),
                'active' => Vendor::where('is_active', true)->count(),
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

        // Assets by category (pie chart)
        $chartData['assets_by_category'] = AssetCategory::withCount('assets')
            ->having('assets_count', '>', 0)
            ->orderBy('assets_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return [
                    'label' => $category->name,
                    'value' => $category->assets_count,
                    'color' => $category->color,
                ];
            });

        // Assets by condition (bar chart)
        $chartData['assets_by_condition'] = Asset::select('condition', DB::raw('count(*) as count'))
            ->groupBy('condition')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst($item->condition),
                    'value' => $item->count,
                ];
            });

        // Assets by status (donut chart)
        $chartData['assets_by_status'] = Asset::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->status)),
                    'value' => $item->count,
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

        // Recent Assets (Admin only)
        if ($user->hasPermission('manage_assets')) {
            $activities['recent_assets'] = Asset::select('id', 'name', 'code', 'status', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
