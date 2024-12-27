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
    public function organs()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterNameLocality && mb_strlen($this->filterNameLocality) >= 3) $filters['nameLocality'] = $this->filterNameLocality;

        // v mapě klasické řazení nehraje roli; důležitější záznamy nutné zobrazit jako první, aby je nepřekryly méně důležité
        if ($this->viewType === 'map') $sorts = ['importance' => 'asc'];
        else $sorts = [$this->sortColumn => $this->sortDirection];

        $query = $this->repository->getFestivalsQuery(
            $filters, $sorts
        );
        if ($this->id) $query->where('id', $this->id);

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
