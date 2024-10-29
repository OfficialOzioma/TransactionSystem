<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserBalance;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        // Generate 10 users with associated balances
        User::factory()
            ->count(10)
            ->has(UserBalance::factory()->count(1), 'balance') // Each user has one balance record
            ->create();
    }
}
