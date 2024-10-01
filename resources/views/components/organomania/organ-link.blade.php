@props(['organ'])

@php
    if (isset($organ->perex)) $description = $organ->perex;
    elseif (isset($organ->description)) $description = str($organ->description)->limit(200);
    else $description = null;
@endphp

@can('view', $organ)
    <a
        class="organ-link link-primary text-decoration-none"
        href="{{ route('organs.show', $organ->slug) }}"
        wire:navigate
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organ->municipality }}, {{ $organ->place }}@if ($organ->year_built) ({{ $organ->year_built }})@endif"
            data-bs-content="{{ $description }}"
        @endif
    >
        <x-organomania.organ-link-content :$organ />
    </a>
@else
    <x-organomania.organ-link-content :$organ />
@endcan
