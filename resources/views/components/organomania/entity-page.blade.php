@props(['metaDescription'])

@php
    use App\Models\Organ;
    use App\Models\OrganBuilder;
    use App\Models\Festival;
    use App\Models\Competition;
    use App\Helpers;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
@endphp

<div @class(['entity-page', "view-type-{$this->viewType}"])>
    @push('meta')
        <meta name="description" content="{{ $metaDescription }}">
    @endpush

    {{-- fixní tlačítka na pravé straně (řazení, filtrace...) --}}
    <div class="buttons side-buttons float-end z-2 position-relative">
        <div class="position-fixed ms-2">
            <div class="position-absolute side-buttons-inner text-center pb-2">
                @if ($this->isEditable)
                    <div @class(['btn-group', 'mb-2', 'mb-md-3', 'd-md-inline-flex', 'd-none' => !Auth::id() && $this->viewType === 'table'])>
                        {{-- wire:navigate: nefunguje v nepřihlášeném stavu --}}
                        <a class="btn btn-sm btn-primary w-100" href="{{ route($this->createRoute) }}">
                            <i class="bi-plus-lg"></i> {{ __('Přidat') }}
                        </a>
                        @can('createPublic', $this->model)
                            <a class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">{{ __('Zobrazit více') }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route($this->createRoute, ['public' => '1']) }}" wire:navigate>{{ __('Přidat veřejně') }}</a></li>
                            </ul>
                        @endcan
                    </div>
                @endif

                <div class="btn-group-vertical mb-1 mb-md-3 dropdown-center">
                    <a class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                        <i class="bi-funnel"></i>
                        <span class="d-none d-md-inline">
                            <br />
                            {{ __('Filtry') }}
                        </span>
                        @if ($this->activeVisibleFiltersCount > 0)
                            <span class="badge rounded-pill text-bg-primary">{{ $this->activeVisibleFiltersCount }}</span>
                        @endif
                    </a>
                    @if (!in_array($this->viewType, ['map', 'timeline']))
                        <a @class(['btn', 'btn-sm', 'btn-outline-primary', 'dropdown-toggle', 'd-none' => $this->viewType === 'table', 'd-md-inline' => $this->viewType === 'table']) data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi-sort-up"></i>
                            <span class="d-none d-md-inline">
                                <br />{{ __('Seřazení') }}
                            </span>
                            <br />
                            <span class="badge text-bg-primary text-wrap text-break">{{ $this->getSortLabel() }}</span>
                            <br />
                        </a>

                        <ul class="dropdown-menu shadow-sm sort-dropdown">
                            @foreach (static::SORT_OPTIONS as $sortOption)
                                <li>
                                    <a
                                        href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'asc')])
                                        @if ($sortOption['column'] === 'distance')
                                            @click="sortByDistance($wire, 'asc')"
                                        @else
                                            wire:click="sort('{{ $sortOption['column'] }}', 'asc')"
                                        @endif
                                    >
                                        {{ __($sortOption['label']) }} ({{ __('vzestupně') }})
                                        <i class="float-end bi-sort-{{ $sortOption['type'] }}-up"></i>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'desc')])
                                        @if ($sortOption['column'] === 'distance')
                                            @click="sortByDistance($wire, 'desc')"
                                        @else
                                            wire:click="sort('{{ $sortOption['column'] }}', 'desc')"
                                        @endif
                                    >
                                        {{ __($sortOption['label']) }} ({{ __('sestupně') }})
                                        <i class="float-end bi-sort-{{ $sortOption['type'] }}-down-alt"></i>
                                    </a>
                                </li>
                                @if (!$loop->last)
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>

                @if ($this->isExportable)
                    {{-- export není v mobilním zobrazení přístupný vůbec (tlačítko zabírá moc místa) --}}
                    <div @class(['btn-group', 'mb-3', 'd-none', 'd-md-inline-flex'])>
                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="export">
                            <span class="d-none d-md-inline"><i class="bi-table"></i></span>
                            {{ __('Export') }}
                        </button>
                        <a class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">{{ __('Zobrazit více') }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route($this->exportRoute) }}">{{ __('Exportovat vše') }}</a></li>
                        </ul>
                    </div>
                @endif
              
                @if (!in_array($this->viewType, ['map', 'timeline']))
                    <div @class(['per-page-div', 'd-none' => $this->viewType === 'thumbnails', 'd-lg-block' => $this->viewType === 'thumbnails'])>
                        <label for="perPage" class="form-label mb-1 bg-white rounded">
                            <span class="d-none d-md-inline">{!!__('Záznamů na&nbsp;stránce') !!}</span>
                            <span class="d-md-none">{{ __('Záznamů') }}</span>
                        </label>
                        <select id="perPage" class="form-select select2 form-select-sm" wire:model.change="perPage">
                            @foreach ($this->perPageValues as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="entity-page-container container ps-1">
        {{-- rychlé filtry --}}
        @if ($this->showQuickFilter)
            <form class="filters container-sm mb-2 ps-0">
                <div class="row gx-4 gy-2 justify-content-center align-items-center">
                    @if (in_array($this->entityClass, [Festival::class, Competition::class]))
                        @if ($this->viewType !== 'timeline')
                            <div class="col col-md-6">
                                <input id="quickFilterNameLocality" size="30" class="form-control" type="search" wire:model.live="filterNameLocality" placeholder="{{ __('Hledat') }} {{ $this->entityNamePluralAkuzativ }}&hellip;" />
                            </div>
                        @endif
                    @else
                        <div class="col-md-8 col-lg-7 col-xl-6 hstack">
                            <div class="input-group">
                                <div class="input-group-text category-input-group-text">
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}" onclick="setTimeout(removeTooltips);">
                                        <a href="#" class="link-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#categoriesModal">
                                            <i class="bi-tags"></i><span class="d-none d-lg-inline">&nbsp;{{ __('Kategorie') }}</span>
                                        </a>
                                    </span>
                                </div>
                                <x-organomania.selects.organ-category-select
                                    id="quickFilterCategories"
                                    model="filterCategories"
                                    placeholder="{{ $this->categorySelectPlaceholder }}..."
                                    :categoriesGroups="$this->organCategoriesGroups"
                                    :customCategoriesGroups="$this->organCustomCategoriesGroups"
                                    :allowClear="true"
                                    :live="true"
                                    :alwaysShowCustomCategories="$this->isCustomCategoryOrgans"
                                />
                            </div>

                            @can($this->gateUseCustomCategories)
                                <div class="vr mx-2"></div>
                                <a class="btn btn-outline-secondary text-nowrap" href="{{ route($this->customCategoriesRoute) }}" data-bs-toggle="tooltip" data-bs-title="{{ $this->customCategoriesTitle }}" wire:navigate>
                                    <i class="bi-tag"></i><small class="d-none d-lg-inline"> {{ __('Vlastní kategorie') }}</small>
                                </a>
                            @endcan
                        </div>

                        @canany([$this->gateLike, 'create'], $this->model)
                            <div class="col-md-4 col-xl-2 text-center">
                                @can($this->gateLike)
                                    <input id="quickFilterFavorite" type="checkbox" class="btn-check" wire:model.live="filterFavorite" autocomplete="off">
                                    <label for="quickFilterFavorite" class="btn btn-outline-danger rounded-pill" for="btn-check" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit jen oblíbené') }}">
                                        <i class="bi-heart"></i> {{ $this->favoriteOrgansCount }}
                                    </label>
                                @endcan
                                &nbsp;
                                @can('create', $this->model)
                                    <input id="quickFilterPrivate" type="checkbox" class="btn-check" wire:model.live="filterPrivate" autocomplete="off">
                                    <label for="quickFilterPrivate" class="btn btn-outline-warning rounded-pill" for="btn-check" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit jen soukromé') }}">
                                        <i class="bi-lock"></i> {{ $this->privateOrgansCount }}
                                    </label>
                                @endcan
                            </div>
                        @endcanany
                    @endif
                </div>
            </form>
        @endif

        {{-- subnavigace --}}
        <div class="container d-flex mb-3 mt-3 px-0">
            <div class="w-100">
            <ul class="nav nav-underline align-center justify-content-center row-gap-1">
                <x-organomania.view-type-nav-item viewType="thumbnails">
                    <i class="bi-image"></i> {{ __('Miniatury') }}
                </x-organomania.view-type-nav-item>
                <x-organomania.view-type-nav-item viewType="table">
                    <i class="bi-table"></i> {{ __('Tabulka') }}
                </x-organomania.view-type-nav-item>
                <x-organomania.view-type-nav-item viewType="map">
                    <i class="bi-pin-map"></i> {{ __('Mapa') }}
                </x-organomania.view-type-nav-item>
                @if (in_array($this->entityClass, [Organ::class]))
                    <x-organomania.view-type-nav-item viewType="chart">
                        <i class="bi-bar-chart-line"></i> {{ __('Srovnat velikost') }}
                    </x-organomania.view-type-nav-item>
                @endif
                @if (in_array($this->entityClass, [OrganBuilder::class, Festival::class]))
                    <x-organomania.view-type-nav-item viewType="timeline">
                        <i class="bi-clock"></i> {{ __('Časová osa') }}
                    </x-organomania.view-type-nav-item>
                @endif
            </ul>
            </div>
        </div>

        @php($showFilterRegionHint = $this->entityClass !== Competition::class && !$this->filterRegionId && !$this->filterNearLatitude && !in_array($this->viewType, ['map', 'timeline', 'chart']))
        @php($showOrganInfoHint = $this->entityClass === Organ::class && !in_array($this->viewType, ['chart']))
        @php($showSortImportaceHint = $this->entityClass === Festival::class && $this->sortColumn !== 'importance' && !in_array($this->viewType, ['map', 'timeline']))
        @php($showSortActiveFromYearHint = $this->entityClass === OrganBuilder::class && $this->sortColumn !== 'active_from_year' && !in_array($this->viewType, ['map', 'timeline']))
        @php($showOrganImportanceHint = $this->entityClass === Organ::class && $this->viewType === 'map' && $this->activeFiltersCount <= 0)
        @php($showOrganBuilderImportanceHint = $this->entityClass === OrganBuilder::class && $this->viewType === 'timeline' && $this->activeFiltersCount <= 0)
        @php($showFestivalsMonthHint = $this->entityClass === Festival::class && $this->viewType === 'map' && $this->activeFiltersCount <= 0)
        @php($showCompetitionsWarning = $this->entityClass === Competition::class)
        
        @if (
            $showFilterRegionHint || $showOrganInfoHint || $showOrganImportanceHint || $showOrganBuilderImportanceHint
            || $showSortImportaceHint || $showSortActiveFromYearHint || $showFestivalsMonthHint || $showCompetitionsWarning
        )
            <div class="mb-2">
                @if ($showFilterRegionHint)
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Objevte :entityName', ['entityName' => $this->entityNamePluralAkuzativ]) }}
                            <a class="link-primary text-decoration-none" href="#" @click="sortByDistance($wire)">{{ __('ve vašem okolí') }}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif

                @if ($showOrganInfoHint)
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Více o varhanách jako nástroji') }}
                            <a class="link-primary text-decoration-none" href="{{ route('about-organ') }}" wire:navigate>{{ __('zde') }}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif

                @if ($showOrganImportanceHint || $showOrganBuilderImportanceHint)
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Zobrazte si jen') }}
                            <a class="link-primary text-decoration-none" href="#" @click="$wire.set('filterImportance', 4)">{{ __($showOrganImportanceHint ? 'nejvýznamnější varhany' : 'nejvýznamnější varhanáře') }}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif
              
                @if ($showSortImportaceHint)
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Seřaďte festivaly') }}
                            <a class="link-primary text-decoration-none" href="#" wire:click="sort('importance', 'desc')">{!! __('podle významu') !!}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif
              
                @if ($showSortActiveFromYearHint)
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Seřaďte varhanáře') }}
                            <a class="link-primary text-decoration-none" href="#" wire:click="sort('active_from_year', 'asc')">{!! __('dle období') !!}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif

                @if ($showFestivalsMonthHint)
                    <?php $currentMonth = Carbon::now()->month; // syntaxe @php zde působí chybu ?>
                    <div class="text-center">
                        <x-organomania.info-alert class="d-inline-block mb-1">
                            {{ __('Zobrazte si jen') }}
                            <a class="link-primary text-decoration-none" href="#" onclick="return setFilterMonth({{ Js::from($currentMonth) }})">{!! __('aktuální festivaly') !!}</a>.
                        </x-organomania.info-alert>
                    </div>
                @endif

                @if ($showCompetitionsWarning) 
                    <div class="text-center">
                        <x-organomania.warning-alert class="d-inline-block mb-1">
                            {!! __('Uváděné parametry soutěží vychází z posledního známého ročníku a <strong>nemusí být aktuální</strong>! Pro aktuální informace navštivte vždy oficiální web soutěže.') !!}
                        </x-organomania.warning-alert>
                    </div>
                @endif
            </div>
        @endif

        @if ($this->showMunicipalityInfo)
            <x-organomania.municipality-info :municipalityInfo="$this->municipalityInfo" />
        @endif
        
        <livewire:dynamic-component
            :is="$this->entityPageViewComponent"
            :filterId="$this->filterId"
            :filterCategories="$this->filterCategories" :filterRegionId="$this->filterRegionId" :filterImportance="$this->filterImportance" :filterPrivate="$this->filterPrivate" :filterFavorite="$this->filterFavorite"
            :filterNearLatitude="$this->filterNearLatitude" :filterNearLongitude="$this->filterNearLongitude" :filterNearDistance="$this->filterNearDistance"
            :filterLocality="$this->filterLocality ?? null"
            :filterDisposition="$this->filterDisposition ?? null"
            :filterManualsCount="$this->filterManualsCount ?? null"
            :filterOrganBuilderId="$this->filterOrganBuilderId ?? null"
            :filterCaseOrganBuilderId="$this->filterCaseOrganBuilderId ?? null"
            :filterConcertHall="$this->filterConcertHall ?? null"
            :filterpreservedCase="$this->filterpreservedCase ?? null"
            :filterpreservedOrgan="$this->filterpreservedOrgan ?? null"
            :filterForeignOrganBuilder="$this->filterForeignOrganBuilder ?? null"
            :filterHasDisposition="$this->filterHasDisposition ?? null"
            :filterName="$this->filterName ?? null"
            :filterMunicipality="$this->filterMunicipality ?? null"
            :filterNameLocality="$this->filterNameLocality ?? null"
            :filterMonth="$this->filterMonth ?? null"
            :selectedTimelineEntityType="request()->query('selectedTimelineEntityType')" :selectedTimelineEntityId="request()->query('selectedTimelineEntityId')"
            :id="$this->id ?? null"
            :sortColumn="$this->sortColumn" :sortDirection="$this->sortDirection" :perPage="$this->perPage"
            :viewType="$this->viewType"
            :sortOptions="static::SORT_OPTIONS"
            :isCustomCategoryOrgans="$this->isCustomCategoryOrgans"
            :activeFiltersCount="$this->activeFiltersCount"
            :lazy="!Helpers::isCrawler()"
        />
        
        <x-organomania.modals.organ-filters-modal
            :organCategoriesGroups="$this->isCategorizable ? $this->organCategoriesGroups : null"
            :organCustomCategoriesGroups="$this->isCategorizable ? $this->organCustomCategoriesGroupsForModal : null"
            :regions="$this->regions"
            :organBuilders="$this->organBuilders ?? null"
            :isCustomCategoryOrgans="$this->isCustomCategoryOrgans"
            :entityClass="$this->entityClass"
        />
          
        @if ($this->isCategorizable)
            <x-organomania.modals.categories-modal :categoriesGroups="$this->organCategoriesGroups" :categoryClass="$this->categoryClass" />
        @endif
    </div>
        
    <x-organomania.toast toastId="sortByDistanceRunning">
        {{ __('Počkejte prosím, probíhá Vaší zjišťování polohy') }}&hellip;
    </x-organomania.toast>
    <x-organomania.toast toastId="sortByDistanceError" color="danger">
        {{ __('Automatické zjištění Vaší polohy se nezdařilo.') }}
        {{ __('Zvolte prosím alespoň kraj, kde se nacházíte.') }}
    </x-organomania.toast>
</div>

@script
<script>
    window.useRegionFilter = function () {
        setTimeout(
            () => $('#filterRegion').select2('open'),
            500
        )
    }
    
    window.setFilterMonth = function (month) {
        $wire.set('filterMonth', month)
        return false
    }
        
    window.sortByDistance = function ($wire, direction = 'asc') {
        let handleError = function (errorToastId) {
            if (errorToastId) showToast(errorToastId)
            openModal('#filtersModal')
            useRegionFilter()
        }
            
        let sort = function () {
            $wire.sort('distance', direction)
        }
        
        let getPositionSuccess = function ({ coords }) {
            $wire.filterNearLatitude = coords.latitude
            $wire.filterNearLongitude = coords.longitude
            $wire.filterNearDistance = 1000000
            sort()
        }
            
        let getPositionError = () => handleError('sortByDistanceError')
        
        // souřadnice již v komponentě existují, není nutné provádět geolokaci
        if ($wire.filterNearLatitude) sort();
        else if (!navigator.geolocation) handleError()
        else {
            showToast('sortByDistanceRunning')
            navigator.geolocation.getCurrentPosition(
                getPositionSuccess, getPositionError
            )
        }
    }
</script>
@endscript