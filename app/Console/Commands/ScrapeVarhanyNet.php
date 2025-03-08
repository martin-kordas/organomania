<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderTimelineItem;
use App\Models\OrganRebuild;
use App\Models\Scopes\OwnedEntityScope;
use App\Models\User;
use App\Services\VarhanyNetService;

class ScrapeVarhanyNet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-varhany-net {--type=organ} {startId} {endId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape organ and organ builder data from website varhany.net';
    
    protected VarhanyNetService $service;
    
    const DELAY = 2;

    /**
     * Execute the console command.
     * 
     * poslední ID varhan: 5739 (2025-02-08)
     * poslední ID dispozice: 1164 (2025-03-08)
     * ID varhanářů: nejsou souvislá, proto chybějící varhanáře nelze snadno scrapovat
     *  - chybějící varhanáři, kteří nebyli importování s žádnými varhanami, existují (např. id 138746)
     */
    public function handle()
    {
        $logger = fn ($message) => $this->error($message);
        $this->service = new VarhanyNetService($logger);
        
        $startId = (int)$this->argument('startId');
        $endId = (int)($this->argument('endId') ?? $startId);
        if ($startId <= 0 || $endId <= 0) $this->fail('Zadaná ID jsou v nesprávném tvaru.');
        
        $type = $this->option('type');
        
        for ($id = $startId; $id <= $endId; $id++) {
            switch ($type) {
                case 'organ':
                    if ($this->getOrgan($id)) continue 2;
                    $this->createOrgan($id);
                    break;
                
                case 'organBuilder':
                    if ($this->getOrganBuilder($id)) continue 2;
                    $this->createOrganBuilder($id);
                    break;
                
                case 'disposition':
                    $this->handleDisposition($id);
                    break;
                
                default:
                    $this->fail('Neplatná volba "type".');
            }
            
            if ($id !== $endId) sleep(static::DELAY);
        }
    }
    
    // na varhany navázané varhanáře známe zatím jen přes varhanyNetOrganBuilderId
    //  - pokud takoví varhanáři chybí v databázi, scrapujeme je také (::resolveOrganBuilder())
    private function createOrgan(int $varhanyNetOrganId)
    {
        $scraped = $this->service->scrapeOrgan($varhanyNetOrganId);
        if (!$scraped) return null;
        
        if ($scraped['varhanyNetOrganBuilderId']) {
            $organBuilder = $this->resolveOrganBuilder($scraped['varhanyNetOrganBuilderId']);
        }
        else $organBuilder = null;
        
        if ($scraped['varhanyNetRenovationOrganBuilderId']) {
            $renovationOrganBuilder = $this->resolveOrganBuilder($scraped['varhanyNetRenovationOrganBuilderId']);
        }
        else $renovationOrganBuilder = null;
        
        $rebuilds = $scraped['rebuilds']->map(function ($rebuild) {
            $organBuilder = $this->resolveOrganBuilder($rebuild['varhanyNetOrganBuilderId']);
            
            return new OrganRebuild([
                'organ_builder_id' => $organBuilder->id,
                'year_built' => $rebuild['yearBuilt'],
            ]);
        });
        
        DB::transaction(function () use ($scraped, $organBuilder, $renovationOrganBuilder, $rebuilds) {
            if ($organBuilder) $scraped['organ']->organBuilder()->associate($organBuilder);
            if ($renovationOrganBuilder) $scraped['organ']->renovationOrganBuilder()->associate($renovationOrganBuilder);
            $scraped['organ']->save();
            
            $scraped['organ']->organRebuilds()->saveMany($rebuilds);
            
            $categoryIds = $scraped['organCategories']->pluck('value');
            $scraped['organ']->organCategories()->sync($categoryIds);
        });
        
        $this->info("Úspěšně zpracovány varhany $varhanyNetOrganId (organId: {$scraped['organ']['id']}).");
        return $scraped['organ'];
    }
    
    private function createOrganBuilder(int $varhanyNetOrganBuilderId)
    {
        $scraped = $this->service->scrapeOrganBuilder($varhanyNetOrganBuilderId);
        
        DB::transaction(function () use ($scraped) {
            $scraped['organBuilder']->save();
            
            $categoryIds = $scraped['organBuilderCategories']->pluck('value');
            $scraped['organBuilder']->organBuilderCategories()->sync($categoryIds);
            
            if ($scraped['organBuilder']->active_from_year !== 9999) {
                $timelineItem = new OrganBuilderTimelineItem;
                $timelineItem->loadFromOrganBuilder($scraped['organBuilder']);
                $timelineItem->save();
            }
        });
        
        $this->info("Úspěšně zpracován varhanář $varhanyNetOrganBuilderId (organBuilderId: {$scraped['organBuilder']['id']}).");
        return $scraped['organBuilder'];
    }
    
    private function getOrganBuilder(int $varhanyNetOrganBuilderId)
    {
        return OrganBuilder::withoutGlobalScope(OwnedEntityScope::class)
            ->where('varhany_net_id', $varhanyNetOrganBuilderId)
            ->first();
    }
    
    private function getOrgan(int $varhanyNetOrganId)
    {
        return Organ::withoutGlobalScope(OwnedEntityScope::class)
            ->where('varhany_net_id', $varhanyNetOrganId)
            ->first();
    }
    
    private function resolveOrganBuilder(int $varhanyNetOrganBuilderId)
    {
        return $this->getOrganBuilder($varhanyNetOrganBuilderId)
            ?? $this->createOrganBuilder($varhanyNetOrganBuilderId);
    }
    
    private function handleDisposition(int $varhanyNetDispositionId)
    {
        $scraped = $this->service->scrapeDisposition($varhanyNetDispositionId);
        
        $organ = Organ::withoutGlobalScope(OwnedEntityScope::class)
            ->where('municipality', $scraped['municipality'])
            ->where('place', $scraped['place'])
            ->where(function (Builder $query) {
                $query
                    ->whereNull('user_id')
                    ->orWhere('user_id', User::USER_ID_MARTIN_KORDAS);
            })
            ->first();
            
        if (!$organ) $this->error("Varhany podle lokality uvedené v dispozici nebyly nalezeny (varhanyNetDispositionId: $varhanyNetDispositionId).");
        elseif (!$organ->isPublic()) {
            $organ->disposition ??= '';
            if ($organ->disposition !== '') $organ->disposition .= "\n\n___\n";
            if (isset($scraped['year'])) $organ->disposition .= "*Dispozice pro stav varhan v r. {$scraped['year']}*:\n\n";
            else $organ->disposition .= "Dispozice z neznámého roku:\n\n";
            $organ->disposition .= $scraped['disposition'];
            $organ->save();
            
            $this->info("Úspěšně zpracována dispozice $varhanyNetDispositionId (organId: {$organ->id}).");
        }
    }
}
