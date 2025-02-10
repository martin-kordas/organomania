<?php

namespace App\Services;

use App\Models\Region;
use App\Interfaces\GeocodingService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use LogicException;

class MapyCzGeocodingService implements GeocodingService
{
    
    protected string $baseUrl = 'https://api.mapy.cz/v1/geocode/';

    public function geocode(string $address): ?array
    {
        $apiKey = env('MAPY_CZ_API_KEY');

        $response = Http::get($this->baseUrl, [
            'query' => $address,
            'apikey' => $apiKey,
            'type' => 'poi',
            'locality' => 'cz',
            'limit' => 1,
        ]);
        
        if ($response->successful()) {
            $res = $response->json()['items'][0] ?? null;
            if (!isset($res)) throw new RuntimeException('Pozice nebyla nalezena.');
            
            $region = collect($res['regionalStructure'])->last(
                fn ($component) => $component['type'] === 'regional.region'
            )['name'] ?? null;
            $region = match ($region) {
                'kraj Hlavní město Praha' => 'Hlavní město Praha',
                default => $region
            };
            $regionId = Region::firstWhere('name', $region)?->id;
            if (!isset($regionId)) throw new RuntimeException("Region nebyl nalezen ($region).");
            
            return [
                'latitude' => $res['position']['lat'],
                'longitude' => $res['position']['lon'],
                'regionId' => $regionId,
            ];
        }

        throw new RuntimeException('Server nevrátil úspěšnou odpověď.');
    }
    
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        throw new LogicException('Not implemented.');
    }
    
}
