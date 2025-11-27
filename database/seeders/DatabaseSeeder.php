<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role and permission seeder first
        $this->call(RolePermissionSeeder::class);
        
        // Add additional users
        $this->call(AdditionalUsersSeeder::class);
        
        // Add materials
        $this->call(MaterialSeeder::class);
        
        // Add loans and fines
        $this->call(PrestamoMaLoanSeeder::class);
    }
}
