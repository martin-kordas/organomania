<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Database\Seeders\DatabaseSeeder;
use App\Helpers;
use App\Enums\Region;
use App\Enums\OrganBuilderCategory;
use App\Enums\OrganCategory;
use App\Models\Festival;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;
use App\Services\RuntimeStatsService;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-data {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports full organs and organ builders data';

    const FIELD_DELIMITER = '|';
    
    private $organs = [];
    private $organBuilders = [];
    
    // na varhanáře již importované v OrganBuilderCategory odkazují importované varhany, proto potřebujeme mapování ID
    const ORGAN_BUILDERS_ORIGINAL_ID_2_BASE_ID = [
        12 => 5,
        13 => 1,
        14 => 2,
        22 => 4,
        26 => 8,
        37 => 6,
        31 => 7,
        32 => 3,
    ];
    
    const ORGAN_ORIGINAL_ID_FESTIVAL_BASE_ID = [
        64 => 8,
        90 => 5,
        56 => 3,
        27 => [9, 39],
        54 => 10,
        91 => 11,
        66 => 12,
        45 => 13,
        59 => 15,
        55 => 17,
        103 => 18,
        41 => 19,
        76 => [22, 45],
        50 => 25,
        40 => 33,
        12 => 35,
        37 => 42,
        63 => 43,
        35 => 44,
        129 => 47,
    ];
    
    /**
     * Execute the console command.
     */
    public function handle(DatabaseSeeder $seeder, RuntimeStatsService $runtimeStats)
    {
        if ($this->option('seed')) $seeder->run();
        foreach (static::ORGAN_BUILDERS_ORIGINAL_ID_2_BASE_ID as $originalId => $baseId) {
            $this->organBuilders[$originalId] = new class(['id' => $baseId]) extends Model {
                protected $guarded = [];
            };
        }
        
        $this->importOrganBuilders();
        $this->importOrgans();
        
        $runtimeStats->forget();
        
    }
    
    private function importOrganBuilders()
    {
        $lines = $this->loadCsv('data/organBuilders.csv');
        
        foreach ($lines as $i => [
            $originalId, $baseData,
            $name, $isWorkshop, $activePeriod, $activeFromYear, $placeOfBirth, $placeOfDeath, $municipality,
            $region,, $latitude, $longitude, $importance,,
            $representativeOrgans, $representativeOrganIds,
            $builtTo1799, $builtFrom1800To1944, $builtFrom1945To1989, $builtFrom1990,
            $baroque, $romantic, $neobaroqueUniversal, $factoryProduction,
            $literature, $web, $varhanyNetId,, $workshopMembers,
            $description, 
        ]) {
            if ($i === 0) continue;     // záhlaví
            if ($baseData) continue;    // importuje OrganBuilderSeeder
            
            $workshopName = $firstName = $lastName = null;
            if ($isWorkshop) $workshopName = $name;
            else {
                $nameArr = explode(', ', $name);
                $firstName = $nameArr[1] ?? null;
                $lastName = $nameArr[0];
            }
            $workshopMembers = str_replace('; ', "\n", $workshopMembers);
            
            // převod škály z 1-6 na 1-10 a obrácení škály (nejvyšší je nejdůležitější)
            $importance = 9 - round(($importance - 1) * 1.8) + 1;
            $regionId = $region !== '' ? $this->getRegionId($region) : null;
            $literature = $this->getLiterature($literature);
            
            $organBuilder = new OrganBuilder([
                'is_workshop' => $isWorkshop ? '1' : '0',
                'workshop_name' => $workshopName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'place_of_birth' => $this->getPlaceOfBirthDeath($placeOfBirth),
                'place_of_death' => $this->getPlaceOfBirthDeath($placeOfDeath),
                'active_period' => $this->getNullableValue($activePeriod),
                'active_from_year' => $activeFromYear,
                'municipality' => $municipality,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'region_id' => $regionId,
                'importance' => $importance,
                'web' => $this->getNullableValue($web),
                'workshop_members' => $this->getNullableValue($workshopMembers),
                'perex' => null,
                'description' => $this->getNullableValue($description),
                'literature' => $this->getNullableValue($literature),
            ]);
            $organBuilder->save();
            $this->organBuilders[$originalId] = $organBuilder;
            
            $categories = [];
            if ($builtTo1799) $categories[] = OrganBuilderCategory::BuiltTo1799;
            if ($builtFrom1800To1944) $categories[] = OrganBuilderCategory::BuiltFrom1800To1944;
            if ($builtFrom1945To1989) $categories[] = OrganBuilderCategory::BuiltFrom1945To1989;
            if ($builtFrom1990) $categories[] = OrganBuilderCategory::BuiltFrom1990;
            if ($baroque) $categories[] = OrganBuilderCategory::Baroque;
            if ($romantic) $categories[] = OrganBuilderCategory::Romantic;
            if ($neobaroqueUniversal) $categories[] = OrganBuilderCategory::NeobaroqueUniversal;
            if ($factoryProduction) $categories[] = OrganBuilderCategory::FactoryProduction;
            
            if (!empty($categories)) {
                $categoryIds = array_map(
                    fn($category) => $category->value,
                    $categories
                );
                $organBuilder->organBuilderCategories()->attach($categoryIds);
            }
        }
    }
    
    private function importOrgans()
    {
        $lines = $this->loadCsv('data/organs.csv');
        
        foreach ($lines as $i => [
            $originalId, $baseData,
            $municipality, $place, $region,,
            $organBuilder,, $organBuilderIds, $organBuilderBaseId,
            $latitude, $longitude,, $yearBuilt,
            $manuals_count, $stops_count, $type,,,,,,,,
            $importance,,
            $builtTo1799, $builtFrom1800To1944, $builtFrom1945To1989, $builtFrom1990,
            $renaissance, $baroque, $romantic, $neobaroqueUniversal, $oldest, $biggest, $organBox,, $concertHall,, $importanceReason,
            $literature,, $web, ,, $description,,,,, $imageUrl, $imageCredits, $outsideImageUrl, $outsideImageCredits
        ]) {
            if ($i <= 1) continue;      // záhlaví
            if ($baseData) continue;    // importuje OrganSeeder
            
            // obrácení škály (nejvyšší je nejdůležitější)
            $importance = 10 - $importance;
            $regionId = $region !== '' ? $this->getRegionId($region) : null;
            $literature = $this->getLiterature($literature);
            
            if ($organBuilderBaseId !== '') {
                $organBuilderIds = [$organBuilderBaseId];
                $organBuilderId1 = $organBuilderBaseId;
            }
            else {
                $organBuilderIds = explode(';', $organBuilderIds);
                $organBuilderId1 = $this->findOrganBuilder($organBuilderIds[0])?->id;
            }
            $yearBuilt = explode(';', $yearBuilt);
            $yearBuilt1 = null;
            if (isset($yearBuilt[0])) {
                $matches = [];
                // ošetření 'po 1770' aj.
                if (preg_match('/[0-9]+/', $yearBuilt[0], $matches)) {
                    $yearBuilt1 = $matches[0];
                }    
            }
            
            // ve údaji může být více webů oddělených středníkem
            $web = str_replace('; ', "\n", $web);
            
            $descriptionParts = [];
            if ($importanceReason !== '') $descriptionParts[] = $importanceReason;
            if ($organBox !== '') $descriptionParts[] = $organBox;
            if ($description !== '') $descriptionParts[] = $description;
            $description = implode(' ', $descriptionParts);
            
            $organ = new Organ([
                'place' => $place,
                'municipality' => $municipality,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'region_id' => $regionId,
                'importance' => $importance,
                'organ_builder_id' => $organBuilderId1,
                'year_built' => $yearBuilt1,
                'stops_count' => $this->getNullableValue($stops_count),
                'manuals_count' => $this->getNullableValue($manuals_count),
                'concert_hall' => $concertHall ? 1 : 0,
                'image_url' => $this->getNullableValue($imageUrl),
                'image_credits' => $this->getNullableValue($imageCredits),
                'outside_image_url' => $this->getNullableValue($outsideImageUrl),
                'outside_image_credits' => $this->getNullableValue($outsideImageCredits),
                'web' => $this->getNullableValue($web),
                'perex' => null,
                'description' => $this->getNullableValue($description),
                'literature' => $this->getNullableValue($literature),
                'disposition' => $this->getDisposition($originalId),
            ]);
            $organ->save();
            $this->organs[$organ->id] = $organ;
            
            $categories = [];
            if ($builtTo1799) $categories[] = OrganCategory::BuiltTo1799;
            if ($builtFrom1800To1944) $categories[] = OrganCategory::BuiltFrom1800To1944;
            if ($builtFrom1945To1989) $categories[] = OrganCategory::BuiltFrom1945To1989;
            if ($builtFrom1990) $categories[] = OrganCategory::BuiltFrom1990;
            if ($renaissance) $categories[] = OrganCategory::Renaissance;
            if ($baroque) $categories[] = OrganCategory::Baroque;
            if ($romantic) $categories[] = OrganCategory::Romantic;
            if ($neobaroqueUniversal) $categories[] = OrganCategory::NeobaroqueUniversal;
            if ($oldest) $categories[] = OrganCategory::Oldest;
            if ($biggest) $categories[] = OrganCategory::Biggest;
            
            if ($type !== '') {
                $categories = [
                    ...$categories,
                    ...$this->getTechnicalCategoriesFromType($type),
                ];
            }
            
            if (!empty($categories)) {
                $categoryIds = array_map(
                    fn($category) => $category->value,
                    $categories
                );
                $organ->organCategories()->attach($categoryIds);
            }
            
            $rebuilds = [];
            for ($i = 1; $i < count($organBuilderIds); $i++) {
                $organBuilder = $this->findOrganBuilder($organBuilderIds[$i]);
                if ($organBuilder && isset($yearBuilt[$i])) {
                    $rebuilds[] = new OrganRebuild([
                        'organ_builder_id' =>  $organBuilder->id,
                        'year_built' => $yearBuilt[$i],
                    ]);
                }
            }
            
            if (!empty($rebuilds)) $organ->organRebuilds()->saveMany($rebuilds);
            
            // HACK: festivaly vložil již dříve FestivalSeeder, organ_id nutné doplnit nyní
            $festivalBaseId = static::ORGAN_ORIGINAL_ID_FESTIVAL_BASE_ID[$originalId] ?? null;
            if ($festivalBaseId !== null) {
                foreach (Arr::wrap($festivalBaseId) as $festivalBaseId1) {
                    Festival::find($festivalBaseId1)->fill(['organ_id' => $organ->id])->save();
                }
            }
        }
    }
    
    private function getDisposition($originalId)
    {
        $disposition = null;
        $file = base_path("data/dispositions/$originalId.md");
        if (file_exists($file)) $disposition = file_get_contents($file);
        return $disposition;
    }
    
    private function getTechnicalCategoriesFromType($type)
    {
        $categories = match ($type) {
            'e' => [OrganCategory::ActionElectrical],
            'ek' => [OrganCategory::ActionElectrical, OrganCategory::WindchestKegel],
            'ep', 'p/e' => [OrganCategory::ActionElectrical, OrganCategory::ActionPneumatical],
            'epk' => [OrganCategory::ActionElectrical, OrganCategory::ActionPneumatical, OrganCategory::WindchestKegel],
            'm' => [OrganCategory::ActionMechanical],
            'm-Bkk', 'm-Bkk-mBk' => [OrganCategory::ActionBarker],
            'mez', 'mz; rejstříková traktura elektrická' => [OrganCategory::ActionMechanical, OrganCategory::ActionElectrical, OrganCategory::WindchestSchleif],
            'mk' => [OrganCategory::ActionMechanical, OrganCategory::WindchestKegel],
            'mk-p' => [OrganCategory::ActionMechanical, OrganCategory::WindchestKegel, OrganCategory::ActionPneumatical],
            'mz', 'mz?' => [OrganCategory::ActionMechanical, OrganCategory::WindchestSchleif],
            'p', 'p?', 'pp', 'pv' => [OrganCategory::ActionPneumatical],
            'pk' => [OrganCategory::ActionPneumatical, OrganCategory::WindchestKegel],
            default => [],
        };
        if (empty($categories)) $this->line("Type not recognized: $type");
        return $categories;
    }
    
    private function findOrganBuilder($originalId)
    {
        return $this->organBuilders[$originalId] ?? null;
    }
    
    private function getNullableValue($value)
    {
        return $value === '' ? null : $value;
    }
    
    private function getPlaceOfBirthDeath($place)
    {
        return ($place !== '' && $place !== '?') ? $place : null;
    }
    
    private function getRegionId($region)
    {
        if ($region === 'Hlavní město Praha') $regionLabel = 'Praha';
        else $regionLabel = Helpers::stripAccents($region);
        return (Region::{$regionLabel})->value;
    }
    
    private function getLiterature($literature)
    {
        $literatureArr = [];
        foreach (explode(';', $literature) as $literature1) {
            $literatureArr[] = trim($literature1);
        }
        return implode("\n", $literatureArr);
    }
    
    private function loadCsv($file)
    {
        $file = base_path($file);
        $contents = file($file);

        foreach ($contents as $line) {
            $data = str_getcsv($line, static::FIELD_DELIMITER, '"');
            yield $data;
        }
    }
}
