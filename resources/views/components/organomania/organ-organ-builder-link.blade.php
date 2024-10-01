@props(['organ'])

@can('view', $organ)
    <a
        class="link-primary text-decoration-none"
        href="{{ route('organs.show', $organ->slug) }}"
        wire:navigate
    >
@endcan
        
{{ $organ->organBuilder?->name ?? __('neznámý varhanář') }}
        
@can('view', $organ)
    </a>
@endcan

@if ($organ->year_built)
    <span class="text-secondary">({{ $organ->year_built }})</span>
@endif
