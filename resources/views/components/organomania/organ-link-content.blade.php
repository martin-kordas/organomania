@props([
    'organ', 'name' => null, 'size' => null, 'year' => null,
    'yearBuilt' => null, 'showOrganBuilder' => false, 'showSizeInfo' => false, 'showSizeInfoOriginal' => false, 'showShortPlace' => false,
    'isRebuild' => false, 'isRenovation' => false, 'showIsHistoricalCase' => false,
])

@use(App\Models\OrganBuilder)

@php
    $details = [];
    if ($showOrganBuilder) {
        if (isset($organ->organ_builder_name)) $details[] = $organ->organ_builder_name;
        elseif (isset($organ->organBuilder)) {
            if ($organ->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) $details[] = $organ->organBuilder->shortName;
        }
    }
    if ($year) $details[] = $year;
    if ($isRebuild) $details[] = __('přestavba');
    if ($showIsHistoricalCase) {
        if (isset($organ->caseOrganBuilder) || isset($organ->case_organ_builder_name))
            $details[] = __('historická skříň');
    }
    elseif ($isRenovation) {
        if (isset($organ->organ_builder_name)) $organInfo = str_replace(',', '', $organ->organBuilderNameLowercase);
        else $organInfo = $organ->organBuilder->shortName ?? __('neznámý varhanář');
        if (!trim($organInfo)) $organInfo = __('postaveno');    // kvůli OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED
        if (isset($organ->year_built)) $organInfo .= " {$organ->year_built}";
        $details[] = $organInfo;
    }
    elseif ($showSizeInfo) {
        if (isset($size)) $details[] = $size;
        else {
            $showRebuilt = false;
            if ($organ->organ_rebuilds_count <= 0) $showSizeInfo1 = true;
            elseif ($showSizeInfoOriginal && $organ->hasOriginalSizeInfo()) $showSizeInfo1 = $showRebuilt = true;
            else $showSizeInfo1 = false;

            if ($showSizeInfo1 && ($sizeInfo = $organ->getSizeInfo($showSizeInfoOriginal))) {
                $details[] = $sizeInfo;
                if ($showRebuilt) $details[] = __('přestavěno');
            }
        }
    }
@endphp

<span>
    @isset($name)
        {{ $name }}
    @else
        {{ $organ->municipality }}, {{ $showShortPlace ? $organ->shortPlace : $organ->place }}
    @endisset

    @if (!$organ->isPublic())
        <i class="bi bi-lock text-warning"></i>
    @endif
    @if (!empty($details))
        <span class="text-secondary">
            ({{ implode(', ', $details) }})
        </span>
    @endif
</span>
