@props(['subtitle' => null, 'organBuilderCategory' => null, 'organCategory' => null, 'casePeriodCategory' => null])

<div {{ $attributes->class('d-flex flex-wrap row-gap-3 column-gap-2 align-items-end mb-3') }}>
    <div class="me-auto pe-1 mt-1">
        <h4>{{ $slot }}</h4>
        @isset($subtitle)
            <h6 class="mb-0 fst-italic text-body-secondary">{{ $subtitle }}</h6>
        @endisset
    </div>
    <div>
        @isset($organBuilderCategory)
            <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('organ-builders.index', ['filterCategories' => [$organBuilderCategory->value]]) }}" wire:navigate>
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline">
                    {{ __('Katalog varhanářů') }}
                </span>
                <span class="d-sm-none">
                    {{ __('Varhanáři') }}
                </span>
                <span class="badge text-bg-secondary rounded-pill">
                    {{ $organBuilderCategory->getOrganBuildersCount() }}
                </span>
            </a>
        @endisset

        @isset($organCategory)
            <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('organs.index', ['filterCategories' => [$organCategory->value]]) }}" wire:navigate>
                <i class="bi bi-music-note-list"></i>
                <span class="d-none d-sm-inline">
                    {{ __('Katalog varhan') }}
                </span>
                <span class="d-sm-none">
                    {{ __('Varhany') }}
                </span>
                <span class="badge text-bg-secondary rounded-pill">
                    {{ $organCategory->getOrgansCount() }}
                </span>
            </a>
        @endisset

        @isset($casePeriodCategory)
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('organs.cases', ['filterPeriodCategories' => [$casePeriodCategory->value], 'groupBy' => 'periodCategory']) }}" wire:navigate>
                <span class="d-none d-sm-inline">
                    {{ __('Přehled skříní') }}
                </span>
                <span class="d-sm-none">
                    {{ __('Skříně') }}
                </span>
            </a>
        @endisset
    </div>
</div>
