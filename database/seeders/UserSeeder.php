<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@sintiya.com',
                'password' => Hash::make('admin123'),
                'role' => 'Super Admin',
                'phone' => '081234567890',
                'address' => 'Jakarta',
                'status' => 'active',
            ],
            [
                'name' => 'Accounting Manager',
                'email' => 'accounting@sintiya.com',
                'password' => Hash::make('accounting123'),
                'role' => 'Accounting Manager',
                'phone' => '081234567891',
                'address' => 'Jakarta',
                'status' => 'active',
            ],
            [
                'name' => 'Warehouse Manager',
                'email' => 'warehouse@sintiya.com',
                'password' => Hash::make('warehouse123'),
                'role' => 'Warehouse Manager',
                'phone' => '081234567892',
                'address' => 'Bandung',
                'status' => 'active',
            ],
            [
                'name' => 'Staff Gudang',
                'email' => 'staff@sintiya.com',
                'password' => Hash::make('staff123'),
                'role' => 'Staff',
                'phone' => '081234567893',
                'address' => 'Surabaya',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'phone_number' => $userData['phone'],
                'address' => $userData['address'],
                'status' => $userData['status'],
                'email_verified_at' => now(),
            ]);

            // Assign role to user
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('  Super Admin    : admin@sintiya.com / admin123');
        $this->command->info('  Accounting Mgr : accounting@sintiya.com / accounting123');
        $this->command->info('  Warehouse Mgr  : warehouse@sintiya.com / warehouse123');
        $this->command->info('  Staff          : staff@sintiya.com / staff123');
    }
}
