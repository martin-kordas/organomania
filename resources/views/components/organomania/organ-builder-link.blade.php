@use(Illuminate\Support\Facades\Gate)
@use(Illuminate\Support\Facades\URL)
@use(App\Services\MarkdownConvertorService)

@props([
    'organBuilder', 'name' => null, 'yearBuilt' => null, 'isRebuild' => false, 'isCaseBuilt' => false,
    'showActivePeriod' => false, 'activePeriod' => null, 'showMunicipality' => false, 'showIcon' => true, 'showDescription' => true, 'showOrganWerk' => false,
    'placeholder' => __('neznámý varhanář'), 'iconLink' => true,
    'signed' => false,
])

@php
    $description = null;
    if ($showDescription) {
        if (isset($organBuilder->perex)) $description = $organBuilder->perex;
        elseif (isset($organBuilder->description)) {
            $description = app(MarkdownConvertorService::class)->stripMarkDown($organBuilder->description);
            $description = str($description)->limit(200);
        }
    }

    $canView = Gate::allows('view', $organBuilder);
    if ($showLink = $canView || $signed) {
        if ($canView) $href = route('organ-builders.show', $organBuilder->slug, absolute: false);
        else $href = URL::signedRoute('organ-builders.show', $organBuilder->slug, absolute: false);
    }
@endphp

@if ($showLink)
    <a
        {{ $attributes->class(['organ-builder-link', 'align-items-start', 'link-primary', 'text-decoration-none', 'icon-link' => $iconLink, 'icon-link-hover'=> $iconLink]) }}
        href="{{ url($href) }}"
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
        <x-organomania.organ-builder-link-content :$organBuilder :$name :$yearBuilt :$isRebuild :$isCaseBuilt :$showActivePeriod :$activePeriod :$showMunicipality :$showOrganWerk :$placeholder />
    </a>
@else
    <x-organomania.organ-builder-link-content :$organBuilder :$name :$yearBuilt :$isRebuild :$isCaseBuilt :$showActivePeriod :$activePeriod :$showMunicipality :$showOrganWerk :$placeholder />
@endif
