@props(['disposition', 'highlightRegisterId' => null, 'firstDispositionRegisterId' => null, 'newTab' => false])

@php
    $url = route('dispositions.show', [$disposition->slug, 'highlightRegisterId' => $highlightRegisterId]);
    if (isset($firstDispositionRegisterId)) $url .= "#dispositionRegister$firstDispositionRegisterId";
@endphp

<a
    class="disposition-link icon-link icon-link-hover align-items-start link-primary text-decoration-none"
    href="{{ $url }}"
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
                ({{ $disposition->real_disposition_registers_count }} {{ $disposition->getDeclinedRealDispositionRegisters() }})
            </span>
        @endif
    </span>
</a>
