<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Gudang Pusat Jakarta',
                'code' => 'GD-JKT',
                'address' => 'Jl. Raya Jakarta No. 123',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12345',
                'description' => 'Gudang utama di Jakarta untuk distribusi area Jabodetabek',
                'is_active' => true,
                'metadata' => json_encode([
                    'contact_person' => 'Budi Santoso',
                    'phone' => '021-12345678',
                    'type' => 'warehouse',
                    'capacity' => 1000.00,
                ]),
            ],
            [
                'name' => 'Gudang Bandung',
                'code' => 'GD-BDG',
                'address' => 'Jl. Soekarno Hatta No. 456',
                'city' => 'Bandung',
                'state' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '40123',
                'description' => 'Gudang regional Bandung',
                'is_active' => true,
                'metadata' => json_encode([
                    'contact_person' => 'Dedi Wijaya',
                    'phone' => '022-87654321',
                    'type' => 'warehouse',
                    'capacity' => 800.00,
                ]),
            ],
            [
                'name' => 'Gudang Surabaya',
                'code' => 'GD-SBY',
                'address' => 'Jl. Ahmad Yani No. 789',
                'city' => 'Surabaya',
                'state' => 'Jawa Timur',
                'country' => 'Indonesia',
                'postal_code' => '60234',
                'description' => 'Gudang regional Surabaya untuk distribusi Jawa Timur',
                'is_active' => true,
                'metadata' => json_encode([
                    'contact_person' => 'Eko Prasetyo',
                    'phone' => '031-55566677',
                    'type' => 'warehouse',
                    'capacity' => 750.00,
                ]),
            ],
            [
                'name' => 'Toko Jakarta Pusat',
                'code' => 'TK-JKTPST',
                'address' => 'Jl. Thamrin No. 100',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '10350',
                'description' => 'Toko retail di Jakarta Pusat',
                'is_active' => true,
                'metadata' => json_encode([
                    'contact_person' => 'Ani Susanti',
                    'phone' => '021-99988877',
                    'type' => 'store',
                    'capacity' => 100.00,
                ]),
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        $this->command->info('Locations seeded successfully!');
    }
}
