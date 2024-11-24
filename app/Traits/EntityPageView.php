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
use App\Repositories\AbstractRepository;
use App\Models\Scopes\OwnedEntityScope;

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
    public $activeFiltersCount;
    
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
    
    private ?string $categoriesRelation;
    private ?string $customCategoriesRelation;
    private ?string $gateUseCustomCategories;
    private ?string $gateLikeEntity;
    private ?string $exportFilename;
    private ?string $showRoute;
    private ?string $editRoute;
    private ?string $customCategoriesRoute;
    private ?string $customCategoriesCountProp;
    private string $noResultsMessage;
    private ?string $likedMessage;
    private ?string $unlikedMessage;
    private string $thumbnailsViewComponent = 'organomania.entity-page-view-thumbnails';
    private string $mapViewComponent = 'organomania.entity-page-view-map';
    private string $mapId;
    private string $thumbnailComponent;
    
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
        if ($this->viewType === 'map') $this->setShouldPaginate(false);
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

        $query->where(function (Builder $query) use ($categoryIds, $customIds) {
            if (!empty($categoryIds)) $query->orWhereHas(
                $this->categoriesRelation,
                fn(Builder $query) => $query
                    ->when(
                        $this->isCustomCategoryOrgans,
                        fn(Builder $query) => $query->withoutGlobalScope(OwnedEntityScope::class)
                    )
                    ->whereIn('id', $categoryIds)
            );
            if (!empty($customIds)) $query->orWhereHas(
                $this->customCategoriesRelation,
                fn(Builder $query) => $query
                    ->when(
                        $this->isCustomCategoryOrgans,
                        fn(Builder $query) => $query->withoutGlobalScope(OwnedEntityScope::class)
                    )
                    ->whereIn('id', $customIds)
            );
        });
    }
    
    public function getFiltersArray()
    {
        $filters = [];
        if ($this->filterRegionId) $filters['regionId'] = $this->filterRegionId;
        if ($this->filterImportance) $filters['importance'] = $this->filterImportance;
        if ($this->filterFavorite) $filters['isFavorite'] = true;
        if ($this->filterPrivate) $filters['isPrivate'] = true;
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
        $fn = isset($entity->user_id) ? URL::signedRoute(...) : route(...);
        $relativeUrl = $fn($this->showRoute, $entity->id, absolute: false);
        return url($relativeUrl);
    }

    private function getViewUrl(Model $entity)
    {
        $fn = !Gate::allows('view', $entity) ? URL::signedRoute(...) : route(...);
        return $fn($this->showRoute, $entity->slug);
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
    
}
