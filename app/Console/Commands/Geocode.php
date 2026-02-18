<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrganBuilderAdditionalImage;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Scopes\OwnedEntityScope;
use App\Interfaces\GeocodingService;
use RuntimeException;

//  - některé varhany jsou v okolních zemích
//  - pro varhanáře se v praxi nepoužilo, protože jejich místo působení je nespecifické (např. "Lhota")
class Geocode extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:geocode {--type=organ} {startId} {endId?}';

    /**
     * @var string
     */
    protected $description = 'Compute latitude, longitude and region for organs and organ builders';

    protected GeocodingService $service;

    /**
     * Execute the console command.
     */
    public function handle(GeocodingService $service)
    {
        if ($this->option('type') === 'organBuilderAdditionalImage') {
            $this->service = $service;
            foreach (OrganBuilderAdditionalImage::where('organ_builder_id', $this->argument('startId'))->get() as $additionalImage) {
                $item = $this->getItem(new OrganBuilderAdditionalImage, $additionalImage->id, ownedEntity: false);
                if ($item) {
                    //$address = str($item['name'])->replaceFirst(', ', ', kostel ');
                    $address = $item['name'];
                    $address = str_replace('evang. ', 'evangelický ', $address);
                    $this->handleItem($item, $address, hasRegion: false);
                }
            }
        }
        die;


        $this->service = $service;

        $startId = (int)$this->argument('startId');
        $endId = (int)($this->argument('endId') ?? $startId);
        if ($startId <= 0 || $endId <= 0) $this->fail('Zadaná ID jsou v nesprávném tvaru.');

        $type = $this->option('type');
        if (!in_array($type, ['organ', 'organBuilder', 'organBuilderAdditionalImage'])) $this->fail('Neplatná volba "type".');

        for ($id = $startId; $id <= $endId; $id++) {
            if ($type === 'organBuilderAdditionalImage') {
                $additionalImage = $this->getItem(new OrganBuilderAdditionalImage, $id, ownedEntity: false);
                if ($additionalImage) {
                    $address = $additionalImage['name'];
                    $this->handleItem($additionalImage, $address, hasRegion: false);
                }
            }
            elseif ($type === 'organBuilder') {
                $organBuilder = $this->getItem(new OrganBuilder, $id);
                if ($organBuilder) {
                    $address = $organBuilder['municipality'];
                    $this->handleItem($organBuilder, $address);
                }
            }
            else {
                $organ = $this->getItem(new Organ, $id);
                if ($organ) {
                    $address = "{$organ['municipality']} {$organ['place']}";
                    $addresses = [$address, ...$this->getAlternativeAddresses($address)];

                    foreach ($addresses as $i => $address1) {
                        if ($this->handleItem($organ, $address1, $i + 1)) break;
                    }
                }
            }
        }
    }

    private function handleItem(Model $item, string $address, int $attempt = 1, bool $hasRegion = true): bool
    {
        try {
            $res = $this->service->geocode($address);
            $item->latitude = $res['latitude'];
            $item->longitude = $res['longitude'];
            if ($hasRegion) $item->region_id = $res['regionId'];
            $item->save();
            $this->info("Úspěšně zjištěna pozice (id: {$item->id})");
        }
        catch (RuntimeException $ex) {
            if ($ex->getMessage() === 'Pozice nebyla nalezena.') {
                $this->error("CHYBA! Nenalezena přesná pozice (id: {$item->id}, pokus: $attempt)");
                return false;
            }
            else throw $ex;
        }
        return true;
    }

    private function getItem(Model $model, int $id, bool $ownedEntity = true)
    {
        return $model
            ->withoutGlobalScope(OwnedEntityScope::class)
            ->where(function ($query) {
                $query->whereNull('latitude')->orWhere('latitude', 0);
            })
            ->when($ownedEntity, fn ($query) => $query->where('user_id', 5))
            ->find($id);
    }

    private function getAlternativeAddresses(string $address)
    {
        $count = null;
        $address1 = str_replace([
            'klášter augustiniánů - ',
            'klášter kapucínů - ',
            'farní ',
            'zámecká ',
            'proboštský ',
            ', chorální varhany',
            ', menší varhany',
            ', figurální varhany',
            ', kněžský kůr',
            ', pravé křídlo kůru',
            ', druhé varhany',
            ', postranní loď',
        ], '', $address, $count);

        if ($count > 0) yield $address1;

        $address2 = strtr($address1, [
            'kostel' => 'kaple',
            'kaple' => 'kostel',
        ]);
        if ($address2 !== $address1) yield $address2;

        $address3 = strtr($address1, [
            'sv. ' => '',
        ]);
        if ($address3 !== $address1) yield $address3;
    }

}
