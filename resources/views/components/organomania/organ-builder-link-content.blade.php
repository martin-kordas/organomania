@props([
    'organBuilder', 'name' => null, 'yearBuilt' => null, 'isRebuild' => false, 'isCaseBuilt' => false,
    'showActivePeriod' => false, 'activePeriod' => null, 'showMunicipality' => false, 'showOrganWerk' => false,
    'placeholder' => __('neznámý varhanář'), 'shortDetails' => false,
])

@use(App\Models\OrganBuilder)

@php
    $case = $shortDetails ? __('skříň') : __('varhanní skříň');
    $werk = $shortDetails ? __('stroj') : __('varhanní stroj');

    $details = [];
    if ($showActivePeriod || $showMunicipality) {
        if ($showActivePeriod) $details[] = $activePeriod ?? $organBuilder->active_period;
        if ($showMunicipality) {
            $details[] = $organBuilder->municipalityWithoutParenthesis;
        }
    }
    elseif ($yearBuilt) {
        $yearBuiltStr = $yearBuilt;
        if ($isRebuild) $yearBuiltStr .= ', ' . __('přestavba');
        elseif ($isCaseBuilt) $yearBuiltStr .= ', ' . $case;
        elseif ($showOrganWerk) $yearBuiltStr .= ', ' . $werk;
        $details[] = $yearBuiltStr;
    }
    // TODO: refactoring
    else {
        if ($isRebuild) $details[] =  __('přestavba');
        elseif ($isCaseBuilt) $details[] = $case;
        elseif ($showOrganWerk) $details[] = $werk;
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
        <span class="text-secondary fw-normal">
            ({{ implode(', ', $details) }})
        </span>
    @endif
</span>
