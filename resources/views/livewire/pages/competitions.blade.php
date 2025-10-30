<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use App\Interfaces\Category;
use App\Models\Competition;
use App\Models\CustomCategory;
use App\Repositories\CompetitionRepository;
use App\Traits\EntityPage;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use EntityPage;

    #[Url(keep: true)]
    public $filterNameLocality;

    #[Url(keep: true)]
    public $id;

    private CompetitionRepository $repository;
    private Competition $model;

    const SORT_OPTIONS = [
        ['column' => 'name', 'label' => 'Název', 'type' => 'alpha'],
        ['column' => 'locality', 'label' => 'Lokalita', 'type' => 'alpha'],
        ['column' => 'next_year', 'label' => 'Příští ročník', 'type' => 'numeric'],
        ['column' => 'max_age', 'label' => 'Max. věk', 'type' => 'numeric'],
        ['column' => 'participation_fee', 'label' => 'Poplatek', 'type' => 'numeric'],
        ['column' => 'first_prize', 'label' => '1. cena', 'type' => 'numeric'],
        ['column' => 'views', 'label' => 'Počet zobrazení', 'type' => 'numeric'],
    ];

    public function boot(CompetitionRepository $repository, Competition $model)
    {
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
        $this->gateLike = null;
        $this->entityPageViewComponent = 'competitions-view';
        $this->entityClass = Competition::class;
        $this->entityNamePluralAkuzativ = __('soutěže');
        $this->filtersModalAutofocus = '#filterNameLocality';
        $this->filters[] = 'filterNameLocality';
        $this->title = __('Soutěže varhaníků');
    }

    public function mount()
    {
        $this->sortColumn = 'next_year';
        $this->sortDirection = 'desc';
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

<x-organomania.entity-page :metaDescription="__('Prohlédněte si připravované i proběhlé varhanní soutěže v České republice. Zjistěte informace o počtu kol, účastnických poplatcích a cenách pro vítěze.')" />