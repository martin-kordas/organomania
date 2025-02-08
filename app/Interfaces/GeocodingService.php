<?php

namespace App\Interfaces;

interface GeocodingService
{
    
    public function geocode(string $address): ?array;
    
    public function reverseGeocode(float $lat, float $lng): ?array;
    
}
