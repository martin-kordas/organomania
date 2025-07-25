<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Computed;
use App\Models\Like;
use App\Models\Scopes\OwnedEntityScope;
use App\Repositories\AbstractRepository;
use App\Repositories\OrganRepository;
use App\Repositories\OrganBuilderRepository;

trait EntityPageView
{
    
    #[Reactive]
    public $viewType = 'thumbnails';

    #[Reactive]
    public $sortColumn = 'importance';
    #[Reactive]
    public $sortDirection = 'desc';

    #[Reactive]
    public $perPage = 9;

    #[Reactive]
    public $filterId;
    #[Reactive]
    public $filterCategories;
    #[Reactive]
    public $filterRegionId;
    #[Reactive]
    public $filterImportance;
    #[Reactive]
    public $filterFavorite;
    #[Reactive]
    public $filterPrivate;
    #[Reactive]
    public $filterNearLatitude;
    #[Reactive]
    public $filterNearLongitude;
    #[Reactive]
    public $filterNearDistance;
    
    #[Reactive]
    public $activeFiltersCount;
    
    #[Reactive]
    public $selectedTimelineEntityType;
    #[Reactive]
    public $selectedTimelineEntityId;
    
    public $organCustomCategoriesIds;
    
    // TODO: je dobré řešení předávat to jako property?
    #[Locked]
    public $sortOptions;

    /** zda jde o zobrazení vlastní kategorie v signed routě (sdílení cizímu uživateli) */
    #[Locked]
    public $isCustomCategoryOrgans = false;
    
    private $shouldPaginate = true;
    
    private bool $isCategorizable = true;
    private bool $isLikeable = true;
    private bool $showThumbnailFooter = true;
    
    private ?int $thumbnailOrganId = null;
    
    private ?string $categoriesRelation;
    private ?string $customCategoriesRelation;
    private ?string $gateUseCustomCategories;
    private ?string $gateLikeEntity;
    private ?string $exportFilename;
    private ?string $showRoute;
    private ?string $editRoute;
    private ?string $shareModalHint = null;
    private ?string $customCategoriesRoute;
    private ?string $customCategoriesCountProp;
    private string $noResultsMessage;
    private ?string $likedMessage;
    private ?string $unlikedMessage;
    private string $thumbnailsViewComponent = 'organomania.entity-page-view-thumbnails';
    private string $mapViewComponent = 'organomania.entity-page-view-map';
    private string $timelineViewComponent = 'organomania.entity-page-view-timeline';
    private string $chartViewComponent = 'organomania.entity-page-view-chart';
    private string $mapId;
    private string $thumbnailComponent;
    private bool $useMapClusters = false;
    private bool $mapTooManyItems = false;
    
    private abstract function getResourceCollection(Collection $data): ResourceCollection;
    private abstract function viewComponent(): string;
    private abstract function getMapMarkerTitle(Model $entity): string;
    
    public function mount()
    {
        $columns = array_column($this->sortOptions, 'column');
        if (!in_array($this->sortColumn, $columns)) {
            throw new \RuntimeException;
        }
    }

    private function bootCommon(AbstractRepository $repository)
    {
        $this->repository = $repository;
        if (in_array($this->viewType, ['map', 'timeline'])) $this->setShouldPaginate(false);
    }

    private function setShouldPaginate($shouldPaginate = true)
    {
        $this->shouldPaginate = $shouldPaginate;
        unset($this->organs);
    }

    private function getColumnCurrentSortDirection($column)
    {
        foreach (['asc', 'desc'] as $direction) {
            if ($this->isCurrentSort($column, $direction)) {
                return $direction;
            }
        }
        return null;
    }

    // TODO: duplikace s komponentou organs
    private function isCurrentSort($column, $direction)
    {
        return $column === $this->sortColumn && $direction === $this->sortDirection;
    }

    // TODO: duplikace s komponentou organs
    private function getSortOption($column)
    {
        foreach ($this->sortOptions as $sortOption) {
            if ($sortOption['column'] === $column) {
                return $sortOption;
            }
        }
        throw new \RuntimeException;
    }

