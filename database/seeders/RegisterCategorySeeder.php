<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\RegisterCategory;
use App\Models\RegisterCategory as Model;

class RegisterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // vložíme záznam pro všechny členy enumerace
        $data = array_map(
            fn($category) => ['id' => $category->value],
            RegisterCategory::cases()
        );
        
        Model::insert($data);
    }
}
