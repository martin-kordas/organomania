@props([
    'organ', 'name' => null, 'size' => null, 'year' => null,
    'showOrganBuilder' => false, 'showOrganBuilderExactOnly' => false, 'showSizeInfo' => false, 'showSizeInfoOriginal' => false, 'showDescription' => true, 'showShortPlace' => false,
    'isRebuild' => false, 'isRenovation' => false, 'showIsHistoricalCase' => false, 'showIcon' => true, 'iconLink' => true, 'newTab' => false,
])

@use(App\Models\OrganBuilder)
@use(App\Services\MarkdownConvertorService)

@php
    $description = null;
    if ($showDescription) {
        if (isset($organ->perex)) $description = $organ->perex;
        elseif (isset($organ->description)) {
            $description = app(MarkdownConvertorService::class)->stripMarkDown($organ->description);
            $description = str($description)->limit(200);
        }
    }

    $year ??= $organ->year_built;
    
    $popoverDetails = [];
    if ($organ->timelineItem) $popoverDetails[] = $organ->timelineItem->nameLowercaseWithoutComma;
    elseif ($organ->organBuilder && $organ->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) $popoverDetails[] = $organ->organBuilder->shortName;
    if ($organ->year_built) $popoverDetails[] = $organ->year_built;
@endphp

@can('view', $organ)
    <a
        {{ $attributes->class(['organ-link', 'align-items-start', 'link-primary', 'text-decoration-none', 'icon-link' => $iconLink, 'icon-link-hover' => $iconLink]) }}
        href="{{ route('organs.show', $organ->slug) }}"
        @if ($newTab) target="_blank" @else wire:navigate @endif
        @if ($description)
            data-bs-trigger="hover focus"
            data-bs-toggle="popover"
            data-bs-title="{{ $organ->municipality }}, {{ $organ->place }}@isset($organ->year_built){{ "\n" }}({{ implode(', ', $popoverDetails) }})@endisset"
            data-bs-content="{{ $description }}"
        @endif
    >
        @if ($showIcon)
            <i class="bi bi-music-note-list"></i>
        @endif
        <x-organomania.organ-link-content :$organ :$name :$size :$year :$showOrganBuilder :$showOrganBuilderExactOnly :$showSizeInfo :$showSizeInfoOriginal :$showShortPlace :$isRebuild :$isRenovation :$showIsHistoricalCase />
    </a>
@else
    <x-organomania.organ-link-content :$organ :$name :$size :$year :$showOrganBuilder :$showOrganBuilderExactOnly :$showSizeInfo :$showSizeInfoOriginal :$showShortPlace :$isRebuild :$isRenovation :$showIsHistoricalCase />
@endcan
