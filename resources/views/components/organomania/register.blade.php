@props([
    'registerName', 'registerNames', 'register', 'showPitches' => true, 'language' => null, 'pitch' => null,
    'dispositionsLimit' => 5, 'categoriesAsLink' => false, 'excludeDispositionIds' => [], 'excludeOrganIds' => [],
    'newTab' => false
])

@use(App\Models\Register)
@use(App\Services\MarkdownConvertorService)

@php
    // TODO: výpočty do PHP komponenty
    $language ??= $registerName?->language;
    $categoryTag = $categoriesAsLink ? 'a' : 'span';
    $categoryIds = $register->registerCategories->pluck('id')->push($register->registerCategory->value);

    if (isset($register->description)) {
        $descriptionHtml = app(MarkdownConvertorService::class)->convert($register->description);
        $descriptionHtml = trim($descriptionHtml);
    }
    else $descriptionHtml = '';

    $relatedRegisterIds = match ($registerName->register_id) {
        56 => [57], 57 => [56],
        91 => [31],
        31 => [6],
        41 => [1],
        58 => [36],
        59 => [60],
        60 => [59],
        61 => [22],
        63 => [62],
        66 => [67],
        67 => [66],
        74 => [38],
        75 => [21],
        76 => [3],
        81 => [16],
        85 => [15],
        91 => [32],
        94 => [3],
        108 => [24],
        113 => [3],
        115 => [3],
        118 => [10],
        119 => [23],
        default => [],
    };
    $relatedRegisters = collect($relatedRegisterIds)->map(
        fn ($registerId) => Register::find($registerId)
    );

    $dispositionRegisterIdDispositionId = null;
    $dispositions = $register->getDispositions($excludeDispositionIds, $excludeOrganIds, $dispositionsLimit, $dispositionRegisterIdDispositionId);
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

@if ($descriptionHtml !== '')
    <div class="mt-2">
        {!! $descriptionHtml !!}
    </div>
    <hr>
@endif

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
    
@endphp
@if ($relatedRegisters->isNotEmpty() || $dispositions->isNotEmpty())
    @if (isset($pitch) || $showPitches)
        <hr>
    @endif
    @if ($relatedRegisters->isNotEmpty())
        <div class="mt-2 small">
            {{ __('Související rejstříky') }}
            <div class="items-list">
                @foreach ($relatedRegisters as $register)
                    <x-organomania.register-name-link
                        :registerName="$registerName->getRelatedRegisterName($register)"
                        :newTab="$newTab"
                    />
                    @if (!$loop->last) <br /> @endif
                @endforeach
            </div>
        </div>
    @endif
    @if ($dispositions->isNotEmpty())<div class="mt-2 small">
            {{ __('Příklady v dispozicích') }}
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
@endif