    public function setThumbnailOrgan($id)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->thumbnailOrganId = $id;
        $this->thumbnailOrgan = $this->organs->firstOrFail('id', $id);
        $this->dispatch('bootstrap-rendered');
    }

    public function setEditCustomCategoriesOrgan($id)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->editCustomCategoriesOrgan = $this->organs->firstOrFail('id', $id);
        $this->organCustomCategoriesIds = $this->editCustomCategoriesOrgan->{$this->customCategoriesRelation}->pluck('id')->toArray();
    }

    #[On('filtering-changed')]
    #[On('sort-changed')]
    public function resetPagination()
    {
        $this->resetPage();
        unset($this->organs);
    }
    
    public function updatedPage()
    {
        $this->dispatch('pagination-changed');
    }
    
    private function filterCategories(Builder $query, $ids)
    {
        if ($this->isCustomCategoryOrgans) {
            $query->withoutGlobalScope(OwnedEntityScope::class);
        }
        
        $categoryIds = $customIds = [];
        foreach ($ids as $id) {
            if (str_starts_with($id, 'custom-')) $customIds[] = str_replace('custom-', '', $id);
            else $categoryIds[] = $id;
        }

        // více kategorií - AND
        $query->where(function (Builder $query) use ($categoryIds, $customIds) {
            foreach ($categoryIds as $categoryId) {
                $query->whereHas(
                    $this->categoriesRelation,
                    fn (Builder $query) => $query
                        ->when(
                            $this->isCustomCategoryOrgans,
                            fn(Builder $query) => $query->withoutGlobalScope(OwnedEntityScope::class)
                        )
                        ->where('id', $categoryId)
                );
            }
            foreach ($customIds as $customId) {
                $query->whereHas(
                    $this->customCategoriesRelation,
                    fn (Builder $query) => $query
                        ->when(
                            $this->isCustomCategoryOrgans,
                            fn (Builder $query) => $query->withoutGlobalScope(OwnedEntityScope::class)
                        )
                        ->where('id', $customId)
                );
            }
        });
    }
    
    public function getFiltersArray()
    {
        $filters = [];
        if ($this->filterId) $filters['id'] = $this->filterId;
        if ($this->filterRegionId) $filters['regionId'] = $this->filterRegionId;
        if ($this->filterImportance) $filters['importance'] = $this->filterImportance;
        if ($this->filterFavorite) $filters['isFavorite'] = true;
        if ($this->filterPrivate) $filters['isPrivate'] = true;
        if ($this->filterNearLatitude) $filters['nearLatitude'] = (float)$this->filterNearLatitude;
        if ($this->filterNearLongitude) $filters['nearLongitude'] = (float)$this->filterNearLongitude;
        if ($this->filterNearDistance) $filters['nearDistance'] = (float)$this->filterNearDistance;
        
        // nastavení $filters['nearDistance']
        //  - pokud filtrujeme varhan podle vzdálenosti, obvykle je nearDistance nastavena na vysoké číslo, které zahrne všechny varhany
        //  - na mapě však chceme zobrazit jen varhany v okolí aktuální polohy (tím také opticky vynikne, kde aktuální poloha vlastně je)
        //  - nastavení nearDistance je také žádoucí pro účet Martin Kordas s velkým množstvím varhan, aby se mapa nepřetížila zobrazením všech varhan v db.
        //    (mapTooManyItems tomu nezabrání protože $this->filters nejsou prázdné)
        if (
            $this->viewType === 'map'
            && isset($filters['nearLatitude'])
            && in_array($this->repository::class, [OrganRepository::class, OrganBuilderRepository::class])
        ) {
            $filters['nearDistance'] = 25;
        }

        
        return $filters;
    }

    #[On('export-organs')]
    public function export()
    {
        return response()
            ->streamDownload(
                function () {
                    $shouldPaginate = $this->shouldPaginate;
                    $this->setShouldPaginate(false);
                    echo ($this->getResourceCollection($this->organs))->toJson();
                    $this->setShouldPaginate($shouldPaginate);
                },
                name: $this->exportFilename,
                headers: ['Content-Type' => 'application/json']
            );
    }
    
    #[Computed]
    public function organCustomCategories()
    {
        return $this->repository->getCustomCategories();
    }
    
    #[Computed]
    public function hasDistance()
    {
        return $this->organs->first()?->distance !== null;
    }
    
    public function saveOrganCustomCategories()
    {
        Gate::authorize($this->gateUseCustomCategories);
        if (!$this->editCustomCategoriesOrgan) throw new \RuntimeException;
        $this->editCustomCategoriesOrgan
            ->{$this->customCategoriesRelation}()
            ->sync($this->organCustomCategoriesIds);
    }
    
    public function placeholder()
    {
        return view('components.organomania.spinner');
    }

    private function getShareUrl(Model $entity)
    {
        // nelze použít OwnedEntity::getShareUrl(), protože ne všechny $entity jsou OwnedEntity
        if (isset($entity->user_id)) $relativeUrl = URL::signedRoute($this->showRoute, $entity->slug, absolute: false);
        else $relativeUrl = route($this->showRoute, $entity->slug, absolute: false);
        return url($relativeUrl);
    }

    private function getViewUrl(Model $entity)
    {
        // nelze použít OwnedEntity::getViewUrl(), protože ne všechny $entity jsou OwnedEntity
        if (!Gate::allows('view', $entity)) $relativeUrl = URL::signedRoute($this->showRoute, $entity->slug, absolute: false);
        else $relativeUrl = route($this->showRoute, $entity->slug, absolute: false);
        return url($relativeUrl);
    }

    public function rendered()
    {
        // nutné, protože komponenta je lazy
        $this->dispatch("bootstrap-rendered");
        $this->dispatch("select2-rendered");
    }

    public function likeToggle($organId)
    {
        $organ = $this->organs->firstWhere('id', $organId);
        if (!$organ) throw new \RuntimeException;
        Gate::authorize($this->gateLikeEntity, $organ);

        if ($organ->my_likes_count <= 0) {
            $like = new Like(['user_id' => Auth::id()]);
            $organ->likes()->save($like);
            $diff = 1;
            $this->js('showToast("likedToast")');
        }
        else {
            $like = $organ->likes()->where('user_id', Auth::id())->first();
            if ($like) $like->delete();
            $diff = -1;
            $this->js('showToast("unlikedToast")');
        }
        unset($this->organs);
        $this->dispatch('organ-like-updated', $diff);
    }

    private function isOrganLiked($organ)
    {
        return $organ->my_likes_count > 0;
    }
    
    private function getMapMarkerLightness(Model $entity)
    {
        return 56;      // odpovídá výchozí barvě Google map
    }
    
    public function isFilterNearCenter(Model $entity)
    {
        return 
            $this->filterNearLatitude
            && $this->filterNearLongitude
            && $this->filterNearLatitude === $entity->latitude
            && $this->filterNearLongitude === $entity->longitude;
    }
    
    // stanovujeme minimální jas barvy pozadí markeru, aby na něm šel vidět černý text
    private function getMaxMarkerLightnessWithMinBoundary($lightness, $minLightness = 45)
    {
        return round($minLightness + (100 - $minLightness) * $lightness / 100);
    }
    
    private function getMapMarkerLabel()
    {
        return '';
    }
    
}
