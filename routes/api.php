<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\PreventiveMaintenanceController;
use App\Http\Controllers\Api\RepairRequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\DashboardController;

// Accounting & Warehouse Controllers
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\LocationController as WarehouseLocationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no auth required)
Route::get('assets/{identifier}/public', [AssetController::class, 'showPublic'])
    ->where('identifier', '.*');

// Auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

// Protected routes
Route::middleware(['auth:api', 'user.status'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);

    // Dashboard routes
    Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics']);
    Route::get('dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('dashboard/recent-activity', [DashboardController::class, 'getRecentActivity']);

    // Role management routes
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
    Route::delete('roles/{role}/permissions', [RoleController::class, 'removePermissions']);

    // Permission management routes
    Route::apiResource('permissions', PermissionController::class);
    Route::post('permissions/{permission}/roles', [PermissionController::class, 'assignRoles']);
    Route::delete('permissions/{permission}/roles', [PermissionController::class, 'removeRoles']);

    // User management routes
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::post('users/{user}/roles', [UserController::class, 'assignRoles']);
    Route::delete('users/{user}/roles', [UserController::class, 'removeRoles']);
    Route::post('users/{user}/active-role', [UserController::class, 'setActiveRole']);
    Route::post('users/{user}/permissions', [UserController::class, 'assignPermissions']);
    Route::delete('users/{user}/permissions', [UserController::class, 'removePermissions']);
    Route::get('users/{user}/permissions', [UserController::class, 'getPermissions']);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);

    // Asset Category management routes
    Route::get('asset-categories', [AssetCategoryController::class, 'index']);
    Route::post('asset-categories', [AssetCategoryController::class, 'store']);
    Route::get('asset-categories/{assetCategory}', [AssetCategoryController::class, 'show']);
    Route::put('asset-categories/{assetCategory}', [AssetCategoryController::class, 'update']);
    Route::delete('asset-categories/{assetCategory}', [AssetCategoryController::class, 'destroy']);
    Route::post('asset-categories/{assetCategory}/toggle-status', [AssetCategoryController::class, 'toggleStatus']);
    Route::get('asset-categories-tree', [AssetCategoryController::class, 'tree']);
    Route::get('asset-categories-parent-options', [AssetCategoryController::class, 'parentOptions']);
    Route::get('asset-categories-parent-options/{assetCategory}', [AssetCategoryController::class, 'parentOptions']);

    // Location management routes
    Route::get('locations', [LocationController::class, 'index']);
    Route::post('locations', [LocationController::class, 'store']);
    Route::get('locations/{location}', [LocationController::class, 'show']);
    Route::put('locations/{location}', [LocationController::class, 'update']);
    Route::delete('locations/{location}', [LocationController::class, 'destroy']);
    Route::post('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus']);
    Route::get('locations-tree', [LocationController::class, 'tree']);
    Route::get('locations-parent-options', [LocationController::class, 'parentOptions']);
    Route::get('locations-parent-options/{location}', [LocationController::class, 'parentOptions']);

    // Asset management routes
    Route::get('assets', [AssetController::class, 'index']);
    Route::post('assets', [AssetController::class, 'store']);
    Route::get('assets/{asset}', [AssetController::class, 'show']);
    Route::put('assets/{asset}', [AssetController::class, 'update']);
    Route::delete('assets/{asset}', [AssetController::class, 'destroy']);
    Route::post('assets/{asset}/update-status', [AssetController::class, 'updateStatus']);
    Route::post('assets/bulk-update-status', [AssetController::class, 'bulkUpdateStatus']);
    Route::get('assets-by-category/{category}', [AssetController::class, 'getByCategory']);
    Route::get('assets-by-location/{location}', [AssetController::class, 'getByLocation']);
    Route::get('assets-parent-options', [AssetController::class, 'parentOptions']);
    Route::get('assets-parent-options/{asset}', [AssetController::class, 'parentOptions']);
    Route::get('assets-statistics', [AssetController::class, 'statistics']);
    Route::get('assets-export', [AssetController::class, 'export']);

    // Work Order management routes
    Route::get('work-orders', [WorkOrderController::class, 'index']);
    Route::post('work-orders', [WorkOrderController::class, 'store']);
    Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show']);
    Route::put('work-orders/{workOrder}', [WorkOrderController::class, 'update']);
    Route::delete('work-orders/{workOrder}', [WorkOrderController::class, 'destroy']);
    Route::post('work-orders/{workOrder}/update-status', [WorkOrderController::class, 'updateStatus']);
    Route::post('work-orders/{workOrder}/start', [WorkOrderController::class, 'start']);
    Route::post('work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete']);
    Route::post('work-orders/{workOrder}/assign', [WorkOrderController::class, 'assign']);
    Route::post('work-orders/bulk-update-status', [WorkOrderController::class, 'bulkUpdateStatus']);
    Route::get('work-orders-statistics', [WorkOrderController::class, 'statistics']);
    Route::get('work-orders-export', [WorkOrderController::class, 'export']);
    Route::get('work-orders-user-options', [WorkOrderController::class, 'userOptions']);
    Route::get('work-orders-asset-options', [WorkOrderController::class, 'assetOptions']);
    Route::get('work-orders-location-options', [WorkOrderController::class, 'locationOptions']);

    // Preventive Maintenance management routes
    Route::get('preventive-maintenances', [PreventiveMaintenanceController::class, 'index']);
    Route::post('preventive-maintenances', [PreventiveMaintenanceController::class, 'store']);
    Route::get('preventive-maintenances/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'show']);
    Route::put('preventive-maintenances/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'update']);
    Route::delete('preventive-maintenances/{preventiveMaintenance}', [PreventiveMaintenanceController::class, 'destroy']);
    Route::get('preventive-maintenances-statistics', [PreventiveMaintenanceController::class, 'statistics']);
    Route::get('preventive-maintenances-user-options', [PreventiveMaintenanceController::class, 'userOptions']);
    Route::get('preventive-maintenances-asset-options', [PreventiveMaintenanceController::class, 'assetOptions']);
    Route::get('preventive-maintenances-location-options', [PreventiveMaintenanceController::class, 'locationOptions']);

    // Repair Request management routes
    Route::get('repair-requests', [RepairRequestController::class, 'index']);
    Route::post('repair-requests', [RepairRequestController::class, 'store']);
    Route::get('repair-requests/{repairRequest}', [RepairRequestController::class, 'show']);
    Route::put('repair-requests/{repairRequest}', [RepairRequestController::class, 'update']);
    Route::delete('repair-requests/{repairRequest}', [RepairRequestController::class, 'destroy']);
    Route::get('repair-requests-statistics', [RepairRequestController::class, 'statistics']);
    Route::get('repair-requests-export', [RepairRequestController::class, 'export']);

    // Vendor management routes
    Route::get('vendors', [VendorController::class, 'index']);
    Route::post('vendors', [VendorController::class, 'store']);
    Route::get('vendors/{vendor}', [VendorController::class, 'show']);
    Route::put('vendors/{vendor}', [VendorController::class, 'update']);
    Route::delete('vendors/{vendor}', [VendorController::class, 'destroy']);
    Route::post('vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus']);
    Route::get('vendors-by-type', [VendorController::class, 'getByType']);

    // Notification management routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    // Barcode management routes
    Route::get('assets/{asset}/barcode', [BarcodeController::class, 'generateAssetBarcode']);
    Route::post('assets/bulk-barcode', [BarcodeController::class, 'generateBulkBarcodes']);
    Route::get('assets/{asset}/qrcode', [BarcodeController::class, 'generateAssetQRCode']);

    // QR Code PDF Export routes
    Route::get('assets/{asset}/qrcode-pdf', [BarcodeController::class, 'exportAssetQRCodePDF']);
    Route::post('assets/bulk-qrcode-pdf', [BarcodeController::class, 'exportBulkQRCodePDF']);
    Route::get('assets/all-qrcode-pdf', [BarcodeController::class, 'exportAllQRCodePDF']);

    // Profile management routes
    Route::get('profile', [UserController::class, 'getProfile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::put('profile/password', [UserController::class, 'updatePassword']);

    // =====================================================
    // ACCOUNTING MODULE
    // =====================================================

    // Chart of Accounts routes
    Route::get('chart-of-accounts', [ChartOfAccountController::class, 'index']);
    Route::post('chart-of-accounts', [ChartOfAccountController::class, 'store']);
    Route::get('chart-of-accounts/tree', [ChartOfAccountController::class, 'tree']);
    Route::get('chart-of-accounts/active', [ChartOfAccountController::class, 'active']);
    Route::get('chart-of-accounts/generate-code', [ChartOfAccountController::class, 'generateCode']);
    Route::get('chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'show']);
    Route::put('chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'update']);
    Route::delete('chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'destroy']);
    Route::post('chart-of-accounts/{id}/restore', [ChartOfAccountController::class, 'restore']);
    Route::post('chart-of-accounts/{chartOfAccount}/move', [ChartOfAccountController::class, 'move']);
    Route::get('chart-of-accounts/{chartOfAccount}/balance', [ChartOfAccountController::class, 'balance']);
    Route::post('chart-of-accounts/{chartOfAccount}/calculate-balance', [ChartOfAccountController::class, 'calculateBalance']);
    Route::get('chart-of-accounts/{chartOfAccount}/balance-history', [ChartOfAccountController::class, 'balanceHistory']);
    Route::get('chart-of-accounts/{chartOfAccount}/audit', [ChartOfAccountController::class, 'audits']);
    Route::post('chart-of-accounts/export', [ChartOfAccountController::class, 'export']);
    Route::get('chart-of-accounts/export/{filename}', [ChartOfAccountController::class, 'downloadExport']);
    Route::get('chart-of-accounts/exports', [ChartOfAccountController::class, 'getExports']);

    // Journal Entry routes
    Route::get('journal-entries', [JournalEntryController::class, 'index']);
    Route::post('journal-entries', [JournalEntryController::class, 'store']);
    Route::get('journal-entries/statistics', [JournalEntryController::class, 'statistics']);
    Route::get('journal-entries/generate-number', [JournalEntryController::class, 'generateNumber']);
    Route::get('journal-entries/{journalEntry}', [JournalEntryController::class, 'show']);
    Route::put('journal-entries/{journalEntry}', [JournalEntryController::class, 'update']);
    Route::delete('journal-entries/{journalEntry}', [JournalEntryController::class, 'destroy']);
    Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post']);
    Route::post('journal-entries/{journalEntry}/cancel', [JournalEntryController::class, 'cancel']);
    Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse']);
    Route::post('journal-entries/{journalEntry}/duplicate', [JournalEntryController::class, 'duplicate']);
    Route::post('journal-entries/{id}/restore', [JournalEntryController::class, 'restore']);

    // Financial Reports routes
    Route::get('reports/neraca-lajur', [ReportController::class, 'neracaLajur']);
    Route::get('reports/neraca', [ReportController::class, 'neraca']);
    Route::get('reports/laba-rugi', [ReportController::class, 'labaRugi']);
    Route::get('reports/perubahan-modal', [ReportController::class, 'perubahanModal']);
    Route::get('reports/arus-kas', [ReportController::class, 'arusKas']);

    // =====================================================
    // WAREHOUSE MODULE
    // =====================================================

    // Warehouse Location routes (distinct from asset management locations)
    Route::get('warehouse/locations', [WarehouseLocationController::class, 'index']);
    Route::post('warehouse/locations', [WarehouseLocationController::class, 'store']);
    Route::get('warehouse/locations/active', [WarehouseLocationController::class, 'active']);
    Route::get('warehouse/locations/tree', [WarehouseLocationController::class, 'tree']);
    Route::get('warehouse/locations/{location}', [WarehouseLocationController::class, 'show']);
    Route::put('warehouse/locations/{location}', [WarehouseLocationController::class, 'update']);
    Route::delete('warehouse/locations/{location}', [WarehouseLocationController::class, 'destroy']);

    // Unit routes
    Route::get('units', [App\Http\Controllers\UnitController::class, 'active']);
    Route::post('units', [App\Http\Controllers\UnitController::class, 'store']);
    Route::get('units/active', [App\Http\Controllers\UnitController::class, 'active']);
    Route::get('units/{unit}', [App\Http\Controllers\UnitController::class, 'show']);
    Route::put('units/{unit}', [App\Http\Controllers\UnitController::class, 'update']);
    Route::delete('units/{unit}', [App\Http\Controllers\UnitController::class, 'destroy']);

    // Product Category routes
    Route::get('product-categories', [App\Http\Controllers\ProductCategoryController::class, 'index']);
    Route::post('product-categories', [App\Http\Controllers\ProductCategoryController::class, 'store']);
    Route::get('product-categories/active', [App\Http\Controllers\ProductCategoryController::class, 'active']);
    Route::get('product-categories/tree', [App\Http\Controllers\ProductCategoryController::class, 'tree']);
    Route::get('product-categories/{category}', [App\Http\Controllers\ProductCategoryController::class, 'show']);
    Route::put('product-categories/{category}', [App\Http\Controllers\ProductCategoryController::class, 'update']);
    Route::delete('product-categories/{category}', [App\Http\Controllers\ProductCategoryController::class, 'destroy']);

    // Product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/statistics', [ProductController::class, 'statistics']);
    Route::get('products/active', [ProductController::class, 'active']);
    Route::get('products/low-stock', [ProductController::class, 'lowStock']);
    Route::get('products/generate-code', [ProductController::class, 'generateCode']);
    Route::get('products/export', [ProductController::class, 'export']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
    Route::get('products/{product}/stock', [ProductController::class, 'stock']);

    // Stock In routes
    Route::get('stock-in', [StockInController::class, 'index']);
    Route::post('stock-in', [StockInController::class, 'store']);
    Route::get('stock-in/options', [StockInController::class, 'options']);
    Route::get('stock-in/statistics', [StockInController::class, 'statistics']);
    Route::get('stock-in/{stockIn}', [StockInController::class, 'show']);
    Route::put('stock-in/{stockIn}', [StockInController::class, 'update']);
    Route::delete('stock-in/{stockIn}', [StockInController::class, 'destroy']);
    Route::post('stock-in/{stockIn}/post', [StockInController::class, 'post']);
    Route::post('stock-in/{stockIn}/cancel', [StockInController::class, 'cancel']);

    // Stock Mutation routes
    Route::get('stock-mutations', [StockMutationController::class, 'index']);
    Route::post('stock-mutations', [StockMutationController::class, 'store']);
    Route::get('stock-mutations/options', [StockMutationController::class, 'options']);
    Route::get('stock-mutations/check-stock', [StockMutationController::class, 'checkStock']);
    Route::get('stock-mutations/statistics', [StockMutationController::class, 'statistics']);
    Route::get('stock-mutations/{stockMutation}', [StockMutationController::class, 'show']);
    Route::put('stock-mutations/{stockMutation}', [StockMutationController::class, 'update']);
    Route::delete('stock-mutations/{stockMutation}', [StockMutationController::class, 'destroy']);
    Route::post('stock-mutations/{stockMutation}/submit', [StockMutationController::class, 'submit']);
    Route::post('stock-mutations/{stockMutation}/approve', [StockMutationController::class, 'approve']);
    Route::post('stock-mutations/{stockMutation}/complete', [StockMutationController::class, 'complete']);
    Route::post('stock-mutations/{stockMutation}/cancel', [StockMutationController::class, 'cancel']);

    // Stock Adjustment routes
    Route::get('stock-adjustments/statistics', [StockAdjustmentController::class, 'statistics']);
    Route::get('stock-adjustments/export', [StockAdjustmentController::class, 'export']);
    Route::post('stock-adjustments/bulk-approve', [StockAdjustmentController::class, 'bulkApprove']);
    Route::post('stock-adjustments/bulk-delete', [StockAdjustmentController::class, 'bulkDelete']);
    Route::post('stock-adjustments/calculate-system-quantity', [StockAdjustmentController::class, 'calculateSystemQuantity']);
    Route::get('stock-adjustments', [StockAdjustmentController::class, 'index']);
    Route::get('stock-adjustments/create', [StockAdjustmentController::class, 'create']);
    Route::post('stock-adjustments', [StockAdjustmentController::class, 'store']);
    Route::get('stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'show']);
    Route::get('stock-adjustments/{stockAdjustment}/edit', [StockAdjustmentController::class, 'edit']);
    Route::put('stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'update']);
    Route::delete('stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'destroy']);
    Route::post('stock-adjustments/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve']);
    Route::post('stock-adjustments/{stockAdjustment}/cancel', [StockAdjustmentController::class, 'cancel']);

    // Stock Opname routes
    Route::get('stock-opnames', [StockOpnameController::class, 'index']);
    Route::get('stock-opnames/create', [StockOpnameController::class, 'create']);
    Route::post('stock-opnames', [StockOpnameController::class, 'store']);
    Route::get('stock-opnames/{stockOpname}', [StockOpnameController::class, 'show']);
    Route::get('stock-opnames/{stockOpname}/edit', [StockOpnameController::class, 'edit']);
    Route::put('stock-opnames/{stockOpname}', [StockOpnameController::class, 'update']);
    Route::delete('stock-opnames/{stockOpname}', [StockOpnameController::class, 'destroy']);
    Route::post('stock-opnames/{stockOpname}/complete', [StockOpnameController::class, 'complete']);
    Route::post('stock-opnames/{stockOpname}/cancel', [StockOpnameController::class, 'cancel']);
    Route::post('stock-opnames/get-products', [StockOpnameController::class, 'getProductsForOpname']);

    // Stock Card routes
    Route::get('stock-cards', [StockCardController::class, 'index']);
    Route::get('stock-cards/show', [StockCardController::class, 'show']);
    Route::get('stock-cards/summary', [StockCardController::class, 'summary']);
    Route::get('stock-cards/balances', [StockCardController::class, 'balances']);
    Route::get('stock-cards/export', [StockCardController::class, 'export']);

    // Test route for statistics
    Route::get('test-statistics', [App\Http\Controllers\TestController::class, 'testStatistics']);
});
