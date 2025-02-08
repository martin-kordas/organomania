<?php

namespace App\Services;

use App\Models\Region;
use App\Interfaces\GeocodingService;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleGeocodingService implements GeocodingService
{
    
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_API_KEY_BACKEND');
    }

    public function geocode(string $address): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';

        $response = Http::get($url, [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $res = $data['results'][0] ?? null;
            if ($res === null) throw new RuntimeException('Pozice nebyla nalezena.');
            
            if (!in_array($res['geometry']['location_type'], ['ROOFTOP', 'GEOMETRIC_CENTER'])) {
                throw new RuntimeException('Nebyla nalezena přesná pozice.');
            }
            
            $region = collect($res['address_components'])->first(
                fn ($component) => in_array('administrative_area_level_1', $component['types'])
            )['long_name'] ?? null;
            $region = match ($region) {
                'Pardubice Region' => 'Pardubický kraj',
                default => $region
            };
            $regionId = Region::firstWhere('name', $region)?->id;
            if (!isset($regionId)) throw new RuntimeException('Region nebyl nalezen.');
            
            return [
                'latitude' => $res['geometry']['location']['lat'],
                'longitude' => $res['geometry']['location']['lng'],
                'regionId' => $regionId,
            ];
        }

        throw new RuntimeException('Server nevrátil úspěšnou odpověď.');
    }

    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';

        $response = Http::get($url, [
            'latlng' => "{$lat},{$lng}",
            'key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['results'][0]['formatted_address'] ?? null;
        }

        return null;
    }
    
}
