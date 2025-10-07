<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the asset categories.
     */
    public function index(): JsonResponse
    {
        $categories = AssetCategory::with(['parent', 'children', 'location'])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'description' => $category->description,
                    'color' => $category->color,
                    'is_active' => $category->is_active,
                    'parent_id' => $category->parent_id,
                    'parent' => $category->parent ? [
                        'id' => $category->parent->id,
                        'name' => $category->parent->name,
                        'code' => $category->parent->code,
                    ] : null,
                    'children_count' => $category->children->count(),
                    'full_path' => $category->full_path,
                    'hierarchy_level' => $category->hierarchy_level,
                    'metadata' => $category->metadata,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Store a newly created asset category.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:asset_categories,code',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:asset_categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_id is provided
        if ($request->parent_id) {
            $parent = AssetCategory::find($request->parent_id);
            if ($parent && $this->wouldCreateCircularReference($parent, null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot set parent: this would create a circular reference',
                ], 422);
            }
        }

        $category = AssetCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'color' => $request->color ?: '#6366F1',
            'parent_id' => $request->parent_id,
            'location_id' => $request->location_id,
            'metadata' => $request->metadata ?: [],
            'is_active' => true,
        ]);

        // Load relationships for response
        $category->load(['parent', 'children', 'location']);

        return response()->json([
            'success' => true,
            'message' => 'Asset category created successfully',
            'data' => $this->formatCategoryResponse($category),
        ], 201);
    }

    /**
     * Display the specified asset category.
     */
    public function show(AssetCategory $assetCategory): JsonResponse
    {
        $assetCategory->load(['parent', 'children', 'location']);

        return response()->json([
            'success' => true,
            'data' => $this->formatCategoryResponse($assetCategory),
        ]);
    }

    /**
     * Update the specified asset category.
     */
    public function update(Request $request, AssetCategory $assetCategory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:20|unique:asset_categories,code,' . $assetCategory->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'parent_id' => 'nullable|exists:asset_categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'sometimes|boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for circular reference if parent_id is being changed
        if ($request->has('parent_id') && $request->parent_id != $assetCategory->parent_id) {
            if ($request->parent_id) {
                $parent = AssetCategory::find($request->parent_id);
                if ($parent && $this->wouldCreateCircularReference($parent, $assetCategory->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set parent: this would create a circular reference',
                    ], 422);
                }
            }
        }

        $updateData = [];

        // Only update provided fields
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('code')) {
            $updateData['code'] = $request->code;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        if ($request->has('color')) {
            $updateData['color'] = $request->color;
        }
        if ($request->has('parent_id')) {
            $updateData['parent_id'] = $request->parent_id;
        }
        if ($request->has('location_id')) {
            $updateData['location_id'] = $request->location_id;
        }
        if ($request->has('is_active')) {
            $updateData['is_active'] = $request->is_active;
        }
        if ($request->has('metadata')) {
            $updateData['metadata'] = $request->metadata;
        }

        if (!empty($updateData)) {
            $assetCategory->update($updateData);
        }

        // Reload relationships
        $assetCategory->load(['parent', 'children', 'location']);

        return response()->json([
            'success' => true,
            'message' => 'Asset category updated successfully',
            'data' => $this->formatCategoryResponse($assetCategory),
        ]);
    }

    /**
     * Remove the specified asset category.
     */
    public function destroy(AssetCategory $assetCategory): JsonResponse
    {
        // Check if category can be deleted
        if (!$assetCategory->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category: it has child categories or assets assigned to it',
            ], 422);
        }

        $assetCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset category deleted successfully',
        ]);
    }

    /**
     * Toggle asset category status (active/inactive).
     */
    public function toggleStatus(AssetCategory $assetCategory): JsonResponse
    {
        $oldStatus = $assetCategory->is_active;
        $assetCategory->toggleStatus();
        $newStatus = $assetCategory->is_active;

        return response()->json([
            'success' => true,
            'message' => "Asset category " . ($newStatus ? 'activated' : 'deactivated') . " successfully",
            'data' => [
                'category' => $this->formatCategoryResponse($assetCategory->load(['parent', 'children', 'location'])),
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
        ]);
    }

    /**
     * Get hierarchical tree structure of categories.
     */
    public function tree(): JsonResponse
    {
        $categories = AssetCategory::with(['children' => function ($query) {
            $query->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get()
        ->map(function ($category) {
            return $this->buildCategoryTree($category);
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get categories suitable for parent selection (excluding descendants).
     */
    public function parentOptions(AssetCategory $assetCategory = null): JsonResponse
    {
        $query = AssetCategory::active()->orderBy('name');

        // If updating existing category, exclude itself and its descendants
        if ($assetCategory) {
            $excludeIds = [$assetCategory->id];
            $this->collectDescendantIds($assetCategory, $excludeIds);
            $query->whereNotIn('id', $excludeIds);
        }

        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
                'full_path' => $category->full_path,
                'hierarchy_level' => $category->hierarchy_level,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Format category response data.
     */
    private function formatCategoryResponse(AssetCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'code' => $category->code,
            'description' => $category->description,
            'color' => $category->color,
            'is_active' => $category->is_active,
            'parent_id' => $category->parent_id,
            'parent' => $category->parent ? [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
                'code' => $category->parent->code,
            ] : null,
            'location_id' => $category->location_id,
            'location' => $category->location ? [
                'id' => $category->location->id,
                'name' => $category->location->name,
                'code' => $category->location->code,
                'full_address' => $category->location->full_address,
            ] : null,
            'children_count' => $category->children->count(),
            'full_path' => $category->full_path,
            'hierarchy_level' => $category->hierarchy_level,
            'metadata' => $category->metadata,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }

    /**
     * Build hierarchical tree structure.
     */
    private function buildCategoryTree(AssetCategory $category): array
    {
        $data = $this->formatCategoryResponse($category);

        if ($category->children->isNotEmpty()) {
            $data['children'] = $category->children->map(function ($child) {
                return $this->buildCategoryTree($child);
            });
        }

        return $data;
    }

    /**
     * Check if setting a parent would create a circular reference.
     */
    private function wouldCreateCircularReference(AssetCategory $potentialParent, ?int $categoryId): bool
    {
        if (!$categoryId) {
            return false;
        }

        // Check if the potential parent is the same as the category
        if ($potentialParent->id === $categoryId) {
            return true;
        }

        // Check if the potential parent is a descendant of the category
        $parent = $potentialParent->parent;
        while ($parent) {
            if ($parent->id === $categoryId) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Collect all descendant IDs recursively.
     */
    private function collectDescendantIds(AssetCategory $category, array &$ids): void
    {
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $this->collectDescendantIds($child, $ids);
        }
    }
}
