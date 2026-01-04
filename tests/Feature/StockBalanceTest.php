<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\StockBalance;
use App\Models\StockCard;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockMutation;
use App\Models\StockMutationDetail;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\User;

class StockBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->user = User::factory()->create();
        $this->location = Location::factory()->create();
        $this->product = Product::factory()->create();
    }

    public function test_stock_in_updates_stock_balance()
    {
        // Create stock in transaction
        $stockIn = StockIn::create([
            'transaction_number' => 'SI-TEST-001',
            'transaction_date' => now()->format('Y-m-d'),
            'location_id' => $this->location->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        StockInDetail::create([
            'stock_in_id' => $stockIn->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_price' => 10.00,
            'total_price' => 1000.00,
        ]);

        // Post the stock in
        $stockIn->post($this->user->id);

        // Verify stock balance is updated
        $stockBalance = StockBalance::where('product_id', $this->product->id)
            ->where('location_id', $this->location->id)
            ->first();

        $this->assertNotNull($stockBalance);
        $this->assertEquals(100, $stockBalance->current_balance);
        $this->assertEquals('stock_in', $stockBalance->last_transaction_type);
        $this->assertEquals('in_stock', $stockBalance->status);
    }

    public function test_sale_updates_stock_balance()
    {
        // First create stock in to have inventory
        $stockIn = StockIn::create([
            'transaction_number' => 'SI-TEST-001',
            'transaction_date' => now()->format('Y-m-d'),
            'location_id' => $this->location->id,
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        StockInDetail::create([
            'stock_in_id' => $stockIn->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_price' => 10.00,
            'total_price' => 1000.00,
        ]);

        $stockIn->createStockCards();

        // Create sale transaction
        $sale = Sale::create([
            'sale_number' => 'SALE-TEST-001',
            'sale_date' => now()->format('Y-m-d'),
            'customer_id' => 1, // Assuming customer exists
            'location_id' => $this->location->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 30,
            'unit_price' => 15.00,
            'total_price' => 450.00,
        ]);

        // Process the sale
        $sale->process($this->user->id);

        // Verify stock balance is updated
        $stockBalance = StockBalance::where('product_id', $this->product->id)
            ->where('location_id', $this->location->id)
            ->first();

        $this->assertNotNull($stockBalance);
        $this->assertEquals(70, $stockBalance->current_balance); // 100 - 30
        $this->assertEquals('sale', $stockBalance->last_transaction_type);
    }

    public function test_stock_mutation_updates_stock_balance()
    {
        // Create source and destination locations
        $sourceLocation = Location::factory()->create();
        $destLocation = Location::factory()->create();

        // Create stock in at source location
        $stockIn = StockIn::create([
            'transaction_number' => 'SI-TEST-001',
            'transaction_date' => now()->format('Y-m-d'),
            'location_id' => $sourceLocation->id,
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        StockInDetail::create([
            'stock_in_id' => $stockIn->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_price' => 10.00,
            'total_price' => 1000.00,
        ]);

        $stockIn->createStockCards();

        // Create mutation transaction
        $mutation = StockMutation::create([
            'transaction_number' => 'SM-TEST-001',
            'transaction_date' => now()->format('Y-m-d'),
            'from_location_id' => $sourceLocation->id,
            'to_location_id' => $destLocation->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        StockMutationDetail::create([
            'stock_mutation_id' => $mutation->id,
            'product_id' => $this->product->id,
            'quantity' => 30,
            'notes' => 'Test mutation',
        ]);

        // Process the mutation
        $mutation->submit($this->user->id);
        $mutation->approve($this->user->id);
        $mutation->complete($this->user->id);

        // Verify source location balance
        $sourceBalance = StockBalance::where('product_id', $this->product->id)
            ->where('location_id', $sourceLocation->id)
            ->first();

        $this->assertEquals(70, $sourceBalance->current_balance); // 100 - 30

        // Verify destination location balance
        $destBalance = StockBalance::where('product_id', $this->product->id)
            ->where('location_id', $destLocation->id)
            ->first();

        $this->assertEquals(30, $destBalance->current_balance);
    }

    public function test_stock_adjustment_updates_stock_balance()
    {
        // Create stock in first
        $stockIn = StockIn::create([
            'transaction_number' => 'SI-TEST-001',
            'transaction_date' => now()->format('Y-m-d'),
            'location_id' => $this->location->id,
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        StockInDetail::create([
            'stock_in_id' => $stockIn->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'unit_price' => 10.00,
            'total_price' => 1000.00,
        ]);

        $stockIn->createStockCards();

        // Create adjustment transaction
        $adjustment = StockAdjustment::create([
            'adjustment_number' => 'ADJ-TEST-001',
            'adjustment_date' => now()->format('Y-m-d'),
            'location_id' => $this->location->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        StockAdjustmentDetail::create([
            'stock_adjustment_id' => $adjustment->id,
            'product_id' => $this->product->id,
            'system_quantity' => 100,
            'actual_quantity' => 95,
            'difference_quantity' => -5,
            'adjustment_type' => 'decrease',
            'reason' => 'Test adjustment',
        ]);

        // Post the adjustment
        $adjustment->post($this->user->id);

        // Verify stock balance is updated
        $stockBalance = StockBalance::where('product_id', $this->product->id)
            ->where('location_id', $this->location->id)
            ->first();

        $this->assertEquals(95, $stockBalance->current_balance); // 100 - 5
        $this->assertEquals('adjustment', $stockBalance->last_transaction_type);
    }
}
