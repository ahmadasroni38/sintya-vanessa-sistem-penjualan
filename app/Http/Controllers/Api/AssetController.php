<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssetController extends Controller
{
    /**
     * Display a listing of the assets.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Asset::with(['category', 'location', 'parentAsset']);

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->byLocation($request->location_id);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->byStatus($request->status);
        }

        if ($request->has('condition') && !empty($request->condition)) {
            $query->byCondition($request->condition);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $assets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Store a newly created asset.
     */
    public function store(Request $request): JsonResponse
    {
        // Prepare specifications data
        $requestData = $request->all();
        if (isset($requestData['specifications'])) {
            if (is_string($requestData['specifications'])) {
                // If specifications is sent as JSON string, decode it
                $specs = json_decode($requestData['specifications'], true);
                $requestData['specifications'] = is_array($specs) ? $specs : [];
            } elseif (!is_array($requestData['specifications'])) {
                // If specifications is not array or string, set to empty array
                $requestData['specifications'] = [];
            }
        }

        $validator = Validator::make($requestData, [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:assets,code',
            'description' => 'nullable|string|max:1000',
            'serial_number' => 'nullable|string|max:100',
            'model_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:excellent,good,fair,poor,damaged',
            'status' => 'required|in:active,inactive,maintenance,retired,lost',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
            'warranty_expiry' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:locations,id',
            'parent_asset_id' => 'nullable|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_asset_id is provided
        if ($request->parent_asset_id) {
            $parentAsset = Asset::find($request->parent_asset_id);
            if ($parentAsset && $this->wouldCreateCircularReference($parentAsset, null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot set parent asset: this would create a circular reference',
                ], 422);
            }
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('assets', 'public');
        }

        $asset = Asset::create([
            'name' => $requestData['name'],
            'code' => $requestData['code'] ?? null,
            'description' => $requestData['description'] ?? null,
            'serial_number' => $requestData['serial_number'] ?? null,
            'model_number' => $requestData['model_number'] ?? null,
            'manufacturer' => $requestData['manufacturer'] ?? null,
            'purchase_date' => $requestData['purchase_date'] ?? null,
            'purchase_price' => $requestData['purchase_price'] ?? null,
            'current_value' => $requestData['current_value'] ?? null,
            'quantity' => $requestData['quantity'],
            'condition' => $requestData['condition'],
            'status' => $requestData['status'],
            'image_path' => $imagePath,
            'specifications' => $requestData['specifications'] ?? [],
            'warranty_expiry' => $requestData['warranty_expiry'] ?? null,
            'notes' => $requestData['notes'] ?? null,
            'category_id' => $requestData['category_id'],
            'location_id' => $requestData['location_id'],
            'parent_asset_id' => $requestData['parent_asset_id'] ?? null,
        ]);

        // Load relationships for response
        $asset->load(['category', 'location', 'parentAsset', 'childAssets']);

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully',
            'data' => $this->formatAssetResponse($asset),
        ], 201);
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset): JsonResponse
    {
        $asset->load(['category', 'location', 'parentAsset', 'childAssets']);

        return response()->json([
            'success' => true,
            'data' => $this->formatAssetResponse($asset),
        ]);
    }

    /**
     * Update the specified asset.
     */
    public function update(Request $request, Asset $asset): JsonResponse
    {
        // Prepare specifications data for update
        $requestData = $request->all();
        if (isset($requestData['specifications'])) {
            if (is_string($requestData['specifications'])) {
                // If specifications is sent as JSON string, decode it
                $specs = json_decode($requestData['specifications'], true);
                $requestData['specifications'] = is_array($specs) ? $specs : [];
            } elseif (!is_array($requestData['specifications'])) {
                // If specifications is not array or string, set to empty array
                $requestData['specifications'] = [];
            }
        }

        $validator = Validator::make($requestData, [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:assets,code,' . $asset->id,
            'description' => 'nullable|string|max:1000',
            'serial_number' => 'nullable|string|max:100',
            'model_number' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:1',
            'condition' => 'sometimes|required|in:excellent,good,fair,poor,damaged',
            'status' => 'sometimes|required|in:active,inactive,maintenance,retired,lost',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
            'warranty_expiry' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'category_id' => 'sometimes|required|exists:asset_categories,id',
            'location_id' => 'sometimes|required|exists:locations,id',
            'parent_asset_id' => 'nullable|exists:assets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_asset_id is being changed
        if (isset($requestData['parent_asset_id']) && $requestData['parent_asset_id'] != $asset->parent_asset_id) {
            if ($requestData['parent_asset_id']) {
                $parentAsset = Asset::find($requestData['parent_asset_id']);
                if ($parentAsset && $this->wouldCreateCircularReference($parentAsset, $asset->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set parent asset: this would create a circular reference',
                    ], 422);
                }
            }
        }

        $updateData = [];

        // Only update provided fields
        if (array_key_exists('name', $requestData)) {
            $updateData['name'] = $requestData['name'];
        }
        if (array_key_exists('code', $requestData)) {
            $updateData['code'] = $requestData['code'];
        }
        if (array_key_exists('description', $requestData)) {
            $updateData['description'] = $requestData['description'];
        }
        if (array_key_exists('serial_number', $requestData)) {
            $updateData['serial_number'] = $requestData['serial_number'];
        }
        if (array_key_exists('model_number', $requestData)) {
            $updateData['model_number'] = $requestData['model_number'];
        }
        if (array_key_exists('manufacturer', $requestData)) {
            $updateData['manufacturer'] = $requestData['manufacturer'];
        }
        if (array_key_exists('purchase_date', $requestData)) {
            $updateData['purchase_date'] = $requestData['purchase_date'];
        }
        if (array_key_exists('purchase_price', $requestData)) {
            $updateData['purchase_price'] = $requestData['purchase_price'];
        }
        if (array_key_exists('current_value', $requestData)) {
            $updateData['current_value'] = $requestData['current_value'];
        }
        if (array_key_exists('quantity', $requestData)) {
            $updateData['quantity'] = $requestData['quantity'];
        }
        if (array_key_exists('condition', $requestData)) {
            $updateData['condition'] = $requestData['condition'];
        }
        if (array_key_exists('status', $requestData)) {
            $updateData['status'] = $requestData['status'];
        }
        if (array_key_exists('specifications', $requestData)) {
            $updateData['specifications'] = $requestData['specifications'];
        }
        if (array_key_exists('warranty_expiry', $requestData)) {
            $updateData['warranty_expiry'] = $requestData['warranty_expiry'];
        }
        if (array_key_exists('notes', $requestData)) {
            $updateData['notes'] = $requestData['notes'];
        }
        if (array_key_exists('category_id', $requestData)) {
            $updateData['category_id'] = $requestData['category_id'];
        }
        if (array_key_exists('location_id', $requestData)) {
            $updateData['location_id'] = $requestData['location_id'];
        }
        if (array_key_exists('parent_asset_id', $requestData)) {
            $updateData['parent_asset_id'] = $requestData['parent_asset_id'];
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($asset->image_path) {
                Storage::disk('public')->delete($asset->image_path);
            }
            $updateData['image_path'] = $request->file('image')->store('assets', 'public');
        }

        if (!empty($updateData)) {
            $asset->update($updateData);
        }

        // Reload relationships
        $asset->load(['category', 'location', 'parentAsset', 'childAssets']);

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully',
            'data' => $this->formatAssetResponse($asset),
        ]);
    }

    /**
     * Remove the specified asset.
     */
    public function destroy(Asset $asset): JsonResponse
    {
        // Check if asset can be deleted
        if (!$asset->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete asset: it has child assets assigned to it',
            ], 422);
        }

        // Delete image if exists
        if ($asset->image_path) {
            Storage::disk('public')->delete($asset->image_path);
        }

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully',
        ]);
    }

    /**
     * Update asset status.
     */
    public function updateStatus(Request $request, Asset $asset): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,maintenance,retired,lost',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $asset->status;
        $asset->update(['status' => $request->status]);
        $newStatus = $asset->status;

        return response()->json([
            'success' => true,
            'message' => "Asset status updated successfully",
            'data' => [
                'asset' => $this->formatAssetResponse($asset->load(['category', 'location', 'parentAsset', 'childAssets'])),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Get assets by category.
     */
    public function getByCategory(AssetCategory $category): JsonResponse
    {
        $assets = $category->assets()
            ->with(['location', 'parentAsset'])
            ->orderBy('name')
            ->get()
            ->map(function ($asset) {
                return $this->formatAssetResponse($asset);
            });

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Get assets by location.
     */
    public function getByLocation(Location $location): JsonResponse
    {
        $assets = $location->assets()
            ->with(['category', 'parentAsset'])
            ->orderBy('name')
            ->get()
            ->map(function ($asset) {
                return $this->formatAssetResponse($asset);
            });

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Get assets suitable for parent selection (excluding descendants).
     */
    public function parentOptions(Asset $asset = null): JsonResponse
    {
        $query = Asset::active()->orderBy('name');

        // If updating existing asset, exclude itself and its descendants
        if ($asset) {
            $excludeIds = [$asset->id];
            $this->collectDescendantIds($asset, $excludeIds);
            $query->whereNotIn('id', $excludeIds);
        }

        $assets = $query->get()->map(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'code' => $asset->code,
                'full_path' => $asset->full_path,
                'hierarchy_level' => $asset->hierarchy_level,
                'category' => $asset->category ? $asset->category->name : null,
                'location' => $asset->location ? $asset->location->name : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * Bulk update asset status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'asset_ids' => 'required|array|min:1',
            'asset_ids.*' => 'required|integer|exists:assets,id',
            'status' => 'required|in:active,inactive,maintenance,retired,lost',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $assetIds = $request->asset_ids;
        $status = $request->status;

        // Update all assets at once
        Asset::whereIn('id', $assetIds)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => count($assetIds) . ' asset' . (count($assetIds) === 1 ? '' : 's') . ' status updated successfully',
            'data' => [
                'updated_count' => count($assetIds),
                'new_status' => $status,
            ],
        ]);
    }

    /**
     * Export assets to CSV.
     */
    public function export(Request $request)
    {
        $query = Asset::with(['category', 'location', 'parentAsset']);

        // Apply the same filters as index method
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('location_id') && !empty($request->location_id)) {
            $query->byLocation($request->location_id);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->byStatus($request->status);
        }

        if ($request->has('condition') && !empty($request->condition)) {
            $query->byCondition($request->condition);
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'assets_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'Code',
                'Serial Number',
                'Model Number',
                'Manufacturer',
                'Category',
                'Location',
                'Parent Asset',
                'Purchase Date',
                'Purchase Price',
                'Current Value',
                'Book Value',
                'Quantity',
                'Condition',
                'Status',
                'Warranty Expiry',
                'Description',
                'Notes',
                'Created At',
            ]);

            // CSV data
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->id,
                    $asset->name,
                    $asset->code,
                    $asset->serial_number,
                    $asset->model_number,
                    $asset->manufacturer,
                    $asset->category ? $asset->category->name : '',
                    $asset->location ? $asset->location->name : '',
                    $asset->parentAsset ? $asset->parentAsset->name : '',
                    $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '',
                    $asset->purchase_price,
                    $asset->current_value,
                    $asset->book_value,
                    $asset->quantity,
                    $asset->condition,
                    $asset->status,
                    $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '',
                    $asset->description,
                    $asset->notes,
                    $asset->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get asset statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::byStatus('active')->count();
        $maintenanceAssets = Asset::byStatus('maintenance')->count();
        $retiredAssets = Asset::byStatus('retired')->count();
        $lostAssets = Asset::byStatus('lost')->count();

        $totalValue = Asset::sum('current_value');
        $totalPurchaseValue = Asset::sum('purchase_price');

        $assetsByCondition = Asset::select('condition')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition')
            ->pluck('count', 'condition');

        $assetsByCategory = Asset::with('category')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Unknown',
                    'count' => $item->count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_assets' => $totalAssets,
                'active_assets' => $activeAssets,
                'maintenance_assets' => $maintenanceAssets,
                'retired_assets' => $retiredAssets,
                'lost_assets' => $lostAssets,
                'total_current_value' => $totalValue,
                'total_purchase_value' => $totalPurchaseValue,
                'assets_by_condition' => $assetsByCondition,
                'assets_by_category' => $assetsByCategory,
            ],
        ]);
    }

    /**
     * Format asset response data.
     */
    private function formatAssetResponse(Asset $asset): array
    {
        return [
            'id' => $asset->id,
            'name' => $asset->name,
            'code' => $asset->code,
            'description' => $asset->description,
            'serial_number' => $asset->serial_number,
            'model_number' => $asset->model_number,
            'manufacturer' => $asset->manufacturer,
            'purchase_date' => $asset->purchase_date,
            'purchase_price' => $asset->purchase_price,
            'current_value' => $asset->current_value,
            'book_value' => $asset->book_value,
            'quantity' => $asset->quantity,
            'condition' => $asset->condition,
            'status' => $asset->status,
            'image_path' => $asset->image_path,
            'image_url' => $asset->image_url,
            'specifications' => $asset->specifications,
            'warranty_expiry' => $asset->warranty_expiry,
            'is_warranty_valid' => $asset->isWarrantyValid(),
            'notes' => $asset->notes,
            'category_id' => $asset->category_id,
            'category' => $asset->category ? [
                'id' => $asset->category->id,
                'name' => $asset->category->name,
                'code' => $asset->category->code,
                'color' => $asset->category->color,
            ] : null,
            'location_id' => $asset->location_id,
            'location' => $asset->location ? [
                'id' => $asset->location->id,
                'name' => $asset->location->name,
                'code' => $asset->location->code,
                'full_address' => $asset->location->full_address,
            ] : null,
            'parent_asset_id' => $asset->parent_asset_id,
            'parent_asset' => $asset->parentAsset ? [
                'id' => $asset->parentAsset->id,
                'name' => $asset->parentAsset->name,
                'code' => $asset->parentAsset->code,
            ] : null,
            'child_assets_count' => $asset->childAssets->count(),
            'full_path' => $asset->full_path,
            'hierarchy_level' => $asset->hierarchy_level,
            'created_at' => $asset->created_at,
            'updated_at' => $asset->updated_at,
        ];
    }

    /**
     * Check if setting a parent would create a circular reference.
     */
    private function wouldCreateCircularReference(Asset $potentialParent, ?int $assetId): bool
    {
        if (!$assetId) {
            return false;
        }

        // Check if the potential parent is the same as the asset
        if ($potentialParent->id === $assetId) {
            return true;
        }

        // Check if the potential parent is a descendant of the asset
        $parent = $potentialParent->parentAsset;
        while ($parent) {
            if ($parent->id === $assetId) {
                return true;
            }
            $parent = $parent->parentAsset;
        }

        return false;
    }

    /**
     * Collect all descendant asset IDs recursively.
     */
    private function collectDescendantIds(Asset $asset, array &$ids): void
    {
        foreach ($asset->childAssets as $child) {
            $ids[] = $child->id;
            $this->collectDescendantIds($child, $ids);
        }
    }

    /**
     * Display public asset information (for QR code scanning).
     * Returns basic info if not authenticated, full info if authenticated.
     */
    public function showPublic(Request $request, string $identifier): JsonResponse
    {
        try {
            // Determine if identifier is ID or code
            $urlType = config('asset.qr_url_type', 'id');

            if ($urlType === 'code' || !is_numeric($identifier)) {
                $asset = Asset::where('code', $identifier)->firstOrFail();
            } else {
                $asset = Asset::findOrFail($identifier);
            }

            // Load relationships
            $asset->load(['category', 'location']);

            // Check if user is authenticated
            $isAuthenticated = auth('api')->check();
            $publicViewEnabled = config('asset.public_view.enabled', true);

            if (!$publicViewEnabled && !$isAuthenticated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Public asset view is disabled',
                ], 403);
            }

            // Prepare response data based on authentication status
            if ($isAuthenticated) {
                // Authenticated: Return full details
                $data = [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'code' => $asset->code,
                    'description' => $asset->description,
                    'serial_number' => $asset->serial_number,
                    'model_number' => $asset->model_number,
                    'manufacturer' => $asset->manufacturer,
                    'purchase_date' => $asset->purchase_date,
                    'purchase_price' => $asset->purchase_price,
                    'current_value' => $asset->current_value,
                    'book_value' => $asset->book_value,
                    'quantity' => $asset->quantity,
                    'condition' => $asset->condition,
                    'status' => $asset->status,
                    'image_url' => $asset->image_url,
                    'specifications' => $asset->specifications,
                    'warranty_expiry' => $asset->warranty_expiry,
                    'is_warranty_valid' => $asset->isWarrantyValid(),
                    'notes' => $asset->notes,
                    'category' => $asset->category ? [
                        'id' => $asset->category->id,
                        'name' => $asset->category->name,
                        'code' => $asset->category->code,
                        'color' => $asset->category->color,
                    ] : null,
                    'location' => $asset->location ? [
                        'id' => $asset->location->id,
                        'name' => $asset->location->name,
                        'code' => $asset->location->code,
                        'full_address' => $asset->location->full_address,
                    ] : null,
                    'shortcuts_enabled' => true,
                    'authenticated' => true,
                ];
            } else {
                // Not authenticated: Return basic public info only
                $showPrice = config('asset.public_view.show_price', false);
                $showSerial = config('asset.public_view.show_serial', false);

                $data = [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'code' => $asset->code,
                    'description' => $asset->description,
                    'condition' => $asset->condition,
                    'status' => $asset->status,
                    'image_url' => $asset->image_url,
                    'category' => $asset->category ? [
                        'name' => $asset->category->name,
                    ] : null,
                    'location' => $asset->location ? [
                        'name' => $asset->location->name,
                        'full_address' => $asset->location->full_address,
                    ] : null,
                    'shortcuts_enabled' => false,
                    'authenticated' => false,
                ];

                if ($showSerial) {
                    $data['serial_number'] = $asset->serial_number;
                }

                if ($showPrice) {
                    $data['current_value'] = $asset->current_value;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset information: ' . $e->getMessage(),
            ], 500);
        }
    }
}
