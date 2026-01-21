<?php

/**
 * Generate 78 random sales transactions for dates 1-9 January 2026
 * Following the SalesController store process
 */

// Database configuration
$host = 'localhost';
$dbname = 'vassion_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get available data from database
$customers = $pdo->query("SELECT id FROM customers")->fetchAll(PDO::FETCH_COLUMN);
$locations = $pdo->query("SELECT id FROM locations WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
$products = $pdo->query("SELECT id, selling_price FROM products WHERE is_active = 1 AND selling_price > 0")->fetchAll(PDO::FETCH_ASSOC);

if (empty($customers)) {
    die("No customers found. Please create at least one customer first.\n");
}

if (empty($locations)) {
    die("No locations found. Please create at least one location first.\n");
}

if (empty($products)) {
    die("No products found. Please create at least one product with selling_price > 0.\n");
}

// Get next transaction number
$lastTransaction = $pdo->query("SELECT transaction_number FROM sales ORDER BY id DESC LIMIT 1")->fetchColumn();
$lastNumber = $lastTransaction ? (int)str_replace('SO-2026-', '', $lastTransaction) : 0;
$nextNumber = $lastNumber + 1;

// Generate 78 transactions
$startDate = new DateTime('2026-01-01');
$endDate = new DateTime('2026-01-09 23:59:59');

$paymentMethods = ['cash', 'transfer', 'card', 'e-wallet'];
$statuses = ['draft', 'posted'];
$notes = ['Walk-in customer', 'Regular customer', 'First time buyer', 'Promo sale', 'Normal sale', 'Referral', 'Online order', 'In-store purchase'];

echo "Generating 78 sales transactions...\n\n";

$salesData = [];
$saleDetailsData = [];

for ($i = 1; $i <= 78; $i++) {
    // Generate random date between Jan 1-9, 2026
    $randomTimestamp = mt_rand($startDate->getTimestamp(), $endDate->getTimestamp());
    $transactionDate = date('Y-m-d H:i:s', $randomTimestamp);
    $transactionDateOnly = date('Y-m-d', $randomTimestamp);
    $createdAt = date('Y-m-d H:i:s', $randomTimestamp + mt_rand(0, 3600));

    // Generate transaction number
    $transactionNumber = 'SO-2026-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    $nextNumber++;

    // Random customer and location
    $customerId = $customers[array_rand($customers)];
    $locationId = $locations[array_rand($locations)];

    // Random number of items (1-5 items per sale)
    $numItems = mt_rand(1, 5);

    // Generate sale details
    $subtotal = 0;
    $taxAmount = 0;
    $discountAmount = 0;

    $selectedProducts = [];
    $details = [];

    for ($j = 0; $j < $numItems; $j++) {
        // Select random product
        $product = $products[array_rand($products)];
        $productId = $product['id'];
        $unitPrice = $product['selling_price'];

        // Random quantity (1-10)
        $quantity = mt_rand(1, 10);

        // Random discount percent (0-20%)
        $discountPercent = mt_rand(0, 20);

        // Random tax percent (0, 11%)
        $taxPercent = mt_rand(0, 1) == 1 ? 11 : 0;

        // Calculate line total
        $lineTotal = $quantity * $unitPrice;

        // Calculate discount amount
        $discountAmountLine = $lineTotal * ($discountPercent / 100);
        $lineAfterDiscount = $lineTotal - $discountAmountLine;

        // Calculate tax amount
        $taxAmountLine = $lineAfterDiscount * ($taxPercent / 100);
        $totalPrice = $lineAfterDiscount + $taxAmountLine;

        $subtotal += $lineAfterDiscount;
        $taxAmount += $taxAmountLine;
        $discountAmount += $discountAmountLine;

        $details[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmountLine,
            'tax_percent' => $taxPercent,
            'tax_amount' => $taxAmountLine,
            'total_price' => $totalPrice,
            'notes' => null
        ];

        $selectedProducts[] = $productId;
    }

    // Calculate total amount
    $totalAmount = $subtotal + $taxAmount;

    // Random paid amount (sometimes exact, sometimes more)
    $paidAmount = $totalAmount;
    if (mt_rand(0, 1) == 1) {
        // Add some extra amount for change
        $paidAmount += mt_rand(0, 50000);
    }

    $changeAmount = $paidAmount - $totalAmount;
    if ($changeAmount < 0) $changeAmount = 0;

    // Random payment method
    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

    // Random status (mostly posted)
    $status = mt_rand(0, 10) < 8 ? 'posted' : 'draft';

    // Random notes
    $note = mt_rand(0, 1) == 1 ? $notes[array_rand($notes)] : null;

    // Random created_by (assuming user IDs 1-4 exist)
    $createdBy = mt_rand(1, 4);

    // Posted info if status is posted
    $postedBy = $status == 'posted' ? $createdBy : null;
    $postedAt = $status == 'posted' ? date('Y-m-d H:i:s', $randomTimestamp + mt_rand(60, 300)) : null;

    // Store sale data
    $salesData[] = [
        'transaction_number' => $transactionNumber,
        'transaction_date' => $transactionDateOnly,
        'customer_id' => $customerId,
        'location_id' => $locationId,
        'subtotal' => $subtotal,
        'tax_amount' => $taxAmount,
        'discount_amount' => $discountAmount,
        'total_amount' => $totalAmount,
        'paid_amount' => $paidAmount,
        'change_amount' => $changeAmount,
        'payment_method' => $paymentMethod,
        'notes' => $note,
        'status' => $status,
        'created_by' => $createdBy,
        'posted_by' => $postedBy,
        'posted_at' => $postedAt,
        'created_at' => $createdAt,
        'updated_at' => $createdAt
    ];

    // Store sale details data
    foreach ($details as $detail) {
        $saleDetailsData[] = array_merge($detail, [
            'sale_id' => $i + 2, // Offset by existing sales (assuming 2 existing sales)
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ]);
    }

    echo "Transaction {$i}: {$transactionNumber} - Date: {$transactionDateOnly} - Items: {$numItems} - Total: " . number_format($totalAmount, 2) . "\n";
}

// Get the next sale ID
$nextSaleId = $pdo->query("SELECT MAX(id) FROM sales")->fetchColumn() + 1;

// Generate SQL INSERT statements
$sql = "-- Generated Sales Data for 78 transactions (Jan 1-9, 2026)\n";
$sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";

// Sales table insert
$sql .= "-- Insert into sales table\n";
$sql .= "INSERT INTO `sales` (`transaction_number`, `transaction_date`, `customer_id`, `location_id`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `paid_amount`, `change_amount`, `payment_method`, `notes`, `status`, `created_by`, `posted_by`, `posted_at`, `created_at`, `updated_at`) VALUES\n";

$saleValues = [];
foreach ($salesData as $sale) {
    $saleValues[] = sprintf(
        "('%s', '%s', %d, %d, %.2f, %.2f, %.2f, %.2f, %.2f, %.2f, '%s', %s, '%s', %d, %s, %s, '%s', '%s')",
        $sale['transaction_number'],
        $sale['transaction_date'],
        $sale['customer_id'],
        $sale['location_id'],
        $sale['subtotal'],
        $sale['tax_amount'],
        $sale['discount_amount'],
        $sale['total_amount'],
        $sale['paid_amount'],
        $sale['change_amount'],
        $sale['payment_method'],
        $sale['notes'] ? "'{$sale['notes']}'" : 'NULL',
        $sale['status'],
        $sale['created_by'],
        $sale['posted_by'] ?? 'NULL',
        $sale['posted_at'] ? "'{$sale['posted_at']}'" : 'NULL',
        $sale['created_at'],
        $sale['updated_at']
    );
}

$sql .= implode(",\n", $saleValues) . ";\n\n";

// Sale details table insert
$sql .= "-- Insert into sale_details table\n";
$sql .= "INSERT INTO `sale_details` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount_percent`, `discount_amount`, `tax_percent`, `tax_amount`, `total_price`, `notes`, `created_at`, `updated_at`) VALUES\n";

$detailValues = [];
$currentSaleId = $nextSaleId;
$detailIndex = 0;

foreach ($salesData as $saleIndex => $sale) {
    $saleId = $nextSaleId + $saleIndex;

    // Find details for this sale
    $detailsCount = 0;
    foreach ($saleDetailsData as $detail) {
        if ($detail['sale_id'] == $saleIndex + 2) {
            $detailValues[] = sprintf(
                "(%d, %d, %.2f, %.2f, %.2f, %.2f, %.2f, %.2f, %.2f, %s, '%s', '%s')",
                $saleId,
                $detail['product_id'],
                $detail['quantity'],
                $detail['unit_price'],
                $detail['discount_percent'],
                $detail['discount_amount'],
                $detail['tax_percent'],
                $detail['tax_amount'],
                $detail['total_price'],
                $detail['notes'] ? "'{$detail['notes']}'" : 'NULL',
                $detail['created_at'],
                $detail['updated_at']
            );
            $detailsCount++;
        }
    }
}

$sql .= implode(",\n", $detailValues) . ";\n";

// Save SQL to file
$filename = 'sales_data_78_transactions.sql';
file_put_contents($filename, $sql);

echo "\n\n";
echo "========================================\n";
echo "SQL file generated: {$filename}\n";
echo "Total sales: " . count($salesData) . "\n";
echo "Total sale details: " . count($detailValues) . "\n";
echo "========================================\n";
echo "\nTo insert this data into your database, run:\n";
echo "mysql -u root -p vassion_db < {$filename}\n";
echo "\nOr copy the SQL from the file and execute in your database management tool.\n";
