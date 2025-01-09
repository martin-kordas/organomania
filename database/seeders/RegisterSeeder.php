<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Register;
use App\Models\RegisterName;
use App\Models\PaletteRegister;
use App\Enums\Pitch;
use App\Enums\RegisterCategory;
use App\Enums\DispositionLanguage;

class RegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertRegister(
            data: [
                'name' => 'Principál',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Základní silný a nosný hlas varhan. U větších nástrojů bývá v hlavním manuálu disponován již v poloze 16\', nejčastěji však až v poloze 8\'. U menších nástrojů se vyskytuje pouze v poloze 4\' nebo 2\'.'
            ],
            names: [
                new RegisterName(['name' => 'Principál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Prinzipal', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Principal', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Montre', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true])
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_8,
                Pitch::Pitch_16,
            ],
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna harmonická',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Flétna bohatá na alikvotní tóny. Typický rejstřík romantických varhan, vynalezený francouzským varhanářem Aristide Cavaillé-Collem (1811-1899).'
            ],
            names: [
                new RegisterName(['name' => 'Flétna harmonická', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Flûte harmonique', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true])
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Prefukujici,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Trompeta',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Výrazný jazykový rejstřík. Disponuje se v manuálu i v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Trompeta', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Trubka', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Trompete', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Tromba', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Trompette', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'frequent_pedal' => true])
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Burdon',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Frekventovaný krytý dřevěný rejstřík. Staví se nejčastěji v 16\' poloze v manuálu i pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Burdon', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Bourdon', 'language' => DispositionLanguage::French]),
                new RegisterName(['name' => 'Bourdun', 'language' => DispositionLanguage::French]),
                new RegisterName(['name' => 'Bordun', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true])
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Gamba',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Středně hlasitý rejstřík jasně smykavého zabarvení.'
            ],
            names: [
                new RegisterName(['name' => 'Gamba', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gambe', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Viola da Gamba', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Viola de Gamba', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true])
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Oktáva',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Označení pro principálový rejstřík ve vyšší poloze.'
            ],
            names: [
                new RegisterName(['name' => 'Oktáva', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Octava', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Octav', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Octave', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Oktave', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Préstant', 'language' => DispositionLanguage::French]),
                new RegisterName(['name' => 'Prestant', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna trubicová',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Specifická konstrukce píšťal rejstříku spočívá v kryté píšťale prodloužené úzkou trubičkou, díky níž je zvuk bohatší na alikvotní tóny.'
            ],
            names: [
                new RegisterName(['name' => 'Flétna trubicová', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Rohrflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Rohrflaut', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flûte à Cheminée', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true])
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Mixtura',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Zvuková koruna varhan s píšťalami principálové šířky. Na každou klávesu připadá ve smíšeném rejstříku 3 až 5 píšťal, které znějí v základních a kvintových polohách. Ve vyšších polohách rejstřík repetuje (hrají tóny nižší než odpovídající poloze píšťalové řady).'
            ],
            names: [
                new RegisterName(['name' => 'Mixtura', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Mikstura', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Mixtur', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Fourniture', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'pitch' => Pitch::Pitch_2_And_2_3, 'multiplier' => 4]),
                new PaletteRegister(['pitch' => Pitch::Pitch_1_And_1_3, 'multiplier' => 4]),
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
                Pitch::Pitch_2,
                Pitch::Pitch_1_And_1_3,
                Pitch::Pitch_1,
                Pitch::Pitch_1_2,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kryt jemný',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Kryt jemný', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Zartgedackt', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Vox coelestis',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Jemný výchvěvný rejstřík, oblíbený v romantickém varhanářství. Jeho název v překladu znamená "nebeský hlas".',
            ],
            names: [
                new RegisterName(['name' => 'Vox celestis', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Vox coelestis', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Voix céleste', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Vychvevne
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna koncertní',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna koncertní', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Concertflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Prefukujici
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principál italský',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principál s o něco širšími píšťalami a kulatějším zvukem.'
            ],
            names: [
                new RegisterName(['name' => 'Principál italský', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Roh noční',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Rejstřík s velmi širokými píšťalami a jemným zvukem.'
            ],
            names: [
                new RegisterName(['name' => 'Roh noční', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Nachthorn', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cor de Nuit', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Cimbál',
                'pitch' => Pitch::Pitch_1,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Zvuková koruna ve velmi vysoké poloze, disponuje se obvykle na jiném než hlavním manuálu.'
            ],
            names: [
                new RegisterName(['name' => 'Cimbál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cimbal', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cymbál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cembalo', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Zymbel', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cimbel', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cymballe', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'multiplier' => 4]),
            ],
            pitches: [
                Pitch::Pitch_2,
                Pitch::Pitch_1_And_1_3,
                Pitch::Pitch_1,
                Pitch::Pitch_2_3,
                Pitch::Pitch_1_2,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Hoboj',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Velmi často disponovaný jazykový rejstřík. Jemný a nosový zvuk.'
            ],
            names: [
                new RegisterName(['name' => 'Hoboj', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Oboe', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Hautbois', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
                RegisterCategory::Trychtyrovite,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Oktávbas',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Název principálového rejstříku v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Oktávbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Oktávbass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Octavbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Octav Bass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Oktavenbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Octavenbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Cello',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Smykavý rejstřík v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Cello', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cello', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Violoncello', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Violoncello', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Subbas',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Krytý rejstřík v pedálu, disponovaný téměř na všech varhanách s pedálem.'
            ],
            names: [
                new RegisterName(['name' => 'Subbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Subbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Soubasse', 'language' => DispositionLanguage::French]),
                new RegisterName(['name' => 'Sub Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Bourdonbas',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Tichý krytý rejstřík v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Bourdonbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Bourdonbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kryt',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Rejstřík jemného zvuku. Staví se v různých polohách s dřevěnými i kovovými píšťalami.'
            ],
            names: [
                new RegisterName(['name' => 'Kryt', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gedackt', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Roh kamzičí',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Rejstřík zvukově na pomezí flétny a smyku.'
            ],
            names: [
                new RegisterName(['name' => 'Roh kamzičí', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gemshorn', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvinta',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principálový rejstřík v kvintové poloze. Není-li disponován samostatně, může být součástí mixtury.'
            ],
            names: [
                new RegisterName(['name' => 'Kvinta', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quinta', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quinte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quinte', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'pitch' => Pitch::Pitch_2_And_2_3]),
                new PaletteRegister(['frequent_manual' => true, 'pitch' => Pitch::Pitch_1_And_1_3]),
            ],
            pitches: [
                Pitch::Pitch_5_And_1_3,
                Pitch::Pitch_2_And_2_3,
                Pitch::Pitch_1_And_1_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Seskvialtera',
                'pitch' => Pitch::Pitch_2_3,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Smíšený rejstřík s píšťalami flétnové šířky. Skládá se z píšťalových řad kvinty (2 2/3\') a tercie (1 3/5\').'
            ],
            names: [
                new RegisterName(['name' => 'Seskvialtera', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Sesquialtera', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'multiplier' => 2]),
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Salicionál',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Tichý smykavý rejstřík, disponovaný ve varhanách všech stylových období. Název pochází z latinského slova salix (vrba).'
            ],
            names: [
                new RegisterName(['name' => 'Salicionál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Salicional', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Salizional', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Salicet', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Nasard',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Méně výrazný kvintový rejstřík, stavěný obvykle jako kryt. Zvuk má nosový charakter.'
            ],
            names: [
                new RegisterName(['name' => 'Nasard', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Nasat', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Nassat', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Nasard', 'language' => DispositionLanguage::French]),
                new RegisterName(['name' => 'Nazard', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
                Pitch::Pitch_1_And_1_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna lesní',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Často disponovaný flétnový rejstřík měkkého zvuku.'
            ],
            names: [
                new RegisterName(['name' => 'Flétna lesní', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Waldflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        // TODO: může to být i principál?
        $this->insertRegister(
            data: [
                'name' => 'Tercie',
                'pitch' => Pitch::Pitch_1_And_3_5,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Rejstřík v terciové poloze, typický pro francouzské varhanářství a varhanní literaturu.'
            ],
            names: [
                new RegisterName(['name' => 'Tercie', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Terz', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Tertia', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Tierce', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_3_And_1_5,
                Pitch::Pitch_1_And_3_5,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principálbas',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Obvykle nejhlubší z principálových rejstříků v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Principálbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Principálbass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Prinzipalbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Principal Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna krytá',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna krytá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gedacktflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvintbas',
                'pitch' => Pitch::Pitch_5_And_1_3,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principálový rejstřík v kvintové poloze v pedálu.',
            ],
            names: [
                new RegisterName(['name' => 'Kvintbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kvintbass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quintbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quinta Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_5_And_1_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Superoktáva',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Označení principálového rejstříku o oktávu vyššího než Oktáva.',
            ],
            names: [
                new RegisterName(['name' => 'Superoktáva', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Superoctava', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Superoctav', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Superoktave', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Super Octava', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Doublette', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Pozoun',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Nejsilnější jazykový rejstřík, disponuje se v pedálu, nejčastěji v poloze 16\'.',
            ],
            names: [
                new RegisterName(['name' => 'Pozoun', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Posaune', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Posaunbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Posaunenbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Posona', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Trombone', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Bombarde', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
                RegisterCategory::Trychtyrovite,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Fugara',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Rejstřík s výrazně smykavým zvukem. Jeho název pochází od slovenského lidového nástroje fujara.',
            ],
            names: [
                new RegisterName(['name' => 'Fugara', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Dolce',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Rejstřík jemného zvuku s mírným smykem.',
            ],
            names: [
                new RegisterName(['name' => 'Dolce', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flute dolce',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flute dulce', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kornet',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Smíšený rejstřík s píšťalami flétnové šířky, pocházející z francouzského varhanářství. Typicky obsahuje 5 píšťalových řad v polohách 8\', 4\', 2 2/3\', 2\' a 1 3/5\'. Poloha 1 3/5\' působí ve zvuku charakteristické terciové zabarvení, polohy 8\' a 4\' mohou být vynechány. Rejstřík se nejčastěji využívá v sopránové poloze, proto bývá vystavěn až od tónu c1. Ve francouzské varhanní literatuře je typickou součástí ustáleného typu registrace nazývaného grand jeu.',
            ],
            names: [
                new RegisterName(['name' => 'Kornet', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kornett', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cornett', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cornet', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'multiplier' => 5]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna špičatá',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Často disponovaný druh flétny s kónickým, tj. zužujícím se tělem píšťaly.',
            ],
            names: [
                new RegisterName(['name' => 'Flétna špičatá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Spitzflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Spitzflaut', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvintadena',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Krytý rejstřík, jehož zvuk je charakteristicky zabarven jemně přiznívající kvintou.',
            ],
            names: [
                new RegisterName(['name' => 'Kvintadena', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kvintadéna', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quintadena', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quintatön', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quintatöna', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quintatöne', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quintadene', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Basson-Oboe',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Název rejstříku odkazuje na symfonický orchestr, kde hru v nižších polohách obstarává fagot, zatímco hru ve vyšších polohách hoboj. Tónový rozsah varhan zahrnuje obě polohy, proto i rejstřík nese názvy obou nástrojů.',
            ],
            names: [
                new RegisterName(['name' => 'Fagot-Hoboj', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Basson-Oboe', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Basson-Hautbois', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kryt líbezný',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jemný kryt s menší šířkou píšťal.',
            ],
            names: [
                new RegisterName(['name' => 'Kryt líbezný', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Lieblich Gedeckt', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principál houslový',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principálový rejstřík s menší šířkou píšťal, která dává zvuku smykavý charakter. Oblíben byl především v romantickém varhanářství, kde mohl být použit jako náhrada za klasický Principál.',
            ],
            names: [
                new RegisterName(['name' => 'Principál houslový', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Geigenprinzipal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna jemná',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna jemná', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Zartflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Violine',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Violine', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Aeolina',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Velmi tichý smykavý rejstřík, oblíbený v romantickém varhanářství. Je-li disponován, bývá nejtišším hlasem varhan.',
            ],
            names: [
                new RegisterName(['name' => 'Aeolina', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Eolina', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Aeoline', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna příčná',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna příčná', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Querflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Traversflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flauto traverse', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flaut allemand', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'pitch' => Pitch::Pitch_4]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
                RegisterCategory::Prefukujici
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flautino',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flautino', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Vox humana',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jazykový rejstřík drnčivého charakteru, napodobující zvuk lidského hlasu. Často se používá v kombinaci s tremolem.',
            ],
            names: [
                new RegisterName(['name' => 'Vox humana', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Voix Humaine', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
                RegisterCategory::Regaly
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Violonbas',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Violonbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Violonbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Violon', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna kopulová',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna kopulová', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna dutá',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Flétnový rejstřík s širokými píšťalami.',
            ],
            names: [
                new RegisterName(['name' => 'Flétna dutá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Hohlflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Akuta',
                'pitch' => Pitch::Pitch_1,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Zvuková koruna posazená ve vyšší poloze než Mixtura. Zdůrazňuje oktávové píšťalové řady oproti kvintovým.'
            ],
            names: [
                new RegisterName(['name' => 'Akuta', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Akuta', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'multiplier' => 4]),
            ],
            pitches: [
                Pitch::Pitch_1,
                Pitch::Pitch_2_3,
                Pitch::Pitch_1_2,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Šalmaj regál',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Zvuk rejstříku je na pomezí jazykového rejstříku Šalmaj a drnčivých regálů.'
            ],
            names: [
                new RegisterName(['name' => 'Šalmaj regál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Schalmei Regal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Regaly
            ]
        );
        
        // TODO: není to flétna? (viz varhany dóm Olomouc)
        $this->insertRegister(
            data: [
                'name' => 'Chorál',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Principal,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Chorál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Choral', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Flöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flauta', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flûte', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvinta šustivá',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Rejstřík složený z píšťal základního tónu a kvinty, obvykle v polohách 2\' a 1 1/3\'',
            ],
            names: [
                new RegisterName(['name' => 'Kvinta šustivá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kvinta šumivá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Rauschquinte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Rauschquinta', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true, 'multiplier' => 2]),
            ],
            pitches: [
                Pitch::Pitch_2,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Copula major',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Krytý rejstřík měkkého zvuku v poloze 8\'. Spolu s Copulou minor 4\' tvoří typickou dvojici rejstříků na českých barokních varhanách. Pojmenování "major" odkazuje na výšku píšťaly, která je oproti Copule minor dvojnásobná (a tón tedy zní o oktávu níže).',
            ],
            names: [
                new RegisterName(['name' => 'Copula major', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kopula major', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Copl major', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Copula minor',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Krytý rejstřík měkkého zvuku v poloze 4\'. Spolu s Copulou major 8\' tvoří typickou dvojici rejstříků na českých barokních varhanách. Pojmenování "minor" odkazuje na výšku píšťaly, která je oproti Copule major poloviční (a tón tedy zní o oktávu výše).',
            ],
            names: [
                new RegisterName(['name' => 'Copula minor', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kopula minor', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Copl minor', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Cornetbass',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Název rejstříku Kornet v pedálu.',
            ],
            names: [
                new RegisterName(['name' => 'Kornetbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Cornetbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cornet Bass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Kornett Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'multiplier' => 5, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flauta major',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Rejstřík v poloze 8\'. Obvykle tvoří dvojici s Flautou minor 4\', která má o polovinu kratší píšťaly a o oktávu vyšší tón.',
            ],
            names: [
                new RegisterName(['name' => 'Flauta major', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flaut major', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flauta minor',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Rejstřík v poloze 4\'. Obvykle tvoří dvojici s Flautou major 8\', která má dvakrát delší píšťaly a o oktávu nižší tón.',
            ],
            names: [
                new RegisterName(['name' => 'Flauta minor', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flaut minor', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvinta flétnová',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Označení pro Kvintu, která má flétnové (ne obvyklé principálové) píšťaly.',
            ],
            names: [
                new RegisterName(['name' => 'Kvinta flétnová', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Quintflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quintflauta', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna portunálová',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Flétna charakteristická měkkým zvukem.',
            ],
            names: [
                new RegisterName(['name' => 'Flétna portunálová', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Portunál', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Portunalflöte', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Portunal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Portunálbas',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Název Flétny portunálové v pedálu.',
            ],
            names: [
                new RegisterName(['name' => 'Portunálbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Portunalbass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Portunal Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        // TODO: je určitě krytý? (Týnská chrám)
        $this->insertRegister(
            data: [
                'name' => 'Bourdonflaut',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Bourdonflaut', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Bourdon Flauta', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flauta dulcis',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flauta dulcis', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Quinta major',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Kvintový rejstřík v poloze 2 2/3\'. Obvykle tvoří dvojici s Quintou minor 1 1/3\', která má kratší píšťaly a o oktávu vyšší tón.'
            ],
            names: [
                new RegisterName(['name' => 'Quinta major', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Quinta minor',
                'pitch' => Pitch::Pitch_1_And_1_3,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Kvintový rejstřík v poloze 1 1/3\'. Obvykle tvoří dvojici s Quintou major 2 2/3\', která má delší píšťaly a o oktávu nižší tón.'
            ],
            names: [
                new RegisterName(['name' => 'Quinta minor', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_1_And_1_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Sedecima',
                'pitch' => Pitch::Pitch_1,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Označení principálového rejstříku ve vysoké poloze 1\'. Název vychází z latinského slova "šestnáct".',
            ],
            names: [
                new RegisterName(['name' => 'Sedecima', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Quinta decima', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Subbass offen',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null
            ],
            names: [
                new RegisterName(['name' => 'Subbass offen', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Offenerbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Subbass gedeckt',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null
            ],
            names: [
                new RegisterName(['name' => 'Subbass gedeckt', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flaut amabile',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Flétna měkkého zvuku.'
            ],
            names: [
                new RegisterName(['name' => 'Flaut amabile', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Unda maris',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Výchvěvný hlas, při hře se používá v kombinaci s některým principálem nebo flétnou.'
            ],
            names: [
                new RegisterName(['name' => 'Unda maris', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Vychvevne
            ]
        );
        
        // TODO: sv. Mořic - opravdu jde o kryt?
        $this->insertRegister(
            data: [
                'name' => 'Maiorbass',
                'pitch' => Pitch::Pitch_32,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Maiorbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_32,
            ],
            categories: [
            ]
        );
        
        
        $this->insertRegister(
            data: [
                'name' => 'Quintadenbass',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => 'Označení rejstříku Kvintadena v pedálu.',
            ],
            names: [
                new RegisterName(['name' => 'Quintadenbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kvinta kamzičí',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Označení rejstříku Roh kamzičí v kvintové poloze.'
            ],
            names: [
                new RegisterName(['name' => 'Kvinta kamzičí', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gemshornquinte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_5_And_1_3,
                Pitch::Pitch_2_And_2_3,
                Pitch::Pitch_1_And_1_3,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Trombabass',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Označení rejstříku Trumpeta v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Trombabass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Trinuna',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Ojedinělý smykavý rejstřík jemného charakteru.'
            ],
            names: [
                new RegisterName(['name' => 'Trinuna', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Bifara',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Výchvěvný hlas, složený ze dvou řad principálů. Bývá vystavěn až od tónu c1.'
            ],
            names: [
                new RegisterName(['name' => 'Bifara', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Bifero', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Pifaro', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Vychvevne
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Violeta',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Violeta', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flauto soprano',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flauto soprano', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                'pitch' => Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Superoktávbas',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Název principálového rejstříku v pedálu v poloze o oktávu vyšší než Oktávbas.'
            ],
            names: [
                new RegisterName(['name' => 'Superoktávbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Super Octav Bass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna dvojitá',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Krytý nebo otevřený rejstřík, jehož píšťaly mají netypicky 2 výřezy, odkud proudí vzduch.'
            ],
            names: [
                new RegisterName(['name' => 'Flétna dvojitá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Doppelflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Viola d\'amour',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Jemný smykavý rejstřík, oblíbený v období romantismu.'
            ],
            names: [
                new RegisterName(['name' => 'Viola d\'amour', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Viola',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Jemně smykavý rejstřík.'
            ],
            names: [
                new RegisterName(['name' => 'Viola', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Viola', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Klarinet',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jazykový rejstřík, zvukově o něco silnější než Hoboj. Typický představitel průrazných jazykových rejstříků.'
            ],
            names: [
                new RegisterName(['name' => 'Klarinet', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Clarinette', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Clarinette', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::JazykovePrurazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flageolet',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flageolet', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flageolett', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(),
            ],
            pitches: [
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kryt tichý',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Kryt tichý', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Stillgedeckt', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Burdon velký',
                'pitch' => Pitch::Pitch_32,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Burdon velký', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Bas otevřený',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Bas otevřený', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Offenbas', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna basová',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna basová', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Bassflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Fagot',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jazykový rejstřík, zvukově o něco slabší než Pozoun. Disponuje se v manuálu i v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Fagot', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Fagott', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Basson', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Trychtyrovite,
                RegisterCategory::JazykoveNarazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna dřevěná',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna dřevěná', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Holzflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        // TODO: kónické jsem určil podle názvu
        $this->insertRegister(
            data: [
                'name' => 'Oktáva špičatá',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Principal,
                'description' => null
            ],
            names: [
                new RegisterName(['name' => 'Oktáva špičatá', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Roh křivý',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Často disponovaný jazykový rejstřík. Vyznačuje se výrazným zvukem, ale je slabší než např. Trumpeta.'
            ],
            names: [
                new RegisterName(['name' => 'Roh křivý', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Krummhorn', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Cromorne', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::JazykoveNarazne,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principál dřevěný',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principálový rejstřík, jeho dřevěná konstrukce mu dodává měkčí zvuk.',
            ],
            names: [
                new RegisterName(['name' => 'Principál dřevěný', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Holzprinzipal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Drevene
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Violino coelestis',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Violino coelestis', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Piccolo',
                'pitch' => Pitch::Pitch_2,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Piccolo', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Flauto piccolo', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Dulcián',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jazykový rejstřík měkkého zvuku.',
            ],
            names: [
                new RegisterName(['name' => 'Dulcián', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Dulzian', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister()
            ],
            pitches: [
                Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Chorálbas',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Výrazný pedálový rejstřík, sloužící ke hře melodie (chorálu) v pedálu.',
            ],
            names: [
                new RegisterName(['name' => 'Chorálbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Choralbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_pedal' => true, 'pedal' => true]),
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna zobcová',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => 'Oblíbený flétnový rejstřík jasného zvuku.'
            ],
            names: [
                new RegisterName(['name' => 'Flétna zobcová', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Blockflöte', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
                new PaletteRegister(['frequent_manual' => true]),
            ],
            pitches: [
                Pitch::Pitch_4,
                Pitch::Pitch_2,
                Pitch::Pitch_1,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Gamba špičatá',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Gamba špičatá', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Spitzgamba', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Konicke
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principál rohový',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::Principal,
                'description' => null
            ],
            names: [
                new RegisterName(['name' => 'Principál rohový', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Hornprinzipal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_16,
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Harmonia aetherea',
                'pitch' => Pitch::Pitch_2_And_2_3,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Jemná mixtura, používaná v romantickém varhanářství. Šířka píšťal odpovídá principálům nebo i smykům. Na každou klávesu připadá 3 až 5 píšťal, které znějí v základních, kvintových nebo i terciových polohách.'
            ],
            names: [
                new RegisterName(['name' => 'Harmonia aetherea', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Harmonia aetherea', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_2_And_2_3,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Kontrabas',
                'pitch' => Pitch::Pitch_16,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Smykavý hluboký rejstřík v pedálu.'
            ],
            names: [
                new RegisterName(['name' => 'Kontrabas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Kontrabass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Contrabass', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Contre-basse', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_32,
                Pitch::Pitch_16,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Krytbas',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Gedackt,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Krytbas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Krytbass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Gedecktbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétnabas',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétnabas', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Flétnabass', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Flötenbass', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Principál flétnový',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Principal,
                'description' => 'Principál s o něco širšími píšťalami a flétnovějším zvukem.',
            ],
            names: [
                new RegisterName(['name' => 'Principál flétnový', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Flötenprincipal', 'language' => DispositionLanguage::German]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
                Pitch::Pitch_2,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Violina',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::String,
                'description' => 'Rejstřík charakterem odpovídající Salicionálu.',
            ],
            names: [
                new RegisterName(['name' => 'Violina', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Flétna oktávová',
                'pitch' => Pitch::Pitch_4,
                'registerCategory' => RegisterCategory::Flute,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Flétna oktávová', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_4,
            ],
            categories: [
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Šalmaj',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::Reed,
                'description' => 'Jazykový rejstřík, napodobující stejnojmenný starobylý dechový nástroj.',
            ],
            names: [
                new RegisterName(['name' => 'Šalmaj', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Schalmei', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Chalumeau', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
                Pitch::Pitch_4,
            ],
            categories: [
                RegisterCategory::Trychtyrovite,
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Progressio harmonica',
                'pitch' => Pitch::Pitch_1_And_3_5,
                'registerCategory' => RegisterCategory::Mixed,
                'description' => 'Zvláštní typ mixtury s píšťalami užší šířky, používaný v romantickém varhanářství. Ve vyšších polohách počet současně znějících píšťal stoupá, aby došlo k dynamickému zvýraznění hrané melodie.'
            ],
            names: [
                new RegisterName(['name' => 'Progressio harmonica', 'language' => DispositionLanguage::Czech]),
                new RegisterName(['name' => 'Progressio harmonica', 'language' => DispositionLanguage::German]),
                new RegisterName(['name' => 'Progression harmonique', 'language' => DispositionLanguage::French]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_1_And_3_5,
            ],
            categories: [
                RegisterCategory::Alikvotni
            ]
        );
        
        $this->insertRegister(
            data: [
                'name' => 'Chvění houslové',
                'pitch' => Pitch::Pitch_8,
                'registerCategory' => RegisterCategory::String,
                'description' => null,
            ],
            names: [
                new RegisterName(['name' => 'Chvění houslové', 'language' => DispositionLanguage::Czech]),
            ],
            paletteRegisters: [
            ],
            pitches: [
                Pitch::Pitch_8,
            ],
            categories: [
                RegisterCategory::Vychvevne
            ]
        );
    }
    
    public function insertRegister(
        array $data,
        array $names = [],
        array $paletteRegisters = [],
        array $pitches = [],
        array $categories = [],
    )
    {
        $register = new Register($data);
        $register->save();
        if (!empty($names)) {
            $register->registerNames()->saveMany($names);
        }
        if (!empty($paletteRegisters)) {
            $register->paletteRegisters()->saveMany($paletteRegisters);
        }
        if (!empty($pitches)) {
            $register->registerPitches()->attach($pitches);
        }
        if (!empty($categories)) {
            $register->registerCategories()->attach($categories);
        }
    }
}
