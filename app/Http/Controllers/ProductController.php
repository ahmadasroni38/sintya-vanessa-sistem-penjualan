<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ProductService $productService
    ) {
        // Apply authorization middleware
        // $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'product_type',
            'category_id',
            'is_active',
            'location_id',
            'low_stock',
            'sort_by',
            'sort_order',
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $products = $this->productService->getProducts($filters, $perPage);

        return response()->json([
            'status' => true,
            'message' => 'Data product berhasil diambil',
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'message' => 'Product created successfully.',
                'data' => new ProductResource($product),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, Product $product): JsonResponse
    {
        $product->load(['category', 'unit', 'location']);

        // Include stock data if requested
        if ($request->has('with_stock')) {
            $locationId = $request->input('location_id');
            $stockData = $this->productService->getProductStock($product, $locationId);

            return response()->json([
                'data' => new ProductResource($product),
                'stock' => $stockData,
            ]);
        }

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($product, $request->validated());

            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => new ProductResource($product),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            $this->productService->deleteProduct($product);

            return response()->json([
                'message' => 'Product deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get all active products.
     */
    public function active(): JsonResponse
    {
        $products = $this->productService->getActiveProducts();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Get product stock information.
     */
    public function stock(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'location_id' => 'nullable|integer|exists:locations,id',
        ]);

        $locationId = $request->input('location_id');
        $stockData = $this->productService->getProductStock($product, $locationId);

        return response()->json([
            'data' => $stockData,
        ]);
    }

    /**
     * Get product statistics.
     */
    public function statistics(): JsonResponse
    {
        // $this->authorize('viewAny', Product::class);

        $statistics = $this->productService->getStatistics();

        return response()->json([
            'data' => $statistics,
        ]);
    }

    /**
     * Generate next product code.
     */
    public function generateCode(Request $request): JsonResponse
    {
        $request->validate([
            'product_type' => 'required|string|in:raw_material,finished_goods,consumable',
        ]);

        $code = $this->productService->generateProductCode($request->product_type);

        return response()->json([
            'code' => $code,
        ]);
    }

    /**
     * Export products.
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', Product::class);

        $filters = $request->only([
            'search',
            'product_type',
            'category_id',
            'is_active',
            'location_id',
        ]);

        $products = $this->productService->exportProducts($filters);

        // Here you would typically use a package like Laravel Excel
        // For now, just return the data
        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Get low stock products.
     */
    public function lowStock(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $request->validate([
            'location_id' => 'nullable|integer|exists:locations,id',
        ]);

        $locationId = $request->input('location_id');
        $products = $this->productService->getLowStockProducts($locationId);

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Toggle product active status.
     */
    public function toggleStatus(Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return response()->json([
            'message' => 'Product status updated successfully.',
            'data' => new ProductResource($product->fresh(['category', 'unit', 'location'])),
        ]);
    }
}
