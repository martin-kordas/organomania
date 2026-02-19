@props(['marker', 'title' => '', 'inland' => true, 'otherMarkers' => collect()])

@use(App\Helpers)
@use(App\Models\OrganBuilderAdditionalImage)
@use(App\Models\Organ)

@php
    $otherMarkers = Helpers::adjustCoordinates(collect($otherMarkers));

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
            $color = null;

            // TODO: v title by šlo zobrazit i velikost varhan
            // TODO: optimalizace ::organBuilder, ::renovationOrganBuilder, ::caseOrganBuilder (N+1 problém)
            if ($marker1 instanceof Organ) {
                $isOrganBuilder = $marker1->organBuilder?->id === $marker->id;
                $isCaseOrganBuilder = $marker1->caseOrganBuilder?->id === $marker->id;
                $isRenovationOrganBuilder = $marker1->renovationOrganBuilder?->id === $marker->id;

                $background = $isRenovationOrganBuilder ? '#e8e8e8' : 'white';
                $title1 = "{$marker1->municipality}, {$marker1->place}";

                $details = [];
                if ($isOrganBuilder && $marker1->year_built) $details[] = $marker1->year_built;
                elseif ($isCaseOrganBuilder && $marker1->case_year_built) $details[] = $marker1->case_year_built;
                if ($isCaseOrganBuilder) $details[] = __('dochována skříň');
                if (!empty($details)) {
                    $title1 .= "\n";
                    $title1 .= sprintf('(%s)', implode(', ', $details));
                }
            }
            elseif ($marker1 instanceof OrganBuilderAdditionalImage) {
                $background = 'white';
                $color = 'black';
                $title1 = $marker1->getMapMarkerTitle();
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
                @isset($color) glyph-color="{{ $color }}" border-color="{{ $color }}" @endisset
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
