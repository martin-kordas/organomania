@props(['organ'])

<span>
    {{ $organ->municipality }}, {{ $organ->place }}
    <span class="text-secondary">
        @if ($organ->year_built)
            ({{ $organ->year_built }})
        @endif
    </span>
</span>
