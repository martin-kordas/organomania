@props(['organ', 'year' => null, 'yearBuilt' => null, 'showOrganBuilder' => false, 'isRebuild' => false, 'isRenovation' => false])

@php
    $details = [];
    if ($showOrganBuilder && isset($organ->organBuilder)) $details[] = $organ->organBuilder->shortName;
    if ($year) $details[] = $year;
    if ($isRebuild) $details[] = __('přestavba');
    elseif ($isRenovation) {
        $organInfo = $organ->organBuilder->shortName ?? __('neznámý varhanář');
        if (isset($organ->year_built)) $organInfo .= " {$organ->year_built}";
        $details[] = $organInfo;
    }
@endphp

<span>
    {{ $organ->municipality }}, {{ $organ->place }}
    @if (!$organ->isPublic())
        <i class="bi bi-lock text-warning"></i>
    @endif
    @if (!empty($details))
        <span class="text-secondary">
            ({{ implode(', ', $details) }})
        </span>
    @endif
</span>
