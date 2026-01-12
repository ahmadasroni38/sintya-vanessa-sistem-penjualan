<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('   SINTIYA ERP - Database Seeding');
        $this->command->info('========================================');
        $this->command->newLine();

        // Order is important! Follow the dependency chain
        $this->call([
            // 1. Master Data
            RolePermissionSeeder::class,    // Roles & Permissions
            UserSeeder::class,               // Users dengan Roles
            LocationSeeder::class,           // Warehouse Locations
            ChartOfAccountSeeder::class,     // Chart of Accounts (COA)
            OpeningBalanceSeeder::class,     // Saldo Awal via Journal Entry
            ProductSeeder::class,            // Products
            UnitSeeder::class,               // Units

            // 2. Transaction Data
            StockInSeeder::class,            // Stock In transactions
            StockMutationSeeder::class,      // Stock Mutation transactions
        ]);

        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('   Database Seeding Completed!');
        $this->command->info('========================================');
        $this->command->newLine();
        $this->command->info('You can now login with:');
        $this->command->info('  Email: admin@sintiya.com');
        $this->command->info('  Password: admin123');
        $this->command->newLine();
    }
}
