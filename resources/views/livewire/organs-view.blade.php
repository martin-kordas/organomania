<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Organ;
use App\Models\OrganLike;
use App\Models\OrganCustomCategory as OrganCustomCategoryModel;
use App\Models\Scopes\OwnedEntityScope;
use App\Http\Resources\OrganCollection;
use App\Repositories\OrganRepository;
use App\Traits\EntityPageView;

// TODO: sloučit s komponentou organs-builders?
new class extends Component {
    
    use WithPagination, EntityPageView;

    #[Reactive]
    public $filterLocality;
    #[Reactive]
    public $filterDisposition;
    #[Reactive]
    public $filterOrganBuilderId;
    #[Reactive]
    public $filterConcertHall;
    #[Reactive]
    public $filterForeignOrganBuilder;
    #[Reactive]
    public $filterHasDisposition;

    // TODO: jako public mít radši jen id?
    #[Locked]
    public ?Organ $thumbnailOrgan = null;
    #[Locked]
    public ?Organ $editCustomCategoriesOrgan = null;

    private OrganRepository $repository;
    
    public function boot(OrganRepository $repository)
    {
        $this->categoriesRelation = 'organCategories';
        $this->customCategoriesRelation = 'organCustomCategories';
        $this->exportFilename = 'organs.json';
        $this->gateUseCustomCategories = 'useOrganCustomCategories';
        $this->gateLikeEntity = 'likeOrgan';
        $this->showRoute = 'organs.show';
        $this->editRoute = 'organs.edit';
        $this->shareModalHint = __('Sdílením varhan sdílíte i jejich varhanáře a dispozice.');
        $this->customCategoriesRoute = 'organs.organ-custom-categories';
        $this->customCategoriesCountProp = 'organ_custom_categories_count';
        $this->noResultsMessage = __('Nebyly nalezeny žádné varhany.');
        $this->likedMessage = __('Varhany byly přidány do oblíbených.');
        $this->unlikedMessage = __('Varhany byly odebrány z oblíbených.');
        $this->mapId = 'organomania-organs-view';
        $this->thumbnailComponent = 'organomania.organ-thumbnail';
        $this->bootCommon($repository);
    }

    #[Computed]
    public function organs()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterLocality) $filters['locality'] = $this->filterLocality;
        if ($this->filterDisposition) $filters['disposition'] = $this->filterDisposition;
        if ($this->filterOrganBuilderId) $filters['organBuilderId'] = $this->filterOrganBuilderId;
        if ($this->filterConcertHall) $filters['concertHall'] = $this->filterConcertHall;
        if ($this->filterForeignOrganBuilder) $filters['foreignOrganBuilder'] = $this->filterForeignOrganBuilder;
        if ($this->filterHasDisposition) $filters['hasDisposition'] = $this->filterHasDisposition;

        if ($this->viewType === 'map') $sorts = ['importance' => 'asc'];
        else $sorts = [$this->sortColumn => $this->sortDirection];

        $with = OrganRepository::ORGANS_WITH;
        if ($this->isCustomCategoryOrgans) {
            $with = array_diff($with, ['organBuilder']);
            $with['organBuilder'] = function (BelongsTo $query) {
                $query->withoutGlobalScope(OwnedEntityScope::class);
            };
        }

        $withCount = [
            ...OrganRepository::ORGANS_WITH_COUNT,
            'likes as my_likes_count' => function (Builder $query) {
                $query->where('user_id', Auth::id());
            }
        ];

        $query = $this->repository->getOrgansQuery(
            $filters, $sorts,
            with: $with, withCount: $withCount
        );

        if ($this->filterCategories) {
            $this->filterCategories($query, $this->filterCategories);
        }

        if ($this->shouldPaginate) return $query->paginate($this->perPage);
        return $query->get();
    }

    private function getResourceCollection(Collection $data): ResourceCollection
    {
        return new OrganCollection($data);
    }
    
    #[Computed]
    public function viewComponent(): string
    {
        return match ($this->viewType) {
            'thumbnails' => $this->thumbnailsViewComponent,
            'table' => 'organomania.organs-view-table',
            'map' => $this->mapViewComponent,
            'chart' => $this->chartViewComponent,
            default => throw new \LogicException
        };
    }

    private function getMapMarkerTitle(Model $entity): string
    {
        return "{$entity->municipality}, {$entity->place}";
    }

    #[Computed]
    public function chartData()
    {
        $series = $categories = $organData = [];
        foreach (['stopsCount', 'originalStopsCount', 'manualsCount', 'originalManualsCount'] as $key) {
            $series[$key] = [];
        }

        $this->organs->each(function (Organ $organ) use (&$series, &$categories, &$organData) {
            if (isset($organ->stops_count)) {
                $series['stopsCount'][] = $organ->stops_count;
                $series['originalStopsCount'][] = $organ->original_stops_count ?? $organ->stops_count;
                $series['manualsCount'][] = $organ->manuals_count;
                $series['originalManualsCount'][] = $organ->original_manuals_count ?? $organ->manuals_count;

                $organBuilderName = $organ->organBuilder?->shortName ?? __('neznámý varhanář');
                $categories[] = [$organ->municipality, $organ->place, $organBuilderName, $organ->year_built];
                $organData[] = [
                    'id' => $organ->id,
                    'sizeInfo' => $organ->getSizeInfo(),
                    'originalSizeInfo' => $organ->getSizeInfo(original: true),
                ];
            }
        });

        return compact('series', 'categories', 'organData');
    }
    
}; ?>

<x-organomania.entity-page-view />
