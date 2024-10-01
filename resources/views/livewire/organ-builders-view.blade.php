<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use App\Http\Resources\OrganBuilderCollection;
use App\Models\OrganBuilder;
use App\Repositories\OrganBuilderRepository;
use App\Traits\EntityPageView;

new class extends Component {
    
    use WithPagination, EntityPageView;

    // TODO: jako public mít radši jen id?
    #[Locked]
    public ?OrganBuilder $thumbnailOrgan = null;
    #[Locked]
    public ?OrganBuilder $editCustomCategoriesOrgan = null;

    private OrganBuilderRepository $repository;

    public function boot(OrganBuilderRepository $repository)
    {
        $this->repository = $repository;
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
            default => throw new \LogicException
        };
    }

    #[Computed]
    public function organs()
    {
        $filters = $this->getFiltersArray();
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

    private function getMapMarkerTitle(Model $entity): string
    {
        return "$entity->name ($entity->municipality)";
    }
    
}; ?>

<x-organomania.entity-page-view />
