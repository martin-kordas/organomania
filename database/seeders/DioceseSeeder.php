<?php

namespace Database\Seeders;

use App\Models\Diocese;
use Illuminate\Database\Seeder;

class DioceseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Diocese::insert([
            ['name' => 'Pražská arcidiecéze'],
            ['name' => 'Královéhradecká diecéze'],
            ['name' => 'Plzeňská diecéze'],
            ['name' => 'Litoměřická diecéze'],
            ['name' => 'Českobudějovická diecéze'],
            ['name' => 'Olomoucká arcidiecéze'],
            ['name' => 'Brněnská diecéze'],
            ['name' => 'Ostravsko-opavská diecéze'],
        ]);
        //
    }
}
