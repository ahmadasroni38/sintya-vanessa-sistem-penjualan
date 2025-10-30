<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Location;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $defaultLocation = Location::where('code', 'GD-JKT')->first();
        $unitUnit = \App\Models\Unit::where('code', 'UNIT')->first();
        $unitPcs = \App\Models\Unit::where('code', 'PCS')->first();

        $products = [
            [
                'product_code' => 'PRD-001',
                'product_name' => 'Laptop Asus ROG',
                'description' => 'Laptop gaming Asus ROG dengan spesifikasi tinggi',
                'unit_id' => $unitUnit->id,
                'product_type' => 'finished_goods',
                'purchase_price' => 15000000,
                'selling_price' => 18000000,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-002',
                'product_name' => 'Monitor LG 27"',
                'description' => 'Monitor LG 27 inch Full HD',
                'unit_id' => $unitUnit->id,
                'product_type' => 'finished_goods',
                'purchase_price' => 2500000,
                'selling_price' => 3200000,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-003',
                'product_name' => 'Keyboard Mechanical',
                'description' => 'Keyboard mechanical RGB gaming',
                'unit_id' => $unitUnit->id,
                'product_type' => 'finished_goods',
                'purchase_price' => 750000,
                'selling_price' => 1000000,
                'minimum_stock' => 15,
                'maximum_stock' => 150,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-004',
                'product_name' => 'Mouse Logitech G502',
                'description' => 'Mouse gaming Logitech G502 HERO',
                'unit_id' => $unitUnit->id,
                'product_type' => 'finished_goods',
                'purchase_price' => 650000,
                'selling_price' => 850000,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-005',
                'product_name' => 'SSD Samsung 1TB',
                'description' => 'SSD Samsung EVO 1TB NVMe',
                'unit_id' => $unitUnit->id,
                'product_type' => 'raw_material',
                'purchase_price' => 1500000,
                'selling_price' => 1900000,
                'minimum_stock' => 25,
                'maximum_stock' => 150,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-006',
                'product_name' => 'RAM Corsair 16GB',
                'description' => 'RAM Corsair Vengeance 16GB DDR4',
                'unit_id' => $unitUnit->id,
                'product_type' => 'raw_material',
                'purchase_price' => 950000,
                'selling_price' => 1200000,
                'minimum_stock' => 30,
                'maximum_stock' => 200,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-007',
                'product_name' => 'Thermal Paste',
                'description' => 'Thermal paste untuk CPU',
                'unit_id' => $unitPcs->id,
                'product_type' => 'consumable',
                'purchase_price' => 50000,
                'selling_price' => 75000,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
            [
                'product_code' => 'PRD-008',
                'product_name' => 'Cable HDMI 2.1',
                'description' => 'Kabel HDMI 2.1 panjang 2 meter',
                'unit_id' => $unitPcs->id,
                'product_type' => 'consumable',
                'purchase_price' => 100000,
                'selling_price' => 150000,
                'minimum_stock' => 40,
                'maximum_stock' => 300,
                'location_id' => $defaultLocation->id,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Products seeded successfully!');
    }
}
