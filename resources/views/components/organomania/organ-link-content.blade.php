@props(['organ', 'year' => null, 'showOrganBuilder' => false, 'isRebuild' => false])

@php
    $details = [];
    if ($showOrganBuilder && isset($organ->organBuilder)) $details[] = $organ->organBuilder->shortName;
    if (isset($year)) $details[] = $year;
    if ($isRebuild) $details[] = __('přestavba');
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
