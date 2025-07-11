@props(['organ', 'name' => null, 'size' => null, 'year' => null, 'yearBuilt' => null, 'showOrganBuilder' => false, 'showSizeInfo' => false, 'isRebuild' => false, 'isRenovation' => false])

@use(App\Models\OrganBuilder)

@php
    $details = [];
    if ($showOrganBuilder && isset($organ->organBuilder) && $organ->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) {
        $details[] = $organ->organBuilder->shortName;
    }
    if ($year) $details[] = $year;
    if ($isRebuild) $details[] = __('přestavba');
    elseif ($isRenovation) {
        $organInfo = $organ->organBuilder->shortName ?? __('neznámý varhanář');
        if (isset($organ->year_built)) $organInfo .= " {$organ->year_built}";
        $details[] = $organInfo;
    }
    elseif ($showSizeInfo) {
        if (isset($size)) $details[] = $size;
        elseif ($organ->organ_rebuilds_count <= 0 && ($sizeInfo = $organ->getSizeInfo()))
            $details[] = $sizeInfo;
    }
@endphp

<span>
    @isset($name)
        {{ $name }}
    @else
        {{ $organ->municipality }}, {{ $organ->place }}
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
