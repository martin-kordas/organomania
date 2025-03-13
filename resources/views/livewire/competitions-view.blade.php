<?php

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
use App\Models\Competition;
use App\Models\Scopes\OwnedEntityScope;
use App\Http\Resources\OrganCollection;
use App\Repositories\CompetitionRepository;
use App\Traits\EntityPageView;

// TODO: sloučit s komponentou competitions?
new class extends Component {
    
    use WithPagination, EntityPageView;
    
    #[Reactive]
    public $filterNameLocality;

    #[Reactive]
    public $id;
    
    // TODO: jako public mít radši jen id?
    #[Locked]
    public ?Competition $thumbnailOrgan = null;
    #[Locked]
    public ?Competition $editCustomCategoriesOrgan = null;

    private CompetitionRepository $repository;
    
    public function boot(CompetitionRepository $repository)
    {
        $this->isCategorizable = false;
        $this->isLikeable = false;
        $this->showThumbnailFooter = true;

        $this->categoriesRelation = null;
        $this->customCategoriesRelation = null;
        $this->exportFilename = null;
        $this->gateUseCustomCategories = null;
        $this->gateLikeEntity = null;
        $this->showRoute = 'competitions.show';
        $this->editRoute = null;
        $this->customCategoriesCountProp = null;
        $this->noResultsMessage = __('Nebyly nalezeny žádné soutěže.');
        $this->likedMessage = null;
        $this->unlikedMessage = null;
        $this->thumbnailComponent = 'organomania.competition-thumbnail';
        $this->mapId = 'organomania-competitions-view';
        $this->bootCommon($repository);
    }

    #[Computed]
    public function filters()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterNameLocality && mb_strlen($this->filterNameLocality) >= 3) $filters['nameLocality'] = $this->filterNameLocality;
        return $filters;
    }

    #[Computed]
    public function organs()
    {
        $sorts = [$this->sortColumn => $this->sortDirection];

        $query = $this->repository->getCompetitionsQuery(
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
            'table' => 'organomania.competitions-view-table',
            'map' => $this->mapViewComponent,
            default => throw new \LogicException
        };
    }

    private function getMapMarkerTitle(Model $entity): string
    {
        $title = $entity->name;
        if (isset($entity->locality)) $title .= " ({$entity->locality})";
        return $title;
    }
    
}; ?>

<x-organomania.entity-page-view />
