@props(['organ', 'year' => null, 'showOrganBuilder' => false, 'showSizeInfo' => false, 'isRebuild' => false, 'isRenovation' => false])

@php
    if (isset($organ->perex)) $description = $organ->perex;
    elseif (isset($organ->description)) {
        $description = str($organ->description)
            ->replace('*', '')      // odstranění markdownu
            ->limit(200);
    }
    else $description = null;

    $year ??= $organ->year_built;
@endphp

@can('view', $organ)
    <a
        class="organ-link icon-link icon-link-hover align-items-start link-primary text-decoration-none"
        href="{{ route('organs.show', $organ->slug) }}"
        wire:navigate
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organ->municipality }}, {{ $organ->place }}@isset($organ->year_built) ({{ $organ->year_built }})@endisset"
            data-bs-content="{{ $description }}"
        @endif
    >
        <i class="bi bi-music-note-list"></i>
        <x-organomania.organ-link-content :$organ :$year :$showOrganBuilder :$showSizeInfo :$isRebuild :$isRenovation />
    </a>
@else
    <x-organomania.organ-link-content :$organ :$year :$showOrganBuilder :$showSizeInfo :$isRebuild :$isRenovation />
@endcan
