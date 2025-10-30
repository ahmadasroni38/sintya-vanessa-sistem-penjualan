<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Product;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user for creating stock in
        $user = User::first();
        if (!$user) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Get a location
        $location = Location::where('is_active', true)->first();
        if (!$location) {
            $this->command->error('No active locations found. Please run LocationSeeder first.');
            return;
        }

        // Get some products
        $products = Product::where('is_active', true)->limit(5)->get();
        if ($products->count() < 2) {
            $this->command->error('Need at least 2 products. Please run ProductSeeder first.');
            return;
        }

        $this->command->info('Creating sample stock in transactions...');

        DB::beginTransaction();
        try {
            // Create 3 draft stock in transactions
            for ($i = 1; $i <= 3; $i++) {
                $stockIn = StockIn::create([
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'location_id' => $location->id,
                    'supplier_name' => 'Supplier ' . $i,
                    'reference_number' => 'PO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'notes' => 'Sample stock in transaction ' . $i,
                    'status' => 'draft',
                    'created_by' => $user->id,
                    'total_price' => 0, // Will be calculated
                ]);

                // Add 2-3 random product items
                $itemCount = rand(2, min(3, $products->count()));
                $totalPrice = 0;

                foreach ($products->random($itemCount) as $product) {
                    $quantity = rand(5, 50);
                    $unitPrice = rand(10000, 500000);

                    $detail = $stockIn->details()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'notes' => 'Sample item for ' . $product->product_name,
                    ]);

                    $totalPrice += $detail->total_price;
                }

                $stockIn->update(['total_price' => $totalPrice]);

                $this->command->info("Created draft stock in: {$stockIn->transaction_number} with {$itemCount} items");
            }

            // Create 2 posted stock in transactions
            for ($i = 4; $i <= 5; $i++) {
                $stockIn = StockIn::create([
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'location_id' => $location->id,
                    'supplier_name' => 'Supplier ' . $i,
                    'reference_number' => 'PO-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'notes' => 'Sample stock in transaction ' . $i,
                    'status' => 'draft',
                    'created_by' => $user->id,
                    'total_price' => 0,
                ]);

                // Add items
                $itemCount = rand(2, min(4, $products->count()));
                $totalPrice = 0;

                foreach ($products->random($itemCount) as $product) {
                    $quantity = rand(10, 100);
                    $unitPrice = rand(20000, 1000000);

                    $detail = $stockIn->details()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'notes' => 'Posted item for ' . $product->product_name,
                    ]);

                    $totalPrice += $detail->total_price;
                }

                $stockIn->update(['total_price' => $totalPrice]);

                // Post the transaction
                $stockIn->post($user->id);

                $this->command->info("Created posted stock in: {$stockIn->transaction_number} with {$itemCount} items");
            }

            DB::commit();
            $this->command->info('Successfully created 5 stock in transactions (3 draft, 2 posted)');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating stock in transactions: ' . $e->getMessage());
        }
    }
}
