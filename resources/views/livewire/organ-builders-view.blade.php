<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use App\Http\Resources\OrganBuilderCollection;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderTimelineItem;
use App\Repositories\OrganBuilderRepository;
use App\Traits\EntityPageView;

new class extends Component {
    
    use WithPagination, EntityPageView;

    #[Reactive]
    public $filterName;
    #[Reactive]
    public $filterMunicipality;

    // TODO: jako public mít radši jen id?
    #[Locked]
    public ?OrganBuilder $thumbnailOrgan = null;
    #[Locked]
    public ?OrganBuilder $editCustomCategoriesOrgan = null;

    private OrganBuilderRepository $repository;

    public function boot(OrganBuilderRepository $repository)
    {
        $this->categoriesRelation = 'organBuilderCategories';
        $this->customCategoriesRelation = 'organBuilderCustomCategories';
        $this->exportFilename = 'organ-builders.json';
        $this->gateUseCustomCategories = 'useOrganBuilderCustomCategories';
        $this->gateLikeEntity = 'likeOrganBuilder';
        $this->showRoute = 'organ-builders.show';
        $this->editRoute = 'organ-builders.edit';
        $this->customCategoriesRoute = 'organ-builders.organ-builder-custom-categories';
        $this->customCategoriesCountProp = 'organ_builder_custom_categories_count';
        $this->noResultsMessage = __('Nebyly nalezeni žádní varhanáři.');
        $this->likedMessage = __('Varhanář byl přidán do oblíbených.');
        $this->unlikedMessage = __('Varhanář byl odebrán z oblíbených.');
        $this->mapId = 'organomania-organ-builders-view';
        $this->thumbnailComponent = 'organomania.organ-builder-thumbnail';
        // varhanáři tvoří shluky na stejných místech, protože neznáme jejich přesné adresy v daném městě
        $this->useMapClusters = true;
        $this->bootCommon($repository);
    }

    private function getResourceCollection(Collection $data): ResourceCollection
    {
        return new OrganBuilderCollection($data);
    }

    #[Computed]
    public function viewComponent(): string
    {
        return match ($this->viewType) {
            'thumbnails' => $this->thumbnailsViewComponent,
            'table' => 'organomania.organ-builders-view-table',
            'map' => $this->mapViewComponent,
            'timeline' => $this->timelineViewComponent,
            default => throw new \LogicException
        };
    }

    #[Computed]
    public function organs()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterName) $filters['name'] = $this->filterName;
        if ($this->filterMunicipality) $filters['municipality'] = $this->filterMunicipality;

        if ($this->viewType === 'map') $sorts = ['importance' => 'asc'];
        else $sorts = [$this->sortColumn => $this->sortDirection];

        $withCount = [
            ...OrganBuilderRepository::ORGAN_BUILDERS_WITH_COUNT,
            'likes as my_likes_count' => function (Builder $query) {
                $query->where('user_id', Auth::id());
            }
        ];

        $query = $this->repository->getOrganBuildersQuery(
            $filters, $sorts,
            withCount: $withCount
        );

        if ($this->filterCategories) {
            $this->filterCategories($query, $this->filterCategories);
        }

        if ($this->shouldPaginate) return $query->paginate($this->perPage);
        return $query->get();
    }

    #[Computed]
    public function timelineItems()
    {
        $organBuilderIds = [];

        $items = $this->organs->pluck('timelineItems')->flatten()->flatMap(
            function (OrganBuilderTimelineItem $item) use (&$organBuilderIds) {
                $items = [];
                $items[] = [
                    'entityType' => 'organBuilder',
                    'entityId' => $item->organ_builder_id,
                    'public' => $item->organBuilder->isPublic(),
                    'isWorkshop' => $item->is_workshop,
                    'type' => 'range',
                    'name' => $item->name,
                    'time' => $item->active_period ?? $item->organBuilder->active_period,
                    'start' => "{$item->year_from}-01-01",
                    'end' => isset($item->year_to) ? "{$item->year_to}-01-01" : null,
                    'land' => $item->land,
                    // HACK: 'žž' kvůli zařazení varhanářů bez lokality na konec
                    'group' => $item->locality ?? "ž-{$item->land}",
                ];

                // varhany zobrazujeme na timeline jen, pokud je zobrazen jediný varhanář
                //  - jinak by nebylo jasné, kterému varhanáři varhany patří
                if ($this->filterId && !in_array($item->organ_builder_id, $organBuilderIds)) {
                    $organBuilderIds[] = $item->organ_builder_id;
                    $item->organBuilder->load([
                        'organs' => function (HasMany $query) {
                            $query->withCount('organRebuilds');
                        }
                    ]);
                    $items = [
                        ...$items,
                        ...$this->getOrgansTimelineItems($item->organBuilder)
                    ];
                }
                return $items;
            }
        );

        foreach ([1600, 1800, 2000] as $year) {
            $endYear = $year + 100;
            $items[] = [
                'type' => 'background',
                'entityType' => null,
                'start' => "$year-01-01",
                'end' => "$endYear-01-01",
            ];
        }

        return $items;
    }

    private function getOrgansTimelineItems(OrganBuilder $organBuilder)
    {
        $items = [];
        foreach ($organBuilder->organs as $organ) {
            if (isset($organ->year_built)) {
                $time = $organ->year_built;
                if ($organ->organ_rebuilds_count <= 0 && ($sizeInfo = $organ->getSizeInfo()))
                    $time .= ", $sizeInfo";

                $items[] = [
                    'entityType' => 'organ',
                    'entityId' => $organ->id,
                    'type' => 'point',
                    'name' => "{$organ->municipality}, {$organ->place}",
                    'time' => $time,
                    'start' => "{$organ->year_built}-01-01",
                    'url' => route('organs.show', $organ->slug),
                ];
            }
        }
        foreach ($organBuilder->organRebuilds as $rebuild) {
            $items[] = [
                'entityType' => 'organ',
                'entityId' => $rebuild->organ->id,
                'type' => 'point',
                'name' => "{$rebuild->organ->municipality}, {$rebuild->organ->place}",
                'time' => $rebuild->year_built,
                'start' => "{$rebuild->year_built}-01-01",
                'url' => route('organs.show', $rebuild->organ->slug),
            ];
        }
        return $items;
    }

    // členění varhanářu dle zemí (např. Čechy) a center (např. Loket)
    #[Computed]
    public function timelineGroups()
    {
        if ($this->filterId) return null;

        $groups = $this->timelineItems
            ->pluck('group')
            ->filter()
            ->unique()
            ->values()
            ->map(
                fn ($group) => ['name' => $group, 'orderValue' => $group]
            );

        // ['Čechy' => ['Praha', 'Loket', ...], ...]
        $lands = collect();
        // definujeme s předstihem kvůli uchování pořadí
        foreach (['Čechy', 'Morava', 'Slezsko'] as $land)
            $lands[$land] = collect();

        $timelineItemsOrganBuilders = $this->timelineItems->filter(
            fn ($item) => $item['entityType'] === 'organBuilder'
        );
        foreach ($timelineItemsOrganBuilders as $item) {
            $lands[$item['land']] ??= collect();
            if (!$lands[$item['land']]->contains($item['group']))
                $lands[$item['land']][] = $item['group'];
        }
        $lands = $lands->filter(
            fn ($groups) => $groups->isNotEmpty()
        );

        $superGroups = $lands->map(function ($landGroups, $land) {
            static $order = 1;
            return [
                'name' => $land,
                'orderValue' => $order++,
                'nestedGroups' => $landGroups,
            ];
        });

        $groups = $groups->merge($superGroups);
        return $groups;
    }

    #[Computed]
    public function timelineMarkers()
    {
        $years = [
            1620 => __('Období baroka bývá označováno za zlatý věk varhanářství. Posílení významu církve dává vzniknout nejen řadě sakrálních staveb, ale i množství varhan. Jejich stavbě se věnují špičkové, často vícegenerační varhanářské dílny.'),
            1800 => __('Od 19. století nastává pozvolný úpadek barokního varhanářství, který souvisí s josefínskými reformami (rušení klášterů) a ekonomickými problémy vzešlými z napoleonských válek. Ve varhanních dispozicích se začíná uplatňovat větší množství hlubokých hlasů, což je typické pro romantické varhanářství.'),
            1860 => __('S rozvojem průmyslu vznikají velké podniky provozující tovární výrobu varhan. Postupně se prosazuje kuželková vzdušnice a pneumatická traktura. Varhanní dispozice jsou ovlivněny ceciliánskou reformou, která upřednostňuje rejstříky v nízkých polohách.'),
            1945 => __('V období komunismu funguje pouze několik varhanářských firem. Dispozice varhan se pod vlivem varhanního hnutí navrací k baroknímu zvukovému ideálu a staví se varhany tzv. univerzálního typu. Kromě pneumatické traktury se uplatňuje i traktura elektrická a mechanická.'),
            1990 => __('Po pádu komunismu vzniká řada menších varhanářských dílen. Kromě univerzálních se staví i stylově vyhraněné nástroje. Probíhá restaurování cenných historických nástrojů.')
        ];

        $markers = [];
        foreach ($years as $year => $description) {
            $markers[] = [
                'name' => $year,
                'date' => "$year-01-01",
                'description' => $description,
            ];
        }
        return $markers;
    }

    // určuje časový úsek zobrazený defaultně na časové ose
    //  - úsek omezujeme, je-li vyfiltrován konkrétní varhanář a jeho varhany
    #[Computed]
    public function timelineViewRange()
    {
        if ($this->filterId && $this->timelineItems->isNotEmpty()) {
            $items = $this->timelineItems->filter(
                fn ($item) => in_array($item['entityType'], ['organ', 'organBuilder'])
            );

            $oldest = $items
                ->pluck('start')
                ->sort()
                ->first();
            $newest = $items
                ->map(
                    fn ($item) => $item['end'] ?? $item['start']
                )
                ->sortDesc()
                ->first();

            $start = (new Carbon($oldest))
                ->subYears(100)
                ->format('Y-m-d');
            $end = (new Carbon($newest))
                ->addYears(100)
                ->format('Y-m-d');
            return [$start, $end];
        }
    }

    #[Computed]
    public function timelineStep()
    {
        return $this->filterId ? 25 : 25;
    }

    private function getMapMarkerTitle(Model $entity): string
    {
        return "$entity->name ($entity->municipality)";
    }
    
}; ?>

<x-organomania.entity-page-view />
