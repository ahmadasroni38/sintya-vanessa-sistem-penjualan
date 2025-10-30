<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Get products with filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProducts(array $filters = [], int $perPage = 15)
    {
        $query = Product::query()
            ->with(['category', 'unit', 'location'])
            ->withCount(['stockCards']);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'product_code';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters($query, array $filters): void
    {
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Product type filter
        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Active status filter
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        // Location filter
        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        // Low stock filter
        if (!empty($filters['low_stock']) && filter_var($filters['low_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('stockCards', function ($q) {
                $q->selectRaw('MAX(balance) as current_stock')
                  ->groupBy('product_id')
                  ->havingRaw('MAX(balance) < minimum_stock');
            });
        }
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();

        try {
            $product = Product::create($data);

            // Clear cache
            $this->clearProductCache();

            DB::commit();

            return $product->load(['category', 'unit', 'location']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update a product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        DB::beginTransaction();

        try {
            $product->update($data);

            // Clear cache
            $this->clearProductCache();

            DB::commit();

            return $product->fresh(['category', 'unit', 'location']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(Product $product): bool
    {
        // Check if product has stock movements
        if ($product->stockCards()->exists()) {
            throw new \Exception('Cannot delete product with existing stock movements.');
        }

        DB::beginTransaction();

        try {
            $deleted = $product->delete();

            // Clear cache
            $this->clearProductCache();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get active products for dropdown.
     *
     * @return Collection
     */
    public function getActiveProducts(): Collection
    {
        return Cache::remember('active_products', 3600, function () {
            return Product::active()
                ->with(['category', 'unit'])
                ->orderBy('product_code')
                ->get(['id', 'product_code', 'product_name', 'product_type', 'unit_id', 'category_id']);
        });
    }

    /**
     * Get product stock information.
     *
     * @param Product $product
     * @param int|null $locationId
     * @return array
     */
    public function getProductStock(Product $product, ?int $locationId = null): array
    {
        if ($locationId) {
            $stock = $product->getCurrentStock($locationId);

            return [
                'product' => $product,
                'location_id' => $locationId,
                'stock' => $stock,
                'is_below_minimum' => $product->isBelowMinimum($locationId),
                'is_above_maximum' => $product->isAboveMaximum($locationId),
            ];
        }

        // Get stock for all active locations
        $locations = Location::active()->get();
        $stockData = [];

        foreach ($locations as $location) {
            $stock = $product->getCurrentStock($location->id);
            $stockData[] = [
                'location_id' => $location->id,
                'location_name' => $location->name,
                'location_code' => $location->code,
                'stock' => $stock,
                'is_below_minimum' => $product->isBelowMinimum($location->id),
                'is_above_maximum' => $product->isAboveMaximum($location->id),
            ];
        }

        return [
            'product' => $product,
            'stock_data' => $stockData,
            'total_stock' => collect($stockData)->sum('stock'),
        ];
    }

    /**
     * Get product statistics.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return Cache::remember('product_statistics', 1800, function () {
            $total = Product::count();
            $active = Product::where('is_active', true)->count();
            $inactive = Product::where('is_active', false)->count();

            $byType = Product::select('product_type', DB::raw('count(*) as count'))
                ->groupBy('product_type')
                ->pluck('count', 'product_type')
                ->toArray();

            // Count low stock products
            $lowStock = Product::whereHas('stockCards', function ($q) {
                $q->selectRaw('product_id, MAX(balance) as current_stock')
                  ->groupBy('product_id')
                  ->havingRaw('current_stock < minimum_stock');
            })->count();

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'low_stock' => $lowStock,
                'by_type' => $byType,
            ];
        });
    }

    /**
     * Generate next product code.
     *
     * @param string $productType
     * @return string
     */
    public function generateProductCode(string $productType): string
    {
        $prefix = match($productType) {
            'raw_material' => 'RM',
            'finished_goods' => 'FG',
            'consumable' => 'CS',
            default => 'PRD',
        };

        $lastProduct = Product::where('product_code', 'like', "{$prefix}%")
            ->orderBy('product_code', 'desc')
            ->first();

        if (!$lastProduct) {
            return $prefix . '00001';
        }

        $lastNumber = (int) substr($lastProduct->product_code, strlen($prefix));
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Clear product cache.
     *
     * @return void
     */
    protected function clearProductCache(): void
    {
        Cache::forget('active_products');
        Cache::forget('product_statistics');
    }

    /**
     * Export products to array.
     *
     * @param array $filters
     * @return Collection
     */
    public function exportProducts(array $filters = []): Collection
    {
        $query = Product::query()
            ->with(['category', 'unit', 'location']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('product_code')->get();
    }

    /**
     * Get low stock products.
     *
     * @param int|null $locationId
     * @return Collection
     */
    public function getLowStockProducts(?int $locationId = null): Collection
    {
        $query = Product::active()
            ->with(['category', 'unit', 'stockCards' => function ($q) use ($locationId) {
                if ($locationId) {
                    $q->where('location_id', $locationId);
                }
                $q->latest('transaction_date')->latest('id');
            }]);

        return $query->get()->filter(function ($product) use ($locationId) {
            return $product->isBelowMinimum($locationId);
        });
    }
}
