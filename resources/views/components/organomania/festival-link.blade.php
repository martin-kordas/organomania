@props(['festival', 'showDetails' => true, 'name' => null, 'iconLink' => true])

@php
    if (isset($festival->perex)) $description = str($festival->perex)->limit(200);
    else $description = null;
@endphp

<a
    @class(['festival-link', 'icon-link-hover', 'align-items-start', 'link-primary', 'text-decoration-none', 'icon-link' => $iconLink])
    wire:navigate
    href="{{ route('festivals.show', [$festival->slug]) }}"
    @if ($description)
        data-bs-trigger="hover focus"
        data-bs-toggle="popover"
        data-bs-title="{{ $festival->name }}"
        data-bs-content="{{ $description }}"
    @endif
>
    <i class="bi bi-calendar-date"></i>
    <span>
        {{ $name ?? $festival->name }}
        @if ($showDetails && (isset($festival->locality) || isset($festival->frequency)))
            <span class="text-body-secondary">
                ({{ collect([$festival->locality ?? null, $festival->frequency ?? null])->filter()->join(', ') }})
            </span>
        @endif
    </span>
</a>
