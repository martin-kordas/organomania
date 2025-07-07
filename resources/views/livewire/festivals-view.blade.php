<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Locked;
use App\Enums\Region;
use App\Models\Festival;
use App\Models\Scopes\OwnedEntityScope;
use App\Http\Resources\OrganCollection;
use App\Repositories\FestivalRepository;
use App\Traits\EntityPageView;

// TODO: sloučit s komponentou festivals?
new class extends Component {
    
    use WithPagination, EntityPageView;
    
    #[Reactive]
    public $filterNameLocality;
    #[Reactive]
    public $filterMonth;

    #[Reactive]
    public $id;
    
    // TODO: jako public mít radši jen id?
    #[Locked]
    public ?Festival $thumbnailOrgan = null;
    #[Locked]
    public ?Festival $editCustomCategoriesOrgan = null;

    private FestivalRepository $repository;
    
    public function boot(FestivalRepository $repository)
    {
        $this->isCategorizable = false;
        $this->isLikeable = false;
        $this->showThumbnailFooter = true;

        $this->categoriesRelation = null;
        $this->customCategoriesRelation = null;
        $this->exportFilename = null;
        $this->gateUseCustomCategories = null;
        $this->gateLikeEntity = null;
        $this->showRoute = 'festivals.show';
        $this->editRoute = null;
        $this->customCategoriesCountProp = null;
        $this->noResultsMessage = __('Nebyly nalezeny žádné festivaly.');
        $this->likedMessage = null;
        $this->unlikedMessage = null;
        $this->thumbnailComponent = 'organomania.festival-thumbnail';
        $this->mapId = 'organomania-festivals-view';
        $this->bootCommon($repository);
    }

    #[Computed]
    public function filters()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterNameLocality && mb_strlen($this->filterNameLocality) >= 3) $filters['nameLocality'] = $this->filterNameLocality;
        if ($this->filterMonth) $filters['month'] = $this->filterMonth;
        return $filters;
    }

    #[Computed]
    public function organs()
    {
        // v mapě klasické řazení nehraje roli; důležitější záznamy nutné zobrazit jako první, aby je nepřekryly méně důležité
        if ($this->viewType === 'map') $sorts = ['importance' => 'asc'];
        else $sorts = [$this->sortColumn => $this->sortDirection];

        $query = $this->repository->getFestivalsQuery(
            $this->filters, $sorts
        );
        if ($this->id) $query->where('id', $this->id);

        // optimalizace: při zobrazení thumbnailu stačí načíst jen dané varhany (celá mapa se nepřekresluje)
        if (isset($this->thumbnailOrganId)) $query->where('id', $this->thumbnailOrganId);

        if ($this->shouldPaginate) return $query->paginate($this->perPage);
        return $query->get();
    }

    private function getResourceCollection(Collection $data): ResourceCollection
    {
        throw new \LogicException;
    }
    
    #[Computed]
    public function viewComponent(): string
    {
        return match ($this->viewType) {
            'thumbnails' => $this->thumbnailsViewComponent,
            'table' => 'organomania.festivals-view-table',
            'map' => $this->mapViewComponent,
            'timeline' => $this->timelineViewComponent,
            default => throw new \LogicException
        };
    }

    private function getMapMarkerTitle(Model $entity): string
    {
        $title = $entity->name;
        if (isset($entity->locality)) $title .= " ({$entity->locality})";
        return $title;
    }
    
    #[Computed]
    public function timelineItems()
    {
        $items = $this->organs->flatMap(function (Festival $festival) {
            $year = now()->year;
            $yearOverflow = isset($festival->starting_month, $festival->ending_month) && $festival->starting_month > $festival->ending_month;
            $startDate = Carbon::createFromDate($year, $festival->starting_month ?? 1, 1);
            if ($yearOverflow) $endDate = Carbon::createFromDate($year, 12, 31);
            else $endDate = Carbon::createFromDate($year, $festival->ending_month ?? 12)->endOfMonth();

            $details = null;
            if ($festival->region_id !== Region::Praha->value) $details = $festival->locality;

            $item = [
                'entityType' => 'festival',
                'entityId' => $festival->id,
                'type' => 'range',
                'name' => $festival->name,
                'details' => $details,
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'group' => $festival->region?->name ?? __('celá ČR'),
            ];
            
            $items = [$item];

            if ($yearOverflow) {
                $startDate = Carbon::createFromDate($year, 1, 1);
                $endDate = Carbon::createFromDate($year, $festival->ending_month)->endOfMonth();

                $item['start'] = $startDate->format('Y-m-d');
                $item['end'] = $endDate->format('Y-m-d');
                $items[] = $item;
            }
            return $items;
        });

        $items[] = [
            'type' => 'background',
            'entityType' => null,
            'start' => now()->startOfMonth()->format('Y-m-d'),
            'end' => now()->endOfMonth()->format('Y-m-d'),
        ];

        return $items;
    }

    // členění varhanářu dle zemí (např. Čechy) a center (např. Loket)
    #[Computed]
    public function timelineGroups()
    {
        if ($this->filterId) return null;

        return $this->timelineItems
            ->pluck('group')
            ->filter()
            ->unique()
            ->values()
            ->map(
                fn ($group) => [
                    'name' => $group,
                    'orderValue' => $group === __('celá ČR') ? 'ž' : $group
                ]
            );
    }

    #[Computed]
    public function timelineMarkers()
    {
        return [];
    }

    #[Computed]
    public function timelineScale()
    {
        return 'month';
    }

    // určuje rozmezí časové osy
    #[Computed]
    public function timelineRange()
    {
        $year = now()->year;
        $start = Carbon::createFromDate($year, 1, 1);
        $end = Carbon::createFromDate($year, 12, 31);
        return [$start, $end];
    }

    // určuje časový úsek zobrazený defaultně na časové ose
    //  - úsek omezujeme, je-li vyfiltrován konkrétní varhanář a jeho varhany
    #[Computed]
    public function timelineViewRange()
    {
        return null;
    }

    #[Computed]
    public function timelineStep()
    {
        return null;
    }

}; ?>

<x-organomania.entity-page-view />
