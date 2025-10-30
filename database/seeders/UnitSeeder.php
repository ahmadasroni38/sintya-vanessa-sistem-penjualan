<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Length Units
            ['code' => 'M', 'name' => 'Meter', 'symbol' => 'm', 'description' => 'Unit of length'],
            ['code' => 'CM', 'name' => 'Centimeter', 'symbol' => 'cm', 'description' => 'Unit of length'],
            ['code' => 'MM', 'name' => 'Millimeter', 'symbol' => 'mm', 'description' => 'Unit of length'],
            ['code' => 'KM', 'name' => 'Kilometer', 'symbol' => 'km', 'description' => 'Unit of length'],
            ['code' => 'IN', 'name' => 'Inch', 'symbol' => 'in', 'description' => 'Unit of length'],
            ['code' => 'FT', 'name' => 'Feet', 'symbol' => 'ft', 'description' => 'Unit of length'],

            // Weight Units
            ['code' => 'KG', 'name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Unit of mass'],
            ['code' => 'G', 'name' => 'Gram', 'symbol' => 'g', 'description' => 'Unit of mass'],
            ['code' => 'MG', 'name' => 'Milligram', 'symbol' => 'mg', 'description' => 'Unit of mass'],
            ['code' => 'TON', 'name' => 'Ton', 'symbol' => 't', 'description' => 'Unit of mass'],
            ['code' => 'LB', 'name' => 'Pound', 'symbol' => 'lb', 'description' => 'Unit of mass'],
            ['code' => 'OZ', 'name' => 'Ounce', 'symbol' => 'oz', 'description' => 'Unit of mass'],

            // Volume Units
            ['code' => 'L', 'name' => 'Liter', 'symbol' => 'L', 'description' => 'Unit of volume'],
            ['code' => 'ML', 'name' => 'Milliliter', 'symbol' => 'ml', 'description' => 'Unit of volume'],
            ['code' => 'GAL', 'name' => 'Gallon', 'symbol' => 'gal', 'description' => 'Unit of volume'],
            ['code' => 'M3', 'name' => 'Cubic Meter', 'symbol' => 'm³', 'description' => 'Unit of volume'],

            // Quantity Units
            ['code' => 'PCS', 'name' => 'Pieces', 'symbol' => 'pcs', 'description' => 'Unit of quantity'],
            ['code' => 'UNIT', 'name' => 'Unit', 'symbol' => 'unit', 'description' => 'Unit of quantity'],
            ['code' => 'SET', 'name' => 'Set', 'symbol' => 'set', 'description' => 'Unit of quantity'],
            ['code' => 'PAIR', 'name' => 'Pair', 'symbol' => 'pair', 'description' => 'Unit of quantity'],
            ['code' => 'DOZEN', 'name' => 'Dozen', 'symbol' => 'dz', 'description' => 'Unit of quantity (12 units)'],
            ['code' => 'GROSS', 'name' => 'Gross', 'symbol' => 'gr', 'description' => 'Unit of quantity (144 units)'],

            // Packaging Units
            ['code' => 'BOX', 'name' => 'Box', 'symbol' => 'box', 'description' => 'Packaging unit'],
            ['code' => 'BAG', 'name' => 'Bag', 'symbol' => 'bag', 'description' => 'Packaging unit'],
            ['code' => 'CARTON', 'name' => 'Carton', 'symbol' => 'ctn', 'description' => 'Packaging unit'],
            ['code' => 'PACK', 'name' => 'Pack', 'symbol' => 'pack', 'description' => 'Packaging unit'],
            ['code' => 'PALLET', 'name' => 'Pallet', 'symbol' => 'plt', 'description' => 'Packaging unit'],
            ['code' => 'ROLL', 'name' => 'Roll', 'symbol' => 'roll', 'description' => 'Packaging unit'],
            ['code' => 'BUNDLE', 'name' => 'Bundle', 'symbol' => 'bdl', 'description' => 'Packaging unit'],

            // Area Units
            ['code' => 'M2', 'name' => 'Square Meter', 'symbol' => 'm²', 'description' => 'Unit of area'],
            ['code' => 'FT2', 'name' => 'Square Feet', 'symbol' => 'ft²', 'description' => 'Unit of area'],

            // Time Units (for services)
            ['code' => 'HR', 'name' => 'Hour', 'symbol' => 'hr', 'description' => 'Unit of time'],
            ['code' => 'DAY', 'name' => 'Day', 'symbol' => 'day', 'description' => 'Unit of time'],
            ['code' => 'MONTH', 'name' => 'Month', 'symbol' => 'mo', 'description' => 'Unit of time'],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'code' => $unit['code'],
                'name' => $unit['name'],
                'symbol' => $unit['symbol'] ?? null,
                'description' => $unit['description'] ?? null,
                'is_active' => true,
            ]);
        }
    }
}
