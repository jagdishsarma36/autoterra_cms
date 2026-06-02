<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@autoterra.net',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Seed products and prices
        $this->call(ProductSeeder::class);
        $this->call(PriceSeeder::class);

        // Seed page content
        $this->call(PageContentSeeder::class);
    }
}
