<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganCategory as Model;
use App\Enums\OrganCategory;

class OrganCategorySeeder extends Seeder
{

    public function run(): void
    {
        // vložíme záznam pro všechny členy enumerace
        $data = array_map(
            fn($category) => ['id' => $category->value],
            OrganCategory::cases()
        );
        
        Model::insert($data);
    }
}
