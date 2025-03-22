@props(['sortOption', 'sticky' => false])

@php
    $direction = $this->getColumnCurrentSortDirection($sortOption['column']);
    $newDirection = $direction === 'asc' ? 'desc' : 'asc';
    $newDirectionText = $newDirection === 'asc' ? 'vzestupně' : 'sestupně';

    if (isset($direction)) {
        $directionPostfix = $direction === 'asc' ? 'up' : 'down-alt';
        $icon = "bi-sort-{$sortOption['type']}-$directionPostfix";
    }
    else $icon = null;

    $label = __($sortOption['shortLabel'] ?? $sortOption['label']);
@endphp

<th {{ $attributes->class(['text-nowrap', 'position-sticky' => $sticky, 'start-0' => $sticky]) }}>
    <a
        href="#"
        class="link-primary link-underline-opacity-50 link-underline-opacity-75-hover"
        wire:click="$parent.sort('{{ $sortOption['column'] }}', '{{ $newDirection }}')"
        {{-- Bootstrap title nefunguje správně, protože se mění text stejného tooltipu --}}
        title="{{ __("Seřadit podle sloupce :column $newDirectionText", ['column' => __($sortOption['label'])]) }}"
    >
        <span class="text-wrap">{{ $label }}</span>
    </a>
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif
</th>
