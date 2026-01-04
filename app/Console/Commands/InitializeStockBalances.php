<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\StockBalance;
use App\Models\StockCard;

class InitializeStockBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:initialize-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize StockBalance table from existing StockCard data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing StockBalance table from StockCard data...');

        // Clear existing stock balances
        StockBalance::query()->delete();
        $this->info('Cleared existing StockBalance records');

        // Get unique product-location combinations from stock cards
        $productLocations = StockCard::select('product_id', 'location_id')
            ->distinct()
            ->get();

        $this->info("Found {$productLocations->count()} unique product-location combinations");

        $processed = 0;
        $bar = $this->output->createProgressBar($productLocations->count());

        foreach ($productLocations as $pl) {
            // Get the latest balance for this product-location
            $latestCard = StockCard::where('product_id', $pl->product_id)
                ->where('location_id', $pl->location_id)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($latestCard) {
                // Determine status based on balance
                $status = 'in_stock';
                if ($latestCard->balance <= 0) {
                    $status = 'out_of_stock';
                }

                StockBalance::updateOrCreate(
                    [
                        'product_id' => $pl->product_id,
                        'location_id' => $pl->location_id,
                    ],
                    [
                        'current_balance' => $latestCard->balance,
                        'status' => $status,
                        'last_transaction_date' => $latestCard->transaction_date,
                        'last_transaction_type' => $latestCard->transaction_type,
                        'updated_at' => now(),
                    ]
                );
            }

            $processed++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully initialized {$processed} StockBalance records");
        $this->info('StockBalance initialization completed!');

        return Command::SUCCESS;
    }
}
