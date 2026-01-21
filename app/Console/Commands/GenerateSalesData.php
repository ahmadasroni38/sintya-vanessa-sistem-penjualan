<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateSalesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:generate {count=78 : Number of transactions to generate} {--start-date=2026-01-01 : Start date (Y-m-d)} {--end-date=2026-01-09 : End date (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate random sales transactions following SalesController store process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');

        $this->info("Generating {$count} sales transactions from {$startDate} to {$endDate}...");

        // Get available data
        $customers = Customer::all();
        $locations = Location::active()->get();
        $products = Product::active()->where('selling_price', '>', 0)->get();

        if ($customers->isEmpty()) {
            $this->error('No customers found. Please create at least one customer first.');
            return 1;
        }

        if ($locations->isEmpty()) {
            $this->error('No locations found. Please create at least one location first.');
            return 1;
        }

        if ($products->isEmpty()) {
            $this->error('No products found. Please create at least one product with selling_price > 0.');
            return 1;
        }

        // Get next transaction number
        $lastTransaction = Sale::orderBy('id', 'desc')->first();
        $lastNumber = $lastTransaction ? (int)str_replace('SO-2026-', '', $lastTransaction->transaction_number) : 0;
        $nextNumber = $lastNumber + 1;

        // Configuration - must match database enum values
        $paymentMethods = ['cash', 'transfer', 'credit'];
        $notes = ['Walk-in customer', 'Regular customer', 'First time buyer', 'Promo sale', 'Normal sale', 'Referral', 'Online order', 'In-store purchase'];

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $count; $i++) {
                // Generate random date
                $randomTimestamp = mt_rand(strtotime($startDate), strtotime($endDate . ' 23:59:59'));
                $transactionDate = date('Y-m-d', $randomTimestamp);
                $createdAt = date('Y-m-d H:i:s', $randomTimestamp + mt_rand(0, 3600));

                // Generate transaction number
                $transactionNumber = 'SO-2026-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                $nextNumber++;

                // Random customer and location
                $customer = $customers->random();
                $location = $locations->random();

                // Random number of items (1-5 items per sale)
                $numItems = mt_rand(1, 5);

                // Create sale
                $sale = Sale::create([
                    'transaction_number' => $transactionNumber,
                    'transaction_date' => $transactionDate,
                    'customer_id' => $customer->id,
                    'location_id' => $location->id,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'notes' => mt_rand(0, 1) == 1 ? $notes[array_rand($notes)] : null,
                    'status' => 'draft',
                    'created_by' => 1, // Default to user ID 1
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Generate sale details
                $subtotal = 0;
                $taxAmount = 0;
                $discountAmount = 0;

                for ($j = 0; $j < $numItems; $j++) {
                    $product = $products->random();
                    $quantity = mt_rand(1, 10);
                    $unitPrice = $product->selling_price;
                    $discountPercent = mt_rand(0, 20);
                    $taxPercent = mt_rand(0, 1) == 1 ? 11 : 0;

                    // Calculate line totals
                    $lineTotal = $quantity * $unitPrice;
                    $discountAmountLine = $lineTotal * ($discountPercent / 100);
                    $lineAfterDiscount = $lineTotal - $discountAmountLine;
                    $taxAmountLine = $lineAfterDiscount * ($taxPercent / 100);
                    $totalPrice = $lineAfterDiscount + $taxAmountLine;

                    $subtotal += $lineAfterDiscount;
                    $taxAmount += $taxAmountLine;
                    $discountAmount += $discountAmountLine;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percent' => $discountPercent,
                        'discount_amount' => $discountAmountLine,
                        'tax_percent' => $taxPercent,
                        'tax_amount' => $taxAmountLine,
                        'total_price' => $totalPrice,
                        'notes' => null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // Calculate totals
                $totalAmount = $subtotal + $taxAmount;

                // Random paid amount
                $paidAmount = $totalAmount;
                if (mt_rand(0, 1) == 1) {
                    $paidAmount += mt_rand(0, 50000);
                }
                $changeAmount = max(0, $paidAmount - $totalAmount);

                // Update sale with totals
                $sale->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $changeAmount,
                ]);

                // Randomly post the sale (80% chance)
                if (mt_rand(0, 10) < 8) {
                    $postedAt = date('Y-m-d H:i:s', $randomTimestamp + mt_rand(60, 300));
                    $sale->update([
                        'status' => 'posted',
                        'posted_by' => 1,
                        'posted_at' => $postedAt,
                    ]);
                }

                $bar->advance();
            }

            DB::commit();
            $bar->finish();

            $this->newLine(2);
            $this->info("✅ Successfully generated {$count} sales transactions!");
            $this->info("📊 Date range: {$startDate} to {$endDate}");
            $this->info("🔢 Transaction numbers: SO-2026-" . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT) . " to SO-2026-" . str_pad($nextNumber - 1, 5, '0', STR_PAD_LEFT));

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("❌ Failed to generate sales data: " . $e->getMessage());
            return 1;
        }
    }
}
