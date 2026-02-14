@props(['organs', 'thumbnailOrgan', 'thumbnailComponent',  'additionalImages' => []])

@php
    use App\Helpers;
    use App\Repositories\OrganRepository;
    use App\Repositories\OrganBuilderRepository;

    $organs = Helpers::adjustCoordinates($organs);
    $additionalImages = Helpers::adjustCoordinates(collect($additionalImages));
    $zoom = Helpers::isMobile() ? '6.3' : '7.8';
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
        zoom="{{ $zoom }}"
        map-id="{{ $this->mapId }}"
        style="height: 70vh"
        rendering-type="vector"
        wire:ignore
        @if ($this->useMapClusters) data-use-map-clusters @endif
    >
        @if (!isset($this->thumbnailOrgan))
            @foreach ($additionalImages as $additionalImage)
                <gmp-advanced-marker
                    class="simple-marker"
                    position="{{ $additionalImage->latitude }},{{ $additionalImage->longitude }}"
                >
                    <gmp-pin
                        background="white"
                        scale="0.8"
                        title="{{ $additionalImage->getMapMarkerTitle(withOrganBuilder: true) }}"
                        onclick="window.open({{ Js::from($additionalImage->getViewUrl()) }}, '_blank')"
                        glyph-color="black"
                        border-color="black"
                    ></gmp-pin>
                </gmp-advanced-marker>
            @endforeach

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
