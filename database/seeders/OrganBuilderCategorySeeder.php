<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganBuilderCategory as Model;
use App\Enums\OrganBuilderCategory;

class OrganBuilderCategorySeeder extends Seeder
{

    public function run(): void
    {
        // vložíme záznam pro všechny členy enumerace
        $data = array_map(
            fn($category) => ['id' => $category->value],
            OrganBuilderCategory::cases()
        );
        
        Model::insert($data);
    }
}
