@props(['organ', 'name' => null, 'size' => null, 'year' => null, 'showOrganBuilder' => false, 'showSizeInfo' => false, 'isRebuild' => false, 'isRenovation' => false, 'iconLink' => true, 'newTab' => false])

@php
    if (isset($organ->perex)) $description = $organ->perex;
    elseif (isset($organ->description)) {
        $description = str($organ->description)
            ->replace('*', '')      // odstranění markdownu
            ->limit(200);
    }
    else $description = null;

    $year ??= $organ->year_built;
    
    $popoverDetails = [];
    if ($organ->organBuilder) $popoverDetails[] = $organ->organBuilder->shortName;
    $popoverDetails[] = $year;
@endphp

@can('view', $organ)
    <a
        {{ $attributes->class(['organ-link', 'align-items-start', 'link-primary', 'text-decoration-none', 'icon-link' => $iconLink, 'icon-link-hover' => $iconLink]) }}
        href="{{ route('organs.show', $organ->slug) }}"
        @if ($newTab) target="_blank" @else wire:navigate @endif
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organ->municipality }}, {{ $organ->place }}@isset($organ->year_built) ({{ implode(', ', $popoverDetails) }})@endisset"
            data-bs-content="{{ $description }}"
        @endif
    >
        <i class="bi bi-music-note-list"></i>
        <x-organomania.organ-link-content :$organ :$name :$size :$year :$showOrganBuilder :$showSizeInfo :$isRebuild :$isRenovation />
    </a>
@else
    <x-organomania.organ-link-content :$organ :$name :$size :$year :$showOrganBuilder :$showSizeInfo :$isRebuild :$isRenovation />
@endcan
