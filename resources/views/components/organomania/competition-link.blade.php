@props(['competition', 'name' => null, 'iconLink' => true])

@php
    if (isset($competition->perex)) $description = str($competition->perex)->limit(200);
    else $description = null;
@endphp

<a
    @class(['competition-link', 'icon-link-hover', 'align-items-start', 'link-primary', 'text-decoration-none', 'icon-link' => $iconLink])
    wire:navigate href="{{ route('competitions.show', [$competition->slug]) }}"
    @if ($description)
        data-bs-trigger="hover focus"
        data-bs-toggle="popover"
        data-bs-title="{{ $competition->name }}"
        data-bs-content="{{ $description }}"
    @endif
>
    <i class="bi bi-trophy"></i>
    <span>
        {{ $name ?? $competition->name }}
    </span>
</a>
