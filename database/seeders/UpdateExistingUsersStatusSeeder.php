<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateExistingUsersStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all existing users to have 'active' status if they don't have one
        User::whereNull('status')->orWhere('status', '')->update(['status' => 'active']);

        $this->command->info('Updated existing users status to active');
    }
}
