@props(['disposition', 'highlightRegisterId' => null, 'newTab' => false])

<a
    class="disposition-link icon-link icon-link-hover align-items-start link-primary text-decoration-none"
    href="{{ route('dispositions.show', [$disposition->slug, 'highlightRegisterId' => $highlightRegisterId]) }}"
    @if ($newTab) target="_blank" @else wire:navigate @endif
>
    <i class="bi bi-card-list"></i>
    <span>
        {{ $disposition->name }}
        @if (!$disposition->isPublic())
            <i class="bi bi-lock text-warning"></i>
        @endif

        @if ($disposition->real_disposition_registers_count > 0)
            <span class="text-secondary">
                ({{ $disposition->real_disposition_registers_count }} <small>{{ $disposition->getDeclinedRealDispositionRegisters() }}</small>)
            </span>
        @endif
    </span>
</a>
