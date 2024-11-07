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

<th {{ $attributes->class(['position-sticky' => $sticky, 'start-0' => $sticky]) }}>
    <a
        href="#"
        class="link-primary"
        wire:click="$parent.sort('{{ $sortOption['column'] }}', '{{ $newDirection }}')"
        {{-- Bootstrap title nefunguje správně, protože se mění text stejného tooltipu --}}
        title="{{ __("Seřadit podle sloupce :column $newDirectionText", ['column' => __($sortOption['label'])]) }}"
    >
        {{ $label }}
    </a>
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif
</th>
