@props([
    'organCategoriesGroups', 'organCustomCategoriesGroups', 'isCustomCategoryOrgans',
    'organBuilders',
    'regions',
    'entityClass',
])

@php
    use App\Models\Organ;
    use App\Models\OrganBuilder;
    use App\Models\Festival;
    use App\Models\Competition;
@endphp

<div class="modal fade" id="filtersModal" tabindex="-1" data-focus="false" aria-labelledby="filtersModalLabel" aria-hidden="true" @keydown.enter="onEnter" data-autofocus="{{ $this->filtersModalAutofocus }}">
    <div class="modal-dialog">
        <form class="filters-form modal-content" onsubmit="return false">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="filtersModalLabel">{{ __('Filtry') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                @if (in_array($entityClass, [Festival::class, Competition::class]))
                    <div class="mb-3">
                        <label class="form-label" for="filterNameLocality">{{ __('Název, lokalita') }}</label>
                        <input id="filterNameLocality" class="form-control" type="search" wire:model="filterNameLocality" minlength="3" />
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
                    <div class="mb-3">
                        <label class="form-label" for="filterLocality">{{ __('Lokalita') }}</label>
                        <input class="form-control" type="search" id="filterLocality" wire:model="filterLocality" minlength="3" placeholder="{{ __('Zadejte obec nebo název kostela') }}" />
                    </div>
                @elseif ($entityClass === OrganBuilder::class)
                    <div class="mb-3">
                        <label class="form-label" for="filterName">{{ __('Jméno') }}, {{ __('název dílny') }}</label>
                        <input class="form-control" type="search" id="filterName" wire:model="filterName" minlength="3" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="filterMunicipality">{{ __('Lokalita') }}</label>
                        <input class="form-control" type="search" id="filterMunicipality" wire:model="filterMunicipality" minlength="3" placeholder="{{ __('Zadejte obec') }}" />
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label" for="filterRegion">{{ __('Kraj') }}</label>
                    <x-organomania.selects.region-select :regions="$regions" id="filterRegion" model="filterRegionId" :allowClear="true" />
                </div>
                @if ($entityClass !== Competition::class)
                    <div class="mb-3">
                        <label class="form-label" for="filterImportance">{{ __('Význam') }} >= <span class="text-secondary">({{ __('od') }} 1 {{ __('do') }} {{ $this->maxImportance }})</span></label>
                        <input class="form-control" type="number" min="1" max="{{ $this->maxImportance }}" id="filterImportance" wire:model.number="filterImportance" />
                    </div>
                @endif
                
                @if ($entityClass === Organ::class)
                    <div class="mb-3">
                        <label class="form-label" for="filterDisposition">{{ __('Dispozice') }}</label>
                        <input class="form-control" type="search" id="filterDisposition" wire:model="filterDisposition" minlength="3" placeholder="{{ __('Zadejte název rejstříku nebo pomocného zařízení') }}" />
                        <div class="form-text">{{ __('Název rejstříku musí být zadán přesně, jak je uveden v dispozici (např. Prinzipal namísto Principál).') }}</div>
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterConcertHall">{{ __('Jen varhany v koncertních síních') }}</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterConcertHall" wire:model="filterConcertHall">
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterForeignOrganBuilder">{{ __('Jen varhany postavené zahraničním varhanářem') }}</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterForeignOrganBuilder" wire:model="filterForeignOrganBuilder">
                    </div>
                    @if ($this->showFilterHasDisposition)
                        <div class="form-check form-switch">
                            <label class="form-check-label" for="filterHasDisposition">{{ __('Jen varhany s uvedenou dispozicí') }}</label>
                            <input class="form-check-input" type="checkbox" role="switch" id="filterHasDisposition" wire:model="filterHasDisposition">
                        </div>
                    @endif
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterValuableCase">{{ __('Jen varhany s obzvláště cennou skříni') }}</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterValuableCase" wire:model="filterValuableCase">
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
                <button id="filterButton" type="button" class="btn btn-primary" @click="submitFilters()">
                    <i class="bi-funnel"></i> {{ __('Filtrovat') }}
                </button>
            </div>
        </form>

    </div>
</div>

@script
<script>
    window.submitFilters = function () {
        let form = $('.filters-form')[0]
        if (form.checkValidity()) {
            let modal = bootstrap.Modal.getOrCreateInstance('#filtersModal')
            modal.hide()
            $wire.$refresh()
        }
        else form.reportValidity()
    }
    
    window.onEnter = function (e) {
        let isSelect2 = $(e.target).closest('.select2-container').length > 0
        if (!isSelect2) $('#filterButton').click()
    }
        
    $(() => {
        $('#filtersModal').each(function () {
            this.addEventListener('shown.bs.modal', (e) => {
                let elem = $($(this).data('autofocus'))
                if (elem.hasClass('select2')) elem.select2('focus')
                else elem.focus()
            })
        })
    })
</script>
@endscript