<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\Pitch;
use App\Models\Pitch as Model;

class PitchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // vložíme záznam pro všechny členy enumerace
        $data = array_map(
            fn($category) => ['id' => $category->value],
            Pitch::cases()
        );
        
        Model::insert($data);
    }
}
