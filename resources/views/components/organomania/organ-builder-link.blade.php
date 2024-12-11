@props(['organBuilder', 'yearBuilt' => null, 'isRebuild' => false, 'showActivePeriod' => false, 'showIcon' => true, 'placeholder' => __('neznámý varhanář')])

@php
    if (isset($organBuilder->perex)) $description = $organBuilder->perex;
    elseif (isset($organBuilder->description)) {
        $description = str($organBuilder->description)
            ->replace('*', '')      // odstranění markdownu
            ->replaceMatches('/\s+/', ' ')
            ->limit(200);
    }
    else $description = null;
@endphp

@can('view', $organBuilder)
    <a
        class="icon-link icon-link-hover align-items-start link-primary text-decoration-none"
        href="{{ route('organ-builders.show', $organBuilder->slug) }}"
        wire:navigate
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organBuilder->name }}{{ "\n" }}({{ $organBuilder->municipality }}, {{ $organBuilder->active_period }})"
            data-bs-content="{{ $description }}"
        @endif
    >
        @if ($showIcon)
            <i class="bi bi-person-circle"></i>
        @endif
        <x-organomania.organ-builder-link-content :$organBuilder :$yearBuilt :$isRebuild :$showActivePeriod :$placeholder />
    </a>
@else
    <x-organomania.organ-builder-link-content :$organBuilder :$yearBuilt :$isRebuild :$showActivePeriod :$placeholder />
@endcan
