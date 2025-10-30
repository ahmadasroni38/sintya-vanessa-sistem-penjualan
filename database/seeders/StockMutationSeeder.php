<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMutation;
use App\Models\StockMutationDetail;
use App\Models\Location;
use App\Models\Product;

class StockMutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing locations and products
        $locations = Location::all();
        $products = Product::all();

        if ($locations->count() < 2 || $products->count() < 1) {
            $this->command->info('Need at least 2 locations and 1 product to create stock mutations');
            return;
        }

        // Create sample stock mutations
        $statuses = ['draft', 'pending', 'approved', 'completed'];

        for ($i = 1; $i <= 10; $i++) {
            $fromLocation = $locations->random();
            $toLocation = $locations->where('id', '!=', $fromLocation->id)->random();

            $mutation = StockMutation::create([
                'transaction_number' => 'SM-' . date('Y') . str_pad($i, 5, '0', STR_PAD_LEFT),
                'transaction_date' => now()->subDays($i),
                'from_location_id' => $fromLocation->id,
                'to_location_id' => $toLocation->id,
                'reference_number' => 'REF-' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'notes' => 'Sample stock mutation ' . $i,
                'status' => $statuses[array_rand($statuses)],
                'created_by' => 1,
            ]);

            // Add random number of details (1-3 items per mutation)
            $detailCount = rand(1, 3);
            for ($j = 1; $j <= $detailCount; $j++) {
                StockMutationDetail::create([
                    'stock_mutation_id' => $mutation->id,
                    'product_id' => $products->random()->id,
                    'quantity' => rand(1, 100),
                    'available_stock' => rand(50, 200),
                    'notes' => 'Detail note ' . $j,
                ]);
            }
        }

        $this->command->info('Created 10 sample stock mutations');
    }
}
