<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\OrganBuilderCategory;
use App\Enums\OrganCategory;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderTimelineItem;
use App\Models\Scopes\OwnedEntityScope;
use App\Services\RuntimeStatsService;

class ImportBaroqueData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-baroque-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports baroque organs and baroque organ builders data';

    /**
     * Execute the console command.
     */
    public function handle(RuntimeStatsService $runtimeStats)
    {   
        $this->importOrganBuilders();
        $this->importOrgans();
        
        $runtimeStats->forget();   
    }
    
    private function importOrganBuilders()
    {
        $dataOrganBuilders = $this->loadJson('data/baroque/organ-builders.json');
        
        foreach ($dataOrganBuilders as $dataOrganBuilder) {
            $activeFromYear = $data['year_of_birth'] ?? (
                $dataOrganBuilder['year_of_death'] ? ((int)$dataOrganBuilder['year_of_death'] - 50) : 9999
            );
            $activePeriod = implode(
                '–',
                [$dataOrganBuilder['year_of_birth'] ?? '', $dataOrganBuilder['year_of_death'] ?? '']
            );
            
            $organBuilder = new OrganBuilder([
                'is_workshop' => '0',
                'first_name' => $dataOrganBuilder['first_name'],
                'last_name' => $dataOrganBuilder['last_name'],
                'last_name' => $dataOrganBuilder['last_name'],
                'municipality' => $dataOrganBuilder['municipality'] ?? '?',
                'place_of_birth' => $dataOrganBuilder['place_of_birth'],
                'place_of_death' => $dataOrganBuilder['place_of_death'],
                'active_period' => $activePeriod,
                'active_from_year' => $activeFromYear,
                'region_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'importance' => 1,
                'literature' => 'Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (1. díl)',
                'baroque' => 1,
            ]);
            $organBuilder->save();
            
            $categories = [OrganBuilderCategory::Baroque, OrganBuilderCategory::FromBookBaroqueOrganBuilding];
            
            if ($dataOrganBuilder['year_of_birth'] || $dataOrganBuilder['year_of_death']) {
                $yearFrom = $dataOrganBuilder['year_of_birth'] ? (int)$dataOrganBuilder['year_of_birth'] : null;
                $yearTo = $dataOrganBuilder['year_of_death'] ? (int)$dataOrganBuilder['year_of_death'] : null;
                $yearFrom ??= $yearTo;
                $categories = [
                    ...$categories,
                    ...OrganBuilderCategory::getPeriodCategories((int)$yearFrom, $yearTo)->toArray(),
                ];
            }
            
            if (!empty($categories)) {
                $categoryIds = array_map(
                    fn($category) => $category->value,
                    $categories
                );
                $organBuilder->organBuilderCategories()->attach($categoryIds);
            }
            
            $timelineItem = new OrganBuilderTimelineItem;
            $timelineItem->loadFromOrganBuilder($organBuilder);
            $timelineItem->save();
        }
    }
    
    private function importOrgans()
    {
        $dataOrgans = $this->loadJson('data/baroque/organs.json');
        
        foreach ($dataOrgans as $dataOrgan) {
            $organ = new Organ([
                // ve vstupních datech omlyme prohozeno municipality a place
                'municipality' => $dataOrgan['place'],
                'place' => $dataOrgan['municipality'] ?? '?',
                'year_built' => $dataOrgan['year_built'],
                'manuals_count' => $dataOrgan['manuals_count'],
                'stops_count' => $dataOrgan['stops_count'],
                'disposition' => $dataOrgan['disposition'],
                'region_id' => 1,
                'latitude' => 0,
                'longitude' => 0,
                'importance' => 1,
                'literature' => 'Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (2. díl)',
                'baroque' => 1,
            ]);
            
            $organBuilder = OrganBuilder::withoutGlobalScope(OwnedEntityScope::class)->whereRaw(
                'CONCAT(first_name, " ", last_name) = ?', [$dataOrgan["organ_builder"]]
            )->where('baroque', 1)->first();
            $organ->organ_builder_id = $organBuilder?->id;
            
            $organ->save();
            
            // TODO: přidat i kategorie mz?
            $categories = [OrganCategory::Baroque, OrganCategory::FromBookBaroqueOrganBuilding, OrganCategory::ActionMechanical, OrganCategory::WindchestSchleif];
            if ($dataOrgan['year_built']) {
                $categories = [
                    ...$categories,
                    ...OrganCategory::getPeriodCategories((int)$dataOrgan['year_built'])->toArray(),
                ];
            }
            if (!empty($categories)) {
                $categoryIds = array_map(
                    fn($category) => $category->value,
                    $categories
                );
                $organ->organCategories()->attach($categoryIds);
            }
        }
    }
    
    private function loadJson(string $file)
    {
        $file = base_path($file);
        $contents = file_get_contents($file);
        $array = json_decode($contents, true);
        if ($array === null) throw new \RuntimeException;
        return $array;
    }
    
}
