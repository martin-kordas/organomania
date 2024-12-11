@props(['organ', 'showIcon' => true])

@can('view', $organ)
    <a
        class="link-primary icon-link icon-link-hover align-items-start text-decoration-none"
        href="{{ route('organs.show', $organ->slug) }}"
        wire:navigate
    >
@endcan
        
@if ($showIcon)
    <i class="bi bi-music-note-list"></i>
@endif
{{ $organ->organBuilder?->name ?? __('neznámý varhanář') }}
        
@can('view', $organ)
    </a>
@endcan

@if ($organ->year_built)
    <span class="text-secondary">({{ $organ->year_built }})</span>
@endif
