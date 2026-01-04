<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockBalance;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMutation;
use App\Models\StockMutationDetail;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\User;
use App\Models\Product;
use App\Models\Location;
use App\Models\Customer;

class TestStockBalanceIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test StockBalance integration with transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing StockBalance integration...');

        try {
            // Test Stock In
            $this->testStockInIntegration();
            $this->info('✓ Stock In integration test passed');

            // Test Sale
            $this->testSaleIntegration();
            $this->info('✓ Sale integration test passed');

            // Test Stock Mutation
            $this->testStockMutationIntegration();
            $this->info('✓ Stock Mutation integration test passed');

            // Test Stock Adjustment
            $this->testStockAdjustmentIntegration();
            $this->info('✓ Stock Adjustment integration test passed');

            $this->info('🎉 All StockBalance integration tests passed!');

        } catch (\Exception $e) {
            $this->error('❌ Integration test failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function testStockInIntegration()
    {
        $user = User::first();
        $location = Location::first();
        $product = Product::first();

        if (!$user || !$location || !$product) {
            $this->warn('Skipping Stock In test - missing required data');
            return;
        }

        $stockIn = StockIn::create([
            'transaction_number' => 'SI-TEST-' . time(),
            'transaction_date' => now()->format('Y-m-d'),
            'location_id' => $location->id,
            'status' => 'draft',
            'created_by' => $user->id,
            'total_items' => 0,
            'total_price' => 0,
            'description' => 'Test Stock In',
        ]);

        StockInDetail::create([
            'stock_in_id' => $stockIn->id,
            'product_id' => $product->id,
            'quantity' => 100,
            'unit_price' => 10.00,
            'total_price' => 1000.00,
        ]);

        $stockIn->post($user->id);

        $stockBalance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->first();

        if (!$stockBalance || $stockBalance->current_balance != 100) {
            throw new \Exception('Stock In integration failed');
        }
    }

    private function testSaleIntegration()
    {
        $user = User::first();
        $location = Location::first();
        $product = Product::first();
        $customer = Customer::first();

        if (!$user || !$location || !$product || !$customer) {
            $this->warn('Skipping Sale test - missing required data');
            return;
        }

        // First ensure stock exists
        $stockBalance = StockBalance::updateOrCreate(
            ['product_id' => $product->id, 'location_id' => $location->id],
            ['current_balance' => 100]
        );

        $sale = Sale::create([
            'sale_number' => 'SALE-TEST-' . time(),
            'sale_date' => now()->format('Y-m-d'),
            'customer_id' => $customer->id,
            'location_id' => $location->id,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 30,
            'unit_price' => 15.00,
            'total_price' => 450.00,
        ]);

        $sale->process($user->id);

        $stockBalance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->first();

        if (!$stockBalance || $stockBalance->current_balance != 70) {
            throw new \Exception('Sale integration failed');
        }
    }

    private function testStockMutationIntegration()
    {
        $user = User::first();
        $locations = Location::take(2)->get();
        $product = Product::first();

        if (!$user || !$product || $locations->count() < 2) {
            $this->warn('Skipping Stock Mutation test - missing required data');
            return;
        }

        $sourceLocation = $locations[0];
        $destLocation = $locations[1];

        // Ensure stock exists at source
        StockBalance::updateOrCreate(
            ['product_id' => $product->id, 'location_id' => $sourceLocation->id],
            ['current_balance' => 100]
        );

        $mutation = StockMutation::create([
            'transaction_number' => 'SM-TEST-' . time(),
            'transaction_date' => now()->format('Y-m-d'),
            'from_location_id' => $sourceLocation->id,
            'to_location_id' => $destLocation->id,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        StockMutationDetail::create([
            'stock_mutation_id' => $mutation->id,
            'product_id' => $product->id,
            'quantity' => 30,
        ]);

        $mutation->submit($user->id);
        $mutation->approve($user->id);
        $mutation->complete($user->id);

        $sourceBalance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $sourceLocation->id)
            ->first();

        $destBalance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $destLocation->id)
            ->first();

        if (!$sourceBalance || $sourceBalance->current_balance != 70) {
            throw new \Exception('Stock Mutation source integration failed');
        }

        if (!$destBalance || $destBalance->current_balance != 30) {
            throw new \Exception('Stock Mutation destination integration failed');
        }
    }

    private function testStockAdjustmentIntegration()
    {
        $user = User::first();
        $location = Location::first();
        $product = Product::first();

        if (!$user || !$location || !$product) {
            $this->warn('Skipping Stock Adjustment test - missing required data');
            return;
        }

        // Ensure stock exists
        StockBalance::updateOrCreate(
            ['product_id' => $product->id, 'location_id' => $location->id],
            ['current_balance' => 100]
        );

        $adjustment = StockAdjustment::create([
            'adjustment_number' => 'ADJ-TEST-' . time(),
            'adjustment_date' => now()->format('Y-m-d'),
            'location_id' => $location->id,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        StockAdjustmentDetail::create([
            'stock_adjustment_id' => $adjustment->id,
            'product_id' => $product->id,
            'system_quantity' => 100,
            'actual_quantity' => 95,
            'difference_quantity' => -5,
            'adjustment_type' => 'decrease',
            'reason' => 'Test adjustment',
        ]);

        $adjustment->post($user->id);

        $stockBalance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $location->id)
            ->first();

        if (!$stockBalance || $stockBalance->current_balance != 95) {
            throw new \Exception('Stock Adjustment integration failed');
        }
    }
}
