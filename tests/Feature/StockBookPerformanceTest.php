<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockCard;
use App\Models\StockBalance;
use Tests\TestCase;

class StockBookPerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test performance with large dataset
     */
    public function test_large_dataset_performance()
    {
        // Create test data
        $products = Product::factory()->count(100)->create();
        $locations = Location::factory()->count(20)->create();

        // Generate stock cards (10,000 records)
        $stockCards = [];
        $startDate = now()->subDays(365); // 1 year ago

        foreach (range(1, 10000) as $index) {
            $product = $products[$index % 100];
            $location = $locations[$index % 20];
            $date = $startDate->copy()->addDays(rand(0, 365));

            $stockCards[] = [
                'product_id' => $product->id,
                'location_id' => $location->id,
                'transaction_date' => $date,
                'transaction_type' => ['stock_in', 'stock_out', 'mutation_in', 'mutation_out', 'adjustment_in', 'adjustment_out'][rand(0, 7)],
                'reference_number' => 'REF' . str_pad($index, 8, '0'),
                'quantity_in' => rand(0, 100),
                'quantity_out' => rand(0, 100),
                'balance' => rand(0, 1000),
                'created_by' => 'Test User',
                'notes' => 'Performance test data',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in batches for better performance
        $chunks = array_chunk($stockCards, 500);
        foreach ($chunks as $chunk) {
            StockCard::insert($chunk);
        }

        // Create stock balances
        $stockBalances = [];
        foreach ($products as $product) {
            foreach ($locations as $location) {
                $stockBalances[] = [
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'current_balance' => rand(0, 1000),
                    'minimum_stock' => 10,
                    'maximum_stock' => 1000,
                    'status' => 'in_stock',
                    'last_transaction_date' => now()->subDays(rand(1, 30)),
                    'last_transaction_type' => ['stock_in', 'stock_out'][rand(0, 1)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert stock balances in batches
        $balanceChunks = array_chunk($stockBalances, 500);
        foreach ($balanceChunks as $chunk) {
            StockBalance::insert($chunk);
        }

        // Test query performance
        $startTime = microtime(true);

        // Test 1: Filtered query with indexes
        $filteredResults = StockCard::with(['product', 'location'])
            ->where('product_id', $products[0]->id)
            ->where('location_id', $locations[0]->id)
            ->whereDate('transaction_date', '>=', now()->subDays(30))
            ->orderBy('transaction_date', 'desc')
            ->limit(100)
            ->get();

        $queryTime1 = microtime(true) - $startTime;

        // Test 2: Complex query with multiple conditions
        $complexResults = StockCard::with(['product', 'location'])
            ->whereHas('product', function ($query) {
                $query->where('product_name', 'like', '%Test%');
            })
            ->whereHas('location', function ($query) {
                $query->where('name', 'like', '%Location%');
            })
            ->whereIn('transaction_type', ['stock_in', 'stock_out'])
            ->whereDate('transaction_date', '>=', now()->subDays(90))
            ->orderBy('transaction_date', 'desc')
            ->limit(100)
            ->get();

        $queryTime2 = microtime(true) - $startTime;

        // Test 3: Aggregation query
        $aggregationResults = StockCard::selectRaw('
                product_id,
                location_id,
                SUM(quantity_in) as total_in,
                SUM(quantity_out) as total_out,
                COUNT(*) as transaction_count
            ')
            ->whereDate('transaction_date', '>=', now()->subDays(90))
            ->groupBy('product_id', 'location_id')
            ->havingRaw('transaction_count > ?', [10])
            ->limit(100)
            ->get();

        $queryTime3 = microtime(true) - $startTime;

        // Test 4: Current balances query
        $balanceResults = StockBalance::with(['product'])
            ->where('location_id', $locations[0]->id)
            ->where('current_balance', '>', 0)
            ->orderBy('current_balance', 'desc')
            ->limit(100)
            ->get();

        $queryTime4 = microtime(true) - $startTime;

        // Assert performance
        $this->assertLessThan(0.5, $queryTime1, 'Filtered query with indexes should complete in under 0.5 seconds');
        $this->assertLessThan(1.0, $queryTime2, 'Complex query should complete in under 1 second');
        $this->assertLessThan(2.0, $queryTime3, 'Aggregation query should complete in under 2 seconds');
        $this->assertLessThan(0.5, $queryTime4, 'Balance query should complete in under 0.5 seconds');

        // Output performance metrics
        echo "\n=== Stock Book Performance Test Results ===\n";
        echo "Total Products: " . count($products) . "\n";
        echo "Total Locations: " . count($locations) . "\n";
        echo "Total Stock Cards: " . count($stockCards) . "\n";
        echo "Total Stock Balances: " . count($stockBalances) . "\n";
        echo "\n=== Query Performance ===\n";
        echo "Filtered Query Time: " . number_format($queryTime1 * 1000, 2) . " ms\n";
        echo "Complex Query Time: " . number_format($queryTime2 * 1000, 2) . " ms\n";
        echo "Aggregation Query Time: " . number_format($queryTime3 * 1000, 2) . " ms\n";
        echo "Balance Query Time: " . number_format($queryTime4 * 1000, 2) . " ms\n";

        // Test index usage
        $explainQuery = DB::select('EXPLAIN SELECT * FROM stock_cards WHERE product_id = ? AND location_id = ? AND transaction_date >= ? ORDER BY transaction_date DESC LIMIT 100', [
            $products[0]->id,
            $locations[0]->id,
            now()->subDays(30)->format('Y-m-d')
        ]);

        echo "\n=== Index Usage Analysis ===\n";
        foreach ($explainQuery as $row) {
            echo $row->explain . "\n";
        }
    }
}
