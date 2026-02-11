<?php

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Publication;
use App\Http\Resources\PublicationCollection;
use App\Repositories\PublicationRepository;
use App\Traits\EntityPageView;

new class extends Component {

    use WithPagination, EntityPageView;

    #[Reactive]
    public $filterAll;
    #[Reactive]
    public $filterPublicationTypeId;
    #[Reactive]
    public $filterPublicationTopicId;
    #[Reactive]
    public $filterAuthorId;
    #[Reactive]
    public $filterJournal;
    #[Reactive]
    public $filterRegionId;
    #[Reactive]
    public $filterLanguage;
    #[Reactive]
    public $filterOnlineOnly;

    private PublicationRepository $repository;

    public function boot(PublicationRepository $repository)
    {
        $this->isCategorizable = false;
        $this->isLikeable = false;

        $this->categoriesRelation = null;
        $this->customCategoriesRelation = null;
        $this->exportFilename = 'publications.json';
        $this->gateUseCustomCategories = null;
        $this->gateLikeEntity = null;
        $this->showRoute = 'publications.show';
        $this->editRoute = null;
        $this->customCategoriesCountProp = null;
        $this->noResultsMessage = __('Nebyly nalezeny žádné publikace.');
        $this->likedMessage = null;
        $this->unlikedMessage = null;
        $this->bootCommon($repository);
    }

    #[Computed]
    public function filters()
    {
        $filters = $this->getFiltersArray();
        if ($this->filterAll && mb_strlen($this->filterAll) >= 3) $filters['all'] = $this->filterAll;
        if ($this->filterPublicationTypeId) $filters['publication_type_id'] = $this->filterPublicationTypeId;
        if ($this->filterPublicationTopicId) $filters['publication_topic_id'] = $this->filterPublicationTopicId;
        if ($this->filterAuthorId) $filters['author_id'] = $this->filterAuthorId;
        if ($this->filterJournal) $filters['journal'] = $this->filterJournal;
        if ($this->filterLanguage) $filters['language'] = $this->filterLanguage;
        if ($this->filterOnlineOnly) $filters['online_only'] = $this->filterOnlineOnly;
        return $filters;
    }

    #[Computed]
    public function organs()
    {
        $sorts = [$this->sortColumn => $this->sortDirection];

        $query = $this->repository->getPublicationsQuery(
            $this->filters, $sorts
        );

        if ($this->shouldPaginate) return $query->paginate($this->perPage);
        return $query->get();
    }

    #[Computed]
    public function viewComponent(): string
    {
        return match ($this->viewType) {
            'table' => 'organomania.publications-view-table',
            default => throw new \LogicException,
        };
    }

    private function getResourceCollection(Collection $data): ResourceCollection
    {
        return new PublicationCollection($data);
    }

    private function getMapMarkerTitle(Model $entity): string
    {
        throw new \LogicException;
    }

}; ?>

<x-organomania.entity-page-view />
