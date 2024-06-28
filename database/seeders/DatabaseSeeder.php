<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\OrganBuilderCategorySeeder;
use Database\Seeders\OrganBuilderSeeder;
use Database\Seeders\OrganSeeder;
use Database\Seeders\OrganCategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->call([
            RegionSeeder::class,
            OrganBuilderCategorySeeder::class,
            OrganBuilderSeeder::class,
            OrganCategorySeeder::class,
            OrganSeeder::class,
        ]);
    }
}
