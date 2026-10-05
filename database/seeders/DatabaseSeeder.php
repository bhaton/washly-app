<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndUserSeeder::class,
            ServiceSeeder::class,
            PromotionSeeder::class,
            // OrderSeeder::class, // Disabled to allow clean custom order input
        ]);
    }
}
