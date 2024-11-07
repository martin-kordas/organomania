@props(['organ', 'year' => null])

<span>
    {{ $organ->municipality }}, {{ $organ->place }}
    <span class="text-secondary">
        @if ($year)
            ({{ $year }})
        @endif
    </span>
</span>
