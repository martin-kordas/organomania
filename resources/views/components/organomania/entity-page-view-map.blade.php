@props(['organs', 'thumbnailOrgan', 'thumbnailComponent'])

@php
    use App\Repositories\OrganRepository;
    use App\Repositories\OrganBuilderRepository;

    // ošetření konfliktních souřadnic - posuneme je náhodné jiné místo
    $usedCoords = [];
    $getRandOffset = fn() => rand(-5000, 5000) / 10_000_000;
    foreach ($organs as $organ) {
        $coords = ['latitude' => $organ->latitude, 'longitude' => $organ->longitude];
        $exists = collect($usedCoords)->contains(
            fn($coords1) => $coords1 == $coords
        );
        if ($exists) {
            $organ->latitude += $getRandOffset();
            $organ->longitude += $getRandOffset();
        }
        else $usedCoords[] = $coords;
    }
@endphp

<p class="text-secondary text-center">
    <small>
        {{ __('Klikněte na bod na mapě pro zobrazení podrobností.') }}
        @if (in_array($this->repository::class, [OrganRepository::class, OrganBuilderRepository::class]))
            <br class="d-none d-sm-inline" />
            @if ($this->repository::class === OrganBuilderRepository::class)
                {{ __('Tmavší bod značí staršího varhanáře.') }}
            @else
                {{ __('Tmavší bod značí starší varhany.') }}
            @endif
        @endif
    </small>
</p>

<div class="container entity-page-map">
    <gmp-map
        id="entity-page-view-map"
        center="49.815148,15.565384"
        zoom="7.8"
        map-id="{{ $this->mapId }}"
        style="height: 70vh"
        rendering-type="vector"
        wire:ignore
        @if ($this->useMapClusters) data-use-map-clusters @endif
    >
        @if (!isset($this->thumbnailOrgan))
            @foreach ($organs as $organ)
                @php
                    $latitude = $this->filterNearLatitude ? (float)$this->filterNearLatitude : null;
                    $longitude = $this->filterNearLongitude ? (float)$this->filterNearLongitude : null;
                    $nearCoordinate = $this->isFilterNearCenter($organ);

                    $lightness = $this->getMapMarkerLightness($organ);
                @endphp
        
                <gmp-advanced-marker
                    position="{{ $organ->latitude }},{{ $organ->longitude }}"
                    data-map-info="{{ $organ->getMapInfo($latitude, $longitude) }}"
                    data-organ-id="{{ $organ->id }}"
                    data-label="{{ $this->getMapMarkerLabel($organ) }}"
                    data-background="blue"
                    data-lightness="{{ $lightness }}"
                    @if ($nearCoordinate)
                        data-near-coordinate
                    @else
                        style="--marker-background-lightness: {{ $lightness }}%"
                    @endif
                ></gmp-advanced-marker>
            @endforeach
        @endif
    </gmp-map>
  
    <x-organomania.modals.organ-thumbnail-modal />
</div>

@script
<script>
    initGoogleMap($wire)
    
    // TODO: převést do initGoogleMap
    // TODO: na localhostu vzniká chyba
    const mapElement = document.querySelector('#entity-page-view-map')
    // Wait for the map to be ready
    await customElements.whenDefined('gmp-map')
    mapElement.innerMap.setOptions({ scaleControl: true })
</script>
@endscript