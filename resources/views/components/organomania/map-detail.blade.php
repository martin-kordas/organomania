@props(['latitude', 'longitude', 'title' => ''])

<gmp-map
    center="49.815148,15.565384"
    zoom="7.3"
    map-id="ORGAN_MAP_ID"
    style="height: 500px"
    rendering-type="raster"
    wire:replace
>
    <gmp-advanced-marker
        position="{{ $latitude }},{{ $longitude }}"
        title="{{ $title }}"
    ></gmp-advanced-marker>
</gmp-map>
