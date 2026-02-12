<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use App\Interfaces\Category;
use App\Models\OrganBuilder;
use App\Models\Festival;
use App\Models\OrganCategory as OrganCategoryModel;
use App\Enums\OrganCategory;
use App\Models\CustomCategory;
use App\Repositories\FestivalRepository;
use App\Traits\EntityPage;
use App\Helpers;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use EntityPage;

    #[Url(keep: true)]
    public $filterNameLocality;
    #[Url(keep: true)]
    public $filterMonth;

    #[Url(keep: true)]
    public $id;

    private FestivalRepository $repository;
    private Festival $model;

    const SORT_OPTIONS = [
        ['column' => 'name', 'label' => 'Název', 'type' => 'alpha'],
        ['column' => 'locality', 'label' => 'Lokalita', 'type' => 'alpha'],
        ['column' => 'distance', 'label' => 'Vzdálenost', 'shortLabel' => 'Vzdál.', 'type' => 'numeric'],
        ['column' => 'starting_month', 'label' => 'Období', 'type' => 'numeric'],
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric'],
        ['column' => 'views', 'label' => 'Počet zobrazení', 'type' => 'numeric'],
    ];

    public function boot(FestivalRepository $repository, Festival $model)
    {
        $this->viewTypes[] = 'timeline';
        $this->bootCommon();

        $this->repository = $repository;
        $this->model = $model;

        $this->isLikeable = false;
        $this->isEditable = false;
        $this->isExportable = false;
        $this->isCategorizable = false;

        $this->createRoute = null;
        $this->exportRoute = null;
        $this->customCategoriesRoute = null;
        $this->customCategoryRoute = null;
        $this->categorySelectPlaceholder = null;
        $this->gateUseCustomCategories = null;
        $this->maxImportance = 3;
        $this->gateLike = null;
        $this->entityPageViewComponent = 'festivals-view';
        $this->entityClass = Festival::class;
        $this->entityNamePluralAkuzativ = __('festivaly');
        $this->filtersModalAutofocus = '#filterNameLocality';
        $this->filters[] = 'filterNameLocality';
        $this->filters[] = 'filterMonth';
        $this->title = __('Festivaly varhanní hudby');
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('festivals');

        if (!request()->query('sortColumn')) $this->sortColumn = 'starting_month';
        if (!request()->query('sortDirection')) $this->sortDirection = 'asc';
        $this->mountCommon();
    }

    private function getCategoryEnum()
    {
        throw new \LogicException;
    }

    private function getOrganCategoryOrganCount(Category $category)
    {
        throw new \LogicException;
    }

    private function getOrganCustomCategoryOrganCount(CustomCategory $category)
    {
        throw new \LogicException;
    }

}; ?>

<x-organomania.entity-page :metaDescription="__('Objevte prestižní hudební festivaly a koncerty varhanní hudby v celé ČR. Navštivte sólové varhanní recitály i vokálně-instrumentální koncerty špičkových umělců.')" />
