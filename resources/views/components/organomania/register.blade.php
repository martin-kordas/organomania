@props([
    'registerName', 'registerNames', 'register', 'showPitches' => true, 'language' => null, 'pitch' => null,
    'dispositionsLimit' => 5, 'categoriesAsLink' => false, 'excludeDispositionIds' => [], 'excludeOrganIds' => [],
    'newTab' => false
])

@php
    $language ??= $registerName?->language;
    $categoryTag = $categoriesAsLink ? 'a' : 'span';
    $categoryIds = $register->registerCategories->pluck('id')->push($register->registerCategory->value);
@endphp

<div>
    {{-- 1 základní kategorie --}}
    <{{ $categoryTag }}
        class="badge text-bg-primary text-decoration-none"
        @if ($description = $register->registerCategory->getDescription())
            data-bs-toggle="tooltip"
            data-bs-title="{{ $description }}"
        @endif
        @if ($categoriesAsLink)
            href="{{ route('dispositions.registers.index', ['filterCategories' => [$register->registerCategory->value]]) }}"
            wire:navigate
        @endif
    >
        {{ $register->registerCategory->getName() }}
    </{{ $categoryTag }}>
    
    {{-- N ostatních kategorií --}}
    @foreach ($register->registerCategories as $category)
        <{{ $categoryTag }}
            class="badge text-bg-secondary text-decoration-none"
            @if ($description = $category->getEnum()->getDescription())
                data-bs-toggle="tooltip"
                data-bs-title="{{ $description }}"
            @endif
            @if ($categoriesAsLink)
                href="{{ route('dispositions.registers.index', ['filterCategories' => [$category->id]]) }}"
                wire:navigate
            @endif
        >
            {{ $category->getEnum()->getName() }}
        </{{ $categoryTag }}>
    @endforeach
        
    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}" onclick="setTimeout(removeTooltips);">
        <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#categoriesModal" @click="highlightCategoriesInModal(@json($categoryIds))">
            <i class="bi bi-question-circle"></i>
        </a>
    </span>
</div>

@isset($register->description)
    <div class="mt-2">
        {{ $register->description }}
    </div>
    <hr>
@endisset

@isset($pitch)
    <div class="mt-2">
        {{ __('Poloha') }}: <em>{{ $pitch->getLabel($language) }}</em>
        <small class="text-body-secondary">
            ({{ $pitch->getInterval() }} {{ __('poloha') }} &ndash; {{ __('na klávese') }} c<sup>1</sup> {{ __('zní tón') }} {!! $pitch->getAliquoteToneFormatted() !!})
        </small>
    </div>
@endisset

@if ($showPitches)
    <div class="mt-2">
        {{ __('Běžné polohy rejstříku') }}: <em>{{ $register->getPitchesLabels($language)->implode(', ') }}</em>
    </div>
@endif

@php 
    $dispositionRegisterIdDispositionId = null;
    $dispositions = $register->getDispositions($excludeDispositionIds, $excludeOrganIds, $dispositionsLimit, $dispositionRegisterIdDispositionId);
@endphp
@if ($dispositions->isNotEmpty())
    <div class="mt-2">
        {{ __('Příklady v dispozicích') }}:
        <div class="items-list">
            @foreach ($dispositions as $disposition)
                <x-organomania.disposition-link
                    :disposition="$disposition"
                    :highlightRegisterId="$registerName->register_id"
                    :firstDispositionRegisterId="$dispositionRegisterIdDispositionId[$disposition->id]"
                    :newTab="$newTab"
                />
                @if (!$loop->last) <br /> @endif
            @endforeach
        </div>
    </div>
@endif
