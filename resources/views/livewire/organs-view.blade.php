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
use App\Enums\OrganCategory;
use App\Models\Organ;
use App\Models\OrganLike;
use App\Models\OrganCustomCategory as OrganCustomCategoryModel;
use App\Models\Scopes\OwnedEntityScope;
use App\Models\User;
use App\Helpers;
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
    public $filterManualsCount;
    #[Reactive]
    public $filterOrganBuilderId;
    #[Reactive]
    public $filterpreservedCase;
    #[Reactive]
    public $filterpreservedOrgan;
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
        $this->bootCommon($repository);

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
        $this->mapTooManyItems
            = Auth::user()?->id === User::USER_ID_MARTIN_KORDAS
            && $this->viewType === 'map'
            && empty($this->filters)
            && empty($this->filterCategories);
    }

    #[Computed]
    public function filters()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterLocality) $filters['locality'] = $this->filterLocality;
        if ($this->filterDisposition) $filters['disposition'] = $this->filterDisposition;
        if ($this->filterManualsCount) $filters['manualsCount'] = $this->filterManualsCount;
        if ($this->filterOrganBuilderId) $filters['organBuilderId'] = $this->filterOrganBuilderId;
        if ($this->filterpreservedCase) $filters['preservedCase'] = $this->filterpreservedCase;
        if ($this->filterpreservedOrgan) $filters['preservedOrgan'] = $this->filterpreservedOrgan;
        if ($this->filterConcertHall) $filters['concertHall'] = $this->filterConcertHall;
        if ($this->filterForeignOrganBuilder) $filters['foreignOrganBuilder'] = $this->filterForeignOrganBuilder;
        if ($this->filterHasDisposition) $filters['hasDisposition'] = $this->filterHasDisposition;
        return $filters;
    }

    #[Computed]
    public function organs()
    {
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
            $this->filters, $sorts,
            with: $with, withCount: $withCount
        );

        if ($this->filterCategories) {
            $this->filterCategories($query, $this->filterCategories);
        }
        // varhany z knihy Barokní varhanářství načítáme jen, když je zvolena daná kategorie
        //  - filtr podle sloupce baroque namísto kategorie je kvůli optimalizaci
        if (!$this->filterCategories || !in_array(OrganCategory::FromBookBaroqueOrganBuilding->value, $this->filterCategories)) {
            $query->where('baroque', '0');
        }
        // optimalizace: při zobrazení thumbnailu stačí načíst jen dané varhany (celá mapa se nepřekresluje)
        if (isset($this->thumbnailOrganId)) $query->where('id', $this->thumbnailOrganId);

        if ($this->mapTooManyItems) {
            $query->whereRaw('FALSE');
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

    private function getMapMarkerLightness(Organ $entity)
    {
        $lightness = $entity->getRelativeYearBuilt();
        return $this->getMaxMarkerLightnessWithMinBoundary($lightness);
    }

    private function getMapMarkerLabel(Organ $entity)
    {
        return isset($entity->manuals_count) ? Helpers::formatRomanNumeral($entity->manuals_count) : '?';
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

                $organBuilderName = $organ->organBuilder?->name ?? __('neznámý varhanář');
                $organBuilderShortName = $organ->organBuilder?->shortName ?? __('neznámý');
                $categories[] = [$organ->municipality, $organ->place, $organ->shortPlace, $organBuilderName, $organBuilderShortName, $organ->year_built];
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
