<?php

namespace App\Services;

use App\Models\StockBalance;
use App\Models\StockCard;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockBalanceService
{
    /**
     * Update stock balance for a specific product and location
     */
    public static function updateBalance(int $productId, int $locationId, string $transactionType, string $transactionDate = null)
    {
        try {
            DB::beginTransaction();

            // Get the latest stock card to determine current balance
            $latestStockCard = StockCard::where('product_id', $productId)
                ->where('location_id', $locationId)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $currentBalance = $latestStockCard ? $latestStockCard->balance : 0;
            $transactionDate = $transactionDate ?? now()->format('Y-m-d');

            // Get product information for min/max stock
            $product = Product::find($productId);
            $minimumStock = $product->minimum_stock ?? 0;
            $maximumStock = $product->maximum_stock ?? 999999;

            // Determine status based on current balance
            $status = self::determineStockStatus($currentBalance, $minimumStock, $maximumStock);

            // Update or create stock balance record
            StockBalance::updateOrCreate(
                [
                    'product_id' => $productId,
                    'location_id' => $locationId,
                ],
                [
                    'current_balance' => $currentBalance,
                    'minimum_stock' => $minimumStock,
                    'maximum_stock' => $maximumStock,
                    'status' => $status,
                    'last_transaction_date' => $transactionDate,
                    'last_transaction_type' => $transactionType,
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            Log::info('StockBalance updated', [
                'product_id' => $productId,
                'location_id' => $locationId,
                'balance' => $currentBalance,
                'transaction_type' => $transactionType
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update StockBalance', [
                'product_id' => $productId,
                'location_id' => $locationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update multiple stock balances from transaction
     */
    public static function updateBalancesFromTransaction(array $productLocations, string $transactionType, string $transactionDate = null)
    {
        foreach ($productLocations as $item) {
            self::updateBalance(
                $item['product_id'],
                $item['location_id'],
                $transactionType,
                $transactionDate
            );
        }
    }

    /**
     * Get current stock balance for a product at specific location
     */
    public static function getCurrentBalance(int $productId, int $locationId): float
    {
        $stockBalance = StockBalance::where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        return $stockBalance ? $stockBalance->current_balance : 0;
    }

    /**
     * Get stock balance information with status
     */
    public static function getBalanceInfo(int $productId, int $locationId): ?array
    {
        $stockBalance = StockBalance::with(['product', 'location'])
            ->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->first();

        if (!$stockBalance) {
            return null;
        }

        return [
            'product_id' => $stockBalance->product_id,
            'location_id' => $stockBalance->location_id,
            'current_balance' => $stockBalance->current_balance,
            'minimum_stock' => $stockBalance->minimum_stock,
            'maximum_stock' => $stockBalance->maximum_stock,
            'status' => $stockBalance->status,
            'last_transaction_date' => $stockBalance->last_transaction_date,
            'last_transaction_type' => $stockBalance->last_transaction_type,
            'product' => $stockBalance->product,
            'location' => $stockBalance->location,
        ];
    }

    /**
     * Recalculate and update all stock balances (for data correction)
     */
    public static function recalculateAllBalances()
    {
        try {
            DB::beginTransaction();

            // Get all unique product-location combinations from stock cards
            $productLocations = StockCard::select('product_id', 'location_id')
                ->distinct()
                ->get();

            foreach ($productLocations as $combination) {
                self::updateBalance(
                    $combination->product_id,
                    $combination->location_id,
                    'recalculation'
                );
            }

            DB::commit();

            Log::info('All StockBalances recalculated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to recalculate StockBalances', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Determine stock status based on balance
     */
    private static function determineStockStatus(float $balance, float $minStock, float $maxStock): string
    {
        if ($balance == 0) {
            return 'out_of_stock';
        } elseif ($balance < $minStock) {
            return 'low_stock';
        } elseif ($balance > $maxStock) {
            return 'overstock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get low stock products
     */
    public static function getLowStockProducts(int $locationId = null)
    {
        $query = StockBalance::with(['product', 'location'])
            ->where('status', 'low_stock');

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->get();
    }

    /**
     * Get out of stock products
     */
    public static function getOutOfStockProducts(int $locationId = null)
    {
        $query = StockBalance::with(['product', 'location'])
            ->where('status', 'out_of_stock');

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->get();
    }

    /**
     * Get overstock products
     */
    public static function getOverstockProducts(int $locationId = null)
    {
        $query = StockBalance::with(['product', 'location'])
            ->where('status', 'overstock');

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return $query->get();
    }
}
