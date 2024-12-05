@props(['organ', 'year' => null, 'showOrganBuilder' => false])

@php
    $details = [];
    if ($showOrganBuilder && isset($organ->organBuilder)) $details[] = $organ->organBuilder->shortName;
    if (isset($year)) $details[] = $year;
@endphp

<span>
    {{ $organ->municipality }}, {{ $organ->place }}
    @if (!empty($details))
        <span class="text-secondary">
            ({{ implode(', ', $details) }})
        </span>
    @endif
</span>
