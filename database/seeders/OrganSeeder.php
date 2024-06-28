<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organ;
use App\Enums\Region;
use App\Enums\OrganCategory;

class OrganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertOrgan(
            data: [
                'place' => 'kostel sv. Mořice',
                'municipality' => 'Olomouc',
                'latitude' => 49.5951461,
                'longitude' => 17.2512853,
                'region_id' => Region::Olomoucky,
                'importance' => 10,
                'organ_builder_id' => 2,
                'year_built' => 1968,
                'stops_count' => 94,
                'manuals_count' => 5,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/52/Kostel_svat%C3%A9ho_Mo%C5%99ice_v_Olomouci.jpg',
                'perex' => 'Vzácné barokní varhany, označované jako "královna moravských varhan", jsou po rozšíření v 60. letech 20. století považovány za největší nástroj na území ČR co do počtu píšťal.',
                'description' => 'Nástroj patří mezi největší a nejvýznamnější u nás postavené barokní varhany. Po opravě a rozšíření v 60. letech 20. století jsou varhany považovány za největší co do počtu píšťal v ČR. Varhany hostí renomovaný varhanní festival s dlouholetou tradicí.',
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::ActionElectrical,
                OrganCategory::WindchestSchleif,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'kostel Očišťování Panny Marie',
                'municipality' => 'Dub nad Moravou',
                'latitude' => 49.4834803,
                'longitude' => 17.2749228,
                'region_id' => Region::Olomoucky,
                'importance' => 10,
                'organ_builder_id' => 3,
                'year_built' => 1768,
                'stops_count' => 29,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Kostel_O%C4%8Di%C5%A1%C5%A5ov%C3%A1n%C3%AD_Panny_Marie%2C_Dub_nad_Moravou%2C_okres_Olomouc.jpg/640px-Kostel_O%C4%8Di%C5%A1%C5%A5ov%C3%A1n%C3%AD_Panny_Marie%2C_Dub_nad_Moravou%2C_okres_Olomouc.jpg',
                'perex' => 'Velké a zachovalé barokní varhany.',
                'description' => 'Varhany svojí monumentalitou, vyjádřenou též velkou a bohatě zdobenou skříní, odpovídají významu známého poutního místa. Dispozice byla upravena jen částečně na konci 19. století Františkem Čápkem. Ačkoli jsou varhany v současné době hratelné, bylo by žádoucí jejich restaurování.',
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'kostel sv. Mikuláše',
                'municipality' => 'Ludgeřovice',
                'latitude' => 49.8910011,
                'longitude' => 18.2389378,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 6,
                'organ_builder_id' => 1,
                'year_built' => 1931,
                'stops_count' => 45,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Kostel_svat%C3%A9ho_Mikul%C3%A1%C5%A1e_a_fara.JPG/620px-Kostel_svat%C3%A9ho_Mikul%C3%A1%C5%A1e_a_fara.JPG',
                'perex' => 'Zdařilý nástroj z meziválečné produkce firmy Rieger pravidelně hostí varhanní koncerty.',
                'description' => 'Konzervativní neogotická varhanní skříň zapadá dobře do interiéru kostela. Varhany již částečně vycházejí ze zásad tzv. varhanního hnutí, usilujícího o návrat k baroknímu zvukovému ideálu, spočívajícímu mj. v lesklém zvuku rejstříků vyšších poloh.',
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Romantic,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::Biggest,
                OrganCategory::ActionPneumatical,
                OrganCategory::WindchestKegel,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'kostel sv. Mořice',
                'municipality' => 'Kroměříž',
                'latitude' => 49.2991767,
                'longitude' => 17.3910325,
                'region_id' => Region::Zlinsky,
                'importance' => 5,
                'organ_builder_id' => 5,
                'year_built' => 1905,
                'stops_count' => 46,
                'manuals_count' => 3,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Kostel_sv._Mo%C5%99ice_v_Krom%C4%9B%C5%99%C3%AD%C5%BEi.JPG/497px-Kostel_sv._Mo%C5%99ice_v_Krom%C4%9B%C5%99%C3%AD%C5%BEi.JPG',
                'perex' => 'Jediný velký nástroj varhanáře Emanuela Štěpána Petra na Moravě.',
                'description' => 'Velké a dobře zachované Petrovy varhany, jediný velký Petrův nástroj na Moravě.',
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Romantic,
                OrganCategory::Biggest,
                OrganCategory::ActionPneumatical,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'kostel sv. Jakuba',
                'municipality' => 'Brno',
                'latitude' => 49.1966367,
                'longitude' => 16.6084586,
                'region_id' => Region::Jihomoravsky,
                'importance' => 7,
                'organ_builder_id' => 6,
                'year_built' => 1862,
                'stops_count' => 31,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Church_of_Saint_James_in_Brno.jpg/465px-Church_of_Saint_James_in_Brno.jpg',
                'description' => 'Z barokních dvoumanuálových varhan Jakuba Ryšáka z r. 1691 se zachovala pouze významná varhanní skříň. Nástroj Františka Svítila je zajímavým a ojedinělým dokladem varhanářství v 1. pol. 19. stol.',
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Baroque,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'kostel Nanebevzetí Panny Marie',
                'municipality' => 'Polná',
                'latitude' => 49.4876303,
                'longitude' => 15.7183628,
                'region_id' => Region::Vysocina,
                'importance' => 10,
                'organ_builder_id' => 4,
                'year_built' => 1708,
                'stops_count' => 31,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Kostel_NPM_Poln%C3%A1_1.JPG/640px-Kostel_NPM_Poln%C3%A1_1.JPG',
                'description' => 'Velké a jedinečně zachovalé Sieberovy varhany.',
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'konkatedrála Nanebevzetí Panny Marie',
                'municipality' => 'Opava',
                'latitude' => 49.9387481,
                'longitude' => 17.9006833,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 1,
                'organ_builder_id' => 2,
                'year_built' => 1957,
                'stops_count' => 56,
                'manuals_count' => 3,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Kostel_Nanebevzet%C3%AD_P._Marie%2C_Opava_-_panoramio.jpg/640px-Kostel_Nanebevzet%C3%AD_P._Marie%2C_Opava_-_panoramio.jpg',
                'description' => 'Velký chrámový nástroj byl několikrát přebudován. Pravidelně se zde koná Mezinárodní varhanní soutěž Petra Ebena.',
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::BuiltFrom1990,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::ActionPneumatical,
                OrganCategory::ActionElectrical,
            ]
        );
        
        $this->insertOrgan(
            data: [
                'place' => 'Dvořákova síň Rudolfina',
                'municipality' => 'Praha',
                'latitude' => 50.0899300,
                'longitude' => 14.4154419,
                'region_id' => Region::Praha,
                'importance' => 9,
                'organ_builder_id' => 2,
                'year_built' => 1974,
                'stops_count' => 63,
                'manuals_count' => 4,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Rudofinum_%C4%8Delo_1.jpg/640px-Rudofinum_%C4%8Delo_1.jpg',
                'description' => 'Ve Dvořákově síni Rudolfina se vystřídalo několik nástrojů. Současné varhany stojí ve skříni z původních varhan Wilhelma Sauera z r. 1885. Jedná se o první čtyřmanuálové varhany s mechanickou trakturou u nás (pro jednoduchost konstrukce a snadnost hry se u takto velkých varhan používala spíše traktura elektrická). Na umělecké a technické koncepci varhan se podíleli mj. Jiří Reinberger, významný český koncertní varhaník, a německý varhanář Rudolf von Beckerath.',
                'perex' => 'Reprezentativní koncertní nástroj poválečné produkce krnovské firmy Rieger-Kloss.',
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );
    }
    
    private function insertOrgan(array $data, array $categories)
    {
        $organBuilder = new Organ($data);
        $organBuilder->save();
        $organBuilder->organCategories()->attach($categories);
    }
}
