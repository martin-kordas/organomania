@props(['marker', 'title' => '', 'otherMarkers' => collect()])

<gmp-map
    center="49.815148,15.565384"
    zoom="7.3"
    map-id="ORGAN_MAP_ID"
    style="height: 500px"
    rendering-type="raster"
    wire:replace
>
    @foreach ($otherMarkers as $marker1)
        @php
            // TODO: v title by šlo zobrazit i rok postavení a velikost varhan
            $renovated = $marker1->renovationOrganBuilder?->id === $marker->id;
            $background = $renovated ? '#e8e8e8' : 'white';
        @endphp
        <gmp-advanced-marker
            position="{{ $marker1->latitude }},{{ $marker1->longitude }}"
            title="{{ $title }}"
        >
            <gmp-pin
                background="{{ $background }}"
                scale="0.8"
                title="{{ $marker1->municipality . ", " . $marker1->place }}"
                onclick="window.open({{ Js::from(route('organs.show', $marker1->slug)) }}, '_blank')"
            ></gmp-pin>
        </gmp-advanced-marker>
    @endforeach

    {{-- hlavní marker jako poslední, aby překryl vedlejší markery --}}
    <gmp-advanced-marker
        position="{{ $marker->latitude }},{{ $marker->longitude }}"
    >
        <gmp-pin title="{{ $title }}"></gmp-pin>
    </gmp-advanced-marker>
</gmp-map>
