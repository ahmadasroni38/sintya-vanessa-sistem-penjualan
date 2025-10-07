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
});
