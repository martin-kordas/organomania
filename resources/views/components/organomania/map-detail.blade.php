@props(['marker', 'title' => '', 'inland' => true, 'otherMarkers' => collect()])

@use(App\Helpers)
@use(App\Models\OrganBuilderAdditionalImage)
@use(App\Models\Organ)

@php
    $center = $inland ? '49.815148,15.565384' : '47.4521023,15.070935';
    if (Helpers::isMobile()) $zoom = $inland ? '6.1' : '3.9';
    else $zoom = $inland ? '7.3' : '4.4';
@endphp

<gmp-map
    center="{{ $center }}"
    zoom="{{ $zoom }}"
    map-id="ORGAN_MAP_ID"
    style="height: 500px"
    rendering-type="raster"
    wire:replace
>
    @foreach ($otherMarkers as $marker1)
        @php
            $glyph = $borderColor = null;

            // TODO: v title by šlo zobrazit i rok postavení a velikost varhan
            // TODO: optimalizace ::renovationOrganBuilder, ::caseOrganBuilder (N+1 problém)
            if ($marker1 instanceof Organ) {
                $renovated = $marker1->renovationOrganBuilder?->id === $marker->id;
                $background = $renovated ? '#e8e8e8' : 'white';
                $title1 = "{$marker1->municipality}, {$marker1->place}";
                if ($marker1->caseOrganBuilder?->id === $marker->id) {
                    $title1 .= sprintf("\n(%s)", __('dochována skříň'));
                }
            }
            elseif ($marker1 instanceof OrganBuilderAdditionalImage) {
                $background = 'white';
                $glyph = '&#128247;';
                $borderColor = 'black';
                $title1 = $marker1->name;
                if (str($marker1->details)->contains('dochována skříň')) {
                    $title1 .= sprintf("\n(%s)", __('dochována skříň'));
                }
            }
            else {
                $background = 'var(--header-footer-background)';
                $title1 = "{$marker1->standardName} ({$marker1->municipalityWithoutParenthesis})";
            }
        @endphp

        <gmp-advanced-marker
            position="{{ $marker1->latitude }},{{ $marker1->longitude }}"
        >
            <gmp-pin
                background="{{ $background }}"
                scale="0.8"
                title="{{ $title1 }}"
                onclick="window.open({{ Js::from($marker1->getViewUrl()) }}, '_blank')"
                @isset($glyph) glyph-text="{!! $glyph !!};" @endisset
                @isset($borderColor) border-color="{{ $borderColor }}" @endisset
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
