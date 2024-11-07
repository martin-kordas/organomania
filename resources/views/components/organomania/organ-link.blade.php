@props(['organ', 'year' => null])

@php
    if (isset($organ->perex)) $description = $organ->perex;
    elseif (isset($organ->description)) $description = str($organ->description)->limit(200);
    else $description = null;

    $year ??= $organ->year_built;
@endphp

@can('view', $organ)
    <a
        class="organ-link link-primary text-decoration-none"
        href="{{ route('organs.show', $organ->slug) }}"
        wire:navigate
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organ->municipality }}, {{ $organ->place }}@if ($year) ({{ $year }})@endif"
            data-bs-content="{{ $description }}"
        @endif
    >
        <x-organomania.organ-link-content :$organ :$year />
    </a>
@else
    <x-organomania.organ-link-content :$organ :$year />
@endcan
