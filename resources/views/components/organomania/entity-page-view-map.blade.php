@props(['organs', 'thumbnailOrgan', 'thumbnailComponent'])

@php
    use App\Repositories\OrganRepository;

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
    </small>
</p>

<div class="container entity-page-map">
    <gmp-map
        center="49.815148,15.565384"
        zoom="7.8"
        map-id="{{ $this->mapId }}"
        style="height: 70vh"
        rendering-type="vector"
        wire:ignore
    >
        @foreach ($organs as $organ)
            @php
                $latitude = $this->filterNearLatitude ? (float)$this->filterNearLatitude : null;
                $longitude = $this->filterNearLongitude ? (float)$this->filterNearLongitude : null;
            @endphp
            <gmp-advanced-marker
                position="{{ $organ->latitude }},{{ $organ->longitude }}"
                data-map-info="{{ $organ->getMapInfo($latitude, $longitude) }}"
                data-organ-id="{{ $organ->id }}"
                @if ($organ->latitude === $this->filterNearLatitude && $organ->longitude === $this->filterNearLongitude)
                    data-near-coordinate
                @endif
            ></gmp-advanced-marker>
        @endforeach
    </gmp-map>
  
    <div wire:ignore.self class="modal organ-thumbnail-modal fade" id="organThumbnail" tabindex="-1" data-focus="false" aria-labelledby="organThumbnailLabel" aria-hidden="true">
        <div class="modal-dialog shadow-lg" wire:key="{{ $thumbnailOrgan->id ?? 0 }}">
            <div class="modal-content">
                <x-dynamic-component :component="$this->thumbnailComponent" :organ="$thumbnailOrgan" :modal="true" />
            </div>
        </div>
    </div>
</div>

@script
<script>
    initGoogleMap($wire)
</script>
@endscript