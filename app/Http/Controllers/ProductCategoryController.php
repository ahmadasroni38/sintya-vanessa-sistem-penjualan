<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProductCategory::query();

        // Filter active categories by default
        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        // Include parent category
        $query->with('parent');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === '') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'code');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        if ($request->has('paginate') && !$request->paginate) {
            $categories = $query->get();
            return response()->json([
                'data' => $categories,
            ]);
        }

        $categories = $query->paginate($request->per_page ?? 15);

        return response()->json($categories);
    }

    /**
     * Get all active categories for dropdown/select.
     */
    public function active(): JsonResponse
    {
        $categories = ProductCategory::active()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'parent_id']);

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Get category tree structure.
     */
    public function tree(): JsonResponse
    {
        $categories = ProductCategory::active()
            ->with('descendants')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:product_categories,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateCategoryCode($validated['name']);
        }

        // Set is_active to true by default
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $category = ProductCategory::create($validated);

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => [
                'id' => $category->id,
                'value' => $category->id,
                'label' => $category->name,
                'name' => $category->name,
                'code' => $category->code,
            ],
        ], 201);
    }

    /**
     * Generate a unique category code from name.
     */
    private function generateCategoryCode(string $name): string
    {
        // Create code from first 4 letters of name, uppercase
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));

        if (empty($baseCode)) {
            $baseCode = 'CAT';
        }

        // Check if code exists, if yes add number suffix
        $code = $baseCode;
        $counter = 1;

        while (ProductCategory::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }

    /**
     * Display the specified category.
     */
    public function show(ProductCategory $category): JsonResponse
    {
        $category->load(['parent', 'children', 'products']);

        return response()->json([
            'data' => $category,
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, ProductCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:product_categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if (isset($validated['parent_id']) && $validated['parent_id']) {
            $parentCategory = ProductCategory::find($validated['parent_id']);
            if ($parentCategory && $parentCategory->isDescendantOf($category->id)) {
                return response()->json([
                    'message' => 'Cannot set a descendant as parent.',
                ], 422);
            }
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'data' => $category->load('parent'),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ProductCategory $category): JsonResponse
    {
        // Check if category can be deleted
        if (!$category->canBeDeleted()) {
            return response()->json([
                'message' => 'Cannot delete category that has products or sub-categories.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}
