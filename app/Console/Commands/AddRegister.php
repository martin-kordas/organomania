<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RegisterSeeder;
use App\Models\RegisterName;
use App\Models\PaletteRegister;
use App\Enums\Pitch;
use App\Enums\RegisterCategory;
use App\Enums\DispositionLanguage;

class AddRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds register specified in source code to the database';

    /**
     * Execute the console command.
     */
    public function handle(RegisterSeeder $seeder)
    {
        $seeder->insertRegister(
            data: [
                'name' => 'Vox angelica',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Jemný výchvěvný rejstřík, oblíbený v romantickém varhanářství. Jeho název v překladu znamená "andělský hlas". Často je složen ze dvou řad píšťal v poloze 8\' a 4\' (*Vox coelestis* je oproti tomu složen ze dvou řad v poloze 8\').'
            ],
            names: [
                new RegisterName(['name' => 'Vox angelica', 'language' => DispositionLanguage::German, 'hide_language' => 1]),
            ],
            paletteRegisters: [
                //new PaletteRegister(['frequent_manual' => true, 'multiplier' => 2]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Vychvevne
            ]
        );
    }
    
}
