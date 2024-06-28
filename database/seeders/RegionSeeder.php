<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::insert([
            ['name' => 'Hlavní město Praha'],
            ['name' => 'Středočeský kraj'],
            ['name' => 'Jihočeský kraj'],
            ['name' => 'Plzeňský kraj'],
            ['name' => 'Karlovarský kraj'],
            ['name' => 'Ústecký kraj'],
            ['name' => 'Liberecký kraj'],
            ['name' => 'Královéhradecký kraj'],
            ['name' => 'Pardubický kraj'],
            ['name' => 'Kraj Vysočina'],
            ['name' => 'Jihomoravský kraj'],
            ['name' => 'Olomoucký kraj'],
            ['name' => 'Moravskoslezský kraj'],
            ['name' => 'Zlínský kraj'],
        ]);
        //
    }
}
