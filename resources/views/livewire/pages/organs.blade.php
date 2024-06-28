<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\Attributes\On; 
use Livewire\Attributes\Url;
use App\Models\Region;
use App\Models\OrganBuilder;
use App\Models\Organ;
use App\Enums\OrganCategory;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    #[Url(keep: true)]
    public $viewType = 'thumbnails';

    #[Url(keep: true)]
    public $sortColumn = 'importance';
    #[Url(keep: true)]
    public $sortDirection = 'desc';

    #[Url(keep: true)]
    public $filterCategories;
    #[Url(keep: true)]
    public $filterOrganBuilderId;
    #[Url(keep: true)]
    public $filterRegionId;
    #[Url(keep: true)]
    public $filterFavorite;
    #[Url(keep: true)]
    public $filterPrivate;

    public $favoriteOrgansCount;
    public $privateOrgansCount;

    const SORT_OPTIONS = [
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric'],
        ['column' => 'year_built', 'label' => 'Rok', 'type' => 'numeric'],
        ['column' => 'manuals_count', 'label' => 'Počet manuálů', 'type' => 'numeric'],
        ['column' => 'stops_count', 'label' => 'Počet rejstříků', 'type' => 'numeric'],
        ['column' => 'municipality', 'label' => 'Obec', 'type' => 'alpha'],
    ];

    public function mount()
    {
        $this->favoriteOrgansCount = $this->getFavoriteOrgansCount();
        $this->privateOrgansCount = $this->getPrivateOrgansCount();
    }

    private function getFavoriteOrgansCount()
    {
        return Organ::query()->whereHas('organLikes', function (Builder $query) {
            $query->where('user_id', Auth::id());
        })->count();
    }

    private function getPrivateOrgansCount()
    {
        return Organ::query()->whereNotNull('user_id')->count();
    }

    private function isCurrentSort($column, $direction)
    {
        return $column === $this->sortColumn && $direction === $this->sortDirection;
    }

    private function getCurrentSortOption()
    {
        return $this->getSortOption($this->sortColumn);
    }

    private function getSortLabel()
    {
        $sortOption = $this->getCurrentSortOption();
        $label = __($sortOption['label']);
        $arrow = $this->sortDirection === 'asc' ? '↑' : '↓';
        return "$label $arrow";
    }

    private function getSortOption($column)
    {
        foreach (static::SORT_OPTIONS as $sortOption) {
            if ($sortOption['column'] === $column) {
                return $sortOption;
            }
        }
        throw new \RuntimeException;
    }

    #[Computed]
    public function activeFiltersCount()
    {
        $count = 0;
        if ($this->filterCategories) $count++;
        if ($this->filterOrganBuilderId) $count++;
        if ($this->filterRegionId) $count++;
        if ($this->filterFavorite) $count++;
        if ($this->filterPrivate) $count++;
        return $count;
    }

    public function sort($column, $direction)
    {
        if ($this->getSortOption($column)) {
            $this->sortColumn = $column;
            $this->sortDirection = $direction;
        }
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }

    #[Computed]
    public function organBuilders()
    {
        return OrganBuilder::query()->orderBy(DB::raw('IFNULL(last_name, workshop_name)'))->get();
    }

    #[On('organ-like-updated')] 
    public function updatePostList(int $diff)
    {
        $this->favoriteOrgansCount += $diff;
    }

    public function rendered()
    {
        $this->dispatch("select2-rendered");
    }

}; ?>

