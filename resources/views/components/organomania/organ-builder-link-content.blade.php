@props([
    'organBuilder', 'name' => null, 'yearBuilt' => null, 'isRebuild' => false, 'isCaseBuilt' => false,
    'showActivePeriod' => false, 'activePeriod' => null, 'showMunicipality' => false, 'showOrganWerk' => false,
    'placeholder' => __('neznámý varhanář')
])

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
        elseif ($isCaseBuilt) $yearBuiltStr .= ', ' . __('varhanní skříň');
        elseif ($showOrganWerk) $yearBuiltStr .= ', ' . __('varhanní stroj');
        $details[] = $yearBuiltStr;
    }
    // TODO: refactoring
    else {
        if ($isRebuild) $details[] =  __('přestavba');
        elseif ($isCaseBuilt) $details[] = __('varhanní skříň');
        elseif ($showOrganWerk) $details[] = __('varhanní stroj');
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
