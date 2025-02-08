<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Scopes\OwnedEntityScope;
use App\Interfaces\GeocodingService;
use RuntimeException;

class Geocode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:geocode {--type=organ} {startId} {endId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute latitude, longitude and region for organs and organ builders';
    
    protected GeocodingService $service;
    
    /**
     * Execute the console command.
     */
    public function handle(GeocodingService $service)
    {
        $this->service = $service;
        
        $startId = (int)$this->argument('startId');
        $endId = (int)($this->argument('endId') ?? $startId);
        if ($startId <= 0 || $endId <= 0) $this->fail('Zadaná ID jsou v nesprávném tvaru.');
        
        $type = $this->option('type');
        if (!in_array($type, ['organ', 'organBuilder'])) $this->fail('Neplatná volba "type".');
        
        for ($id = $startId; $id <= $endId; $id++) {
            if ($type === 'organBuilder') {
                $organBuilder = OrganBuilder::withoutGlobalScope(OwnedEntityScope::class)->findOrFail($id);
                $address = $organBuilder['municipality'];
                $this->handleItem($organBuilder, $address);
            }
            else {
                $organ = Organ::withoutGlobalScope(OwnedEntityScope::class)->findOrFail($id);
                $address = "{$organ['municipality']} {$organ['place']}";
                $this->handleItem($organ, $address);
            }
        }
    }
    
    private function handleItem(Model $item, string $address)
    {
        try {
            $res = $this->service->geocode($address);
            $item->latitude = $res['latitude'];
            $item->longitude = $res['longitude'];
            $item->region_id = $res['regionId'];
            $item->save();
            $this->info("Úspěšně zjištěna pozice (id: {$item->id})");
        }
        catch (RuntimeException $ex) {
            if ($ex->getMessage() === 'Pozice nebyla nalezena.') $this->error("CHYBA! Nenalezena přesná pozice (id: {$item->id})");
            else throw $ex;
        }
    }
    
}