<div class="organs container">
    <div class="buttons float-end z-2 position-relative">
        <div class="position-fixed ms-4">
            <div class="position-absolute text-center">
                <a type="button" class="btn btn-sm btn-primary mb-3 disabled"><i class="bi-plus-lg"></i> Přidat</a>
                <div class="btn-group-vertical mb-3 dropdown-center">
                    <a type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal"><i class="bi-funnel"></i><br />Filtry
                        @if ($this->activeFiltersCount > 0)
                            <span class="badge rounded-pill text-bg-primary">{{ $this->activeFiltersCount }}</span>
                        @endif
                    </a>
                    <a type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi-sort-up"></i>
                        <br />Seřazení<br />
                        <span class="badge text-bg-primary text-wrap">{{ $this->getSortLabel() }}</span>
                        <br />
                    </a>

                    <ul class="dropdown-menu shadow-sm" style="font-size: 80%; min-width: 14rem;">
                        @foreach (static::SORT_OPTIONS as $sortOption)
                            <li>
                                <a href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'asc')]) wire:click="sort('{{ $sortOption['column'] }}', 'asc')">
                                    {{ $sortOption['label'] }} ({{ __('vzestupně') }})
                                    <i class="float-end bi-sort-{{ $sortOption['type'] }}-up"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'desc')]) wire:click="sort('{{ $sortOption['column'] }}', 'desc')">
                                    {{ $sortOption['label'] }} ({{ __('sestupně') }})
                                    <i class="float-end bi-sort-{{ $sortOption['type'] }}-down-alt"></i>
                                </a>
                            </li>
                            @if (!$loop->last)
                                <li><hr class="dropdown-divider"></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    
    <form class="filters container-sm mb-2">
        <div class="row gx-4 justify-content-center">
            <div class="col-6">
                <div class="input-group">
                    <div class="input-group-text">{{ __('Kategorie') }}</div>
                    <select id="quickFilterCategories" class="form-select select2" wire:model.live="filterCategories" data-placeholder="{{ __('Zvolte kategorii varhan') }}" multiple aria-label="Filtr kategorie">
                        @foreach (OrganCategory::getCategoryGroups() as $group => $categories)
                            <optgroup label="{{ __(OrganCategory::getGroupName($group)) }}">
                                @foreach ($categories as $category)
                                    <option title="{{ __($category->getDescription()) }}" value="{{ $category->value }}">{{ __($category->getName()) }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <input id="quickFilterFavorite" type="checkbox" class="btn-check" wire:model.live="filterFavorite" autocomplete="off">
                <label for="quickFilterFavorite" class="btn btn-outline-danger rounded-pill" for="btn-check" data-bs-toggle="tooltip" data-bs-title="Zobrazit jen oblíbené"><i class="bi-heart"></i> {{ $this->favoriteOrgansCount }}</label>
                &nbsp;
                <input id="quickFilterPrivate" type="checkbox" class="btn-check" wire:model.live="filterPrivate" autocomplete="off">
                <label for="quickFilterPrivate" class="btn btn-outline-warning rounded-pill" for="btn-check" data-bs-toggle="tooltip" data-bs-title="Zobrazit jen soukromé"><i class="bi-file-lock"></i> {{ $this->privateOrgansCount }}</label>
            </div>
        </div>
    </form>
    
    <div class="container d-flex mb-3">
        <div class="w-100">
        <ul class="nav nav-underline align-center justify-content-center">
            <li class="nav-item mx-1">
                <a @class(['nav-link', 'active' => $this->viewType === 'thumbnails']) href="#" @click="$wire.set('viewType', 'thumbnails')"><i class="bi-card-text"></i> {{ __('Miniatury') }}</a>
            </li>
            <li class="nav-item mx-1">
                <a @class(['nav-link', 'active' => $this->viewType === 'table']) href="#" @click="$wire.set('viewType', 'table')"><i class="bi-table"></i> {{ __('Tabulka') }}</a>
            </li>
            <li class="nav-item mx-1">
                <a class="nav-link disabled" href="#"><i class="bi-pin-map"></i> {{ __('Mapa') }}</a>
            </li>
        </ul>
        </div>
    </div>
  
    <livewire:organs-view
        :$filterCategories :$filterOrganBuilderId :$filterRegionId :$filterPrivate :$filterFavorite
        :$sortColumn :$sortDirection
        :$viewType
        lazy
    />
    
    <div class="modal fade" id="filtersModal" tabindex="-1" data-focus="false" aria-labelledby="filtersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="filtersModalLabel">Filtry</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavřít"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="filterCategories">Kategorie</label>
                        <select class="form-select select2" id="filterCategories" wire:model="filterCategories" aria-label="Filtr kategorie" data-placeholder="{{ __('Zvolte kategorii varhan') }}" multiple>
                            @foreach (OrganCategory::getCategoryGroups() as $group => $categories)
                                <optgroup label="{{ __(OrganCategory::getGroupName($group)) }}">
                                    @foreach ($categories as $category)
                                        <option title="{{ __($category->getDescription()) }}" value="{{ $category->value }}">{{ __($category->getName()) }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="filterOrganBuilderId">Varhanář</label>
                        <select id="filterOrganBuilderId" class="form-select select2" aria-label="{{ __('Filtr varhanář') }}" wire:model="filterOrganBuilderId" data-allow-clear="true" data-placeholder="{{ __('Zvolte varhanáře') }}">
                            <option></option>
                            @foreach ($this->organBuilders as $organBuilder)
                                <option value="{{ $organBuilder->id }}">
                                    {{ $organBuilder->name }}
                                    @if ($organBuilder->active_period)
                                        ({{ $organBuilder->active_period }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="filterRegion">Kraj</label>
                        <select id="filterRegion" class="form-select select2" aria-label="{{ __('Filtr kraje') }}" wire:model="filterRegionId" data-allow-clear="true" data-placeholder="{{ __('Zvolte kraj') }}">
                            <option></option>
                            @foreach ($this->regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="filterFavorite">Jen oblíbené</label>
                        <input class="form-check-input" type="checkbox" role="switch" id="filterFavorite" wire:model="filterFavorite">
                        <i class="bi-heart text-danger"></i>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="filterPrivate" wire:model="filterPrivate">
                        <label class="form-check-label" for="filterPrivate">Jen soukromé</label>
                        <i class="bi-file-lock text-warning"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="$refresh"><i class="bi-funnel"></i> Filtrovat</button>
                </div>
            </div>
        </div>
    </div>
</div>
