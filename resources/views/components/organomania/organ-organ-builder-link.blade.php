@props(['organ', 'showIcon' => true, 'iconLink' => true, 'signed' => false])

@use(Illuminate\Support\Facades\Gate)
@use(Illuminate\Support\Facades\URL)

@php
    $canView = Gate::allows('view', $organ);
    if ($showLink = $canView || $signed) {
        if ($canView) $href = route('organs.show', $organ->slug, absolute: false);
        else $href = URL::signedRoute('organs.show', $organ->slug, absolute: false);
    }
@endphp

@if ($showLink)
    <a
        {{ $attributes->class(['link-primary', 'align-items-start', 'text-decoration-none', 'icon-link' => $iconLink, 'icon-link-hover'=> $iconLink]) }}
        class="link-primary icon-link icon-link-hover align-items-start text-decoration-none"
        href="{{ $href }}"
        wire:navigate
    >
@endif
        
@if ($showIcon)
    <i class="bi bi-music-note-list"></i>
@endif

<span>
    {{ $organ->timelineItem?->name ?? $organ->organBuilder?->name ?? __('neznámý varhanář') }}
    @if ($organ && !$organ->isPublic())
        <i class="bi bi-lock text-warning d-print-none"></i>
    @endif
</span>
    
@if ($showLink)
    </a>
@endif

@if ($organ->year_built)
    <span class="text-secondary">({{ $organ->year_built }})</span>
@endif
