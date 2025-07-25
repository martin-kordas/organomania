@props(['organBuilder', 'name' => null, 'yearBuilt' => null, 'isRebuild' => false, 'showActivePeriod' => false, 'activePeriod' => null, 'showMunicipality' => false, 'placeholder' => __('neznámý varhanář')])

@use(App\Models\OrganBuilder)

@php
    $details = [];
    if ($showActivePeriod || $showMunicipality) {
        if ($showActivePeriod) $details[] = $activePeriod ?? $organBuilder->active_period;
        if ($showMunicipality) {
            $municipality = preg_replace('/ \((.*)\)/', ', $1', $organBuilder->municipality);
            $details[] = $municipality;
        }
    }
    elseif ($yearBuilt) {
        $yearBuiltStr = $yearBuilt;
        if ($isRebuild) $yearBuiltStr .= ', ' . __('přestavba');
        $details[] = $yearBuiltStr;
    }
    $regularName = $name ?? $organBuilder->name ?? null;
@endphp

<span>
    @if ($organBuilder?->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED)
        @isset($regularName)
            {{ $name ?? $organBuilder->name }}
        @else
            <span class="text-body-secondary">{{ $placeholder }}</span>
        @endisset
    @endif

    @if ($organBuilder && !$organBuilder->isPublic())
        <i class="bi bi-lock text-warning"></i>
    @endif
    @if (!empty($details))
        <span class="text-secondary">
            ({{ implode(', ', $details) }})
        </span>
    @endif
</span>
