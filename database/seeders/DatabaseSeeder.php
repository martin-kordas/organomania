<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\OrganCustomCategory;
use App\Models\Like;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganCategory;
use App\Models\OrganBuilderCategory;
use App\Models\OrganBuilderCustomCategory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    
    public $defaultOnly = false;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $factory = User::factory();
        $adminUser = $factory->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $testUser = $factory->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $this->call([
            RegionSeeder::class,
            OrganBuilderCategorySeeder::class,
            OrganBuilderSeeder::class,
            OrganCategorySeeder::class,
            OrganSeeder::class,
            FestivalSeeder::class,
            RegisterCategorySeeder::class,
            PitchSeeder::class,
            RegisterSeeder::class,
            CompetitionSeeder::class,
        ]);
        
        $this->command->call('app:import-data');
        $this->call([
            DispositionSeeder::class,
        ]);
        
        if (!$this->defaultOnly) {
            $organCustomCategories = OrganCustomCategory::factory()
                ->count(3)
                ->recycle($adminUser)
                ->create();
            $organBuilderCustomCategories = OrganBuilderCustomCategory::factory()
                ->count(3)
                ->recycle($adminUser)
                ->create();

            Like::factory()
                ->count(8)
                ->recycle([$adminUser, $testUser])
                ->create();

            OrganBuilder::factory()
                ->hasAttached($organBuilderCustomCategories)
                ->count(5)
                ->recycle($adminUser)
                ->has(
                    Organ::factory()
                        ->hasAttached($organCustomCategories)
                        ->recycle($adminUser)
                        ->count(2)
                        // user_id se musí shodovat s nadřazeným varhanářem
                        // (uživatel může k soukromým varhanám vyplnit jen svého soukromého varhanáře)
                        ->state(function (array $attributes, OrganBuilder $organBuilder) {
                            return ['user_id' => $organBuilder->user_id];
                        })
                )
                ->create();
        }
    }
    
}
