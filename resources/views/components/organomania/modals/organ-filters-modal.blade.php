@props([
    'organCategoriesGroups', 'organCustomCategoriesGroups', 'isCustomCategoryOrgans',
    'organBuilders',
    'regions',
    'entityClass',
])

@php
    use App\Models\Organ;
    use App\Models\Festival;
    use App\Models\Competition;
@endphp

<div class="modal fade" id="filtersModal" tabindex="-1" data-focus="false" aria-labelledby="filtersModalLabel" aria-hidden="true" @keydown.enter="onEsc">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="filtersModalLabel">{{ __('Filtry') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                @if (in_array($entityClass, [Festival::class, Competition::class]))
                    <div class="mb-3">
                        <label class="form-label" for="filterNameLocality">{{ __('Název, lokalita') }}</label>
                        <input id="filterNameLocality" class="form-control" type="search" wire:model="filterNameLocality" />
                    </div>
                @endif
                @if ($this->isCategorizable)
                    <div class="mb-3">
                        <label class="form-label" for="filterCategories">{{ __('Kategorie') }}</label>
                        <x-organomania.selects.organ-category-select
                            model="filterCategories"
                            placeholder="{{ __('Zvolte kategorii varhan') }}..."
                            :categoriesGroups="$organCategoriesGroups"
                            :customCategoriesGroups="$organCustomCategoriesGroups"
                            :allowClear="true"
                            :alwaysShowCustomCategories="$isCustomCategoryOrgans"
                        />
                    </div>
                @endif
                @if ($entityClass === Organ::class)
                    <div class="mb-3">
                        <label class="form-label" for="filterOrganBuilderId">{{ __('Varhanář') }}</label>
                        <x-organomania.selects.organ-builder-select model="filterOrganBuilderId" :organBuilders="$organBuilders" :allowClear="true" />
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label" for="filterRegion">{{ __('Kraj') }}</label>
                    <x-organomania.selects.region-select :regions="$regions" id="filterRegion" model="filterRegionId" :allowClear="true" />
                </div>
                @if ($entityClass !== Competition::class)
                    <div class="mb-3">
                        <label class="form-label" for="filterImportance">{{ __('Význam') }} >= <span class="text-secondary">({{ __('od 1 do 5') }})</span></label>
                        <input class="form-control" type="number" min="1" max="5" id="filterImportance" wire:model.number="filterImportance" />
                    </div>
                @endif
                
                @if ($entityClass === Organ::class)
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterConcertHall">{{ __('Jen nástroje v koncertních síních') }}</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterConcertHall" wire:model="filterConcertHall">
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterHasDisposition">{{ __('Jen nástroje s uvedenou dispozicí') }}</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterHasDisposition" wire:model="filterHasDisposition">
                    </div>
                @endif
                
                @if ($this->isLikeable)
                    @can($this->gateLike)
                        <div class="form-check form-switch">
                            <label class="form-check-label" for="filterFavorite">{{ __('Jen oblíbené') }}</label>
                            <input class="form-check-input" type="checkbox" role="switch" id="filterFavorite" wire:model="filterFavorite">
                            <i class="bi-heart text-danger"></i>
                        </div>
                    @endcan
                @endif

                @if ($this->isEditable)
                    @can('create', $entityClass)
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="filterPrivate" wire:model="filterPrivate">
                            <label class="form-check-label" for="filterPrivate">{{ __('Jen soukromé') }}</label>
                            <i class="bi-lock text-warning"></i>
                        </div>
                    @endcan
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                <button id="filterButton" type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="$refresh"><i class="bi-funnel"></i> {{ __('Filtrovat') }}</button>
            </div>
        </div>

    </div>
</div>

@script
<script>
    window.onEsc = function (e) {
        isSelect2 = $(e.target).closest('.select2-container').length > 0
        if (!isSelect2) $('#filterButton').click()
    }
</script>
@endscript