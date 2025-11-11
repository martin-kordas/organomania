<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\Attributes\On; 
use Livewire\Attributes\Url;
use Livewire\Attributes\Session;
use Livewire\Attributes\Locked;
use App\Interfaces\Category;
use App\Models\Region;
use App\Models\OrganBuilder;
use App\Models\Organ;
use App\Models\Category as CategoryModel;
use App\Models\OrganCategory as OrganCategoryModel;
use App\Enums\OrganCategory;
use App\Models\CustomCategory;
use App\Repositories\OrganRepository;
use App\Traits\EntityPage;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use EntityPage;

    #[Url(keep: true)]
    public $filterLocality;
    #[Url(keep: true)]
    public $filterDisposition;
    #[Url(keep: true)]
    public $filterManualsCount;
    #[Url(keep: true)]
    public $filterOrganBuilderId;
    #[Url(keep: true)]
    public $filterCaseOrganBuilderId;
    #[Url(keep: true)]
    public $filterpreservedCase;
    #[Url(keep: true)]
    public $filterpreservedOrgan;
    #[Url(keep: true)]
    public $filterConcertHall;
    #[Url(keep: true)]
    public $filterForeignOrganBuilder;
    #[Url(keep: true)]
    public $filterHasDisposition;

    public $municipality;

    private OrganRepository $repository;
    private Organ $model;
    private OrganCategoryModel $categoryModel;

    private bool $showFilterHasDisposition = true;

    const SORT_OPTIONS = [
        ['column' => 'municipality', 'label' => 'Obec', 'type' => 'alpha'],
        ['column' => 'distance', 'label' => 'Vzdálenost', 'shortLabel' => 'Vzdál.', 'type' => 'numeric'],
        ['column' => 'organ_builder', 'label' => 'Varhanář', 'type' => 'numeric'],
        ['column' => 'year_built', 'label' => 'Rok', 'type' => 'numeric'],
        ['column' => 'manuals_count', 'label' => 'Počet manuálů', 'shortLabel' => 'Man.', 'type' => 'numeric'],
        ['column' => 'stops_count', 'label' => 'Počet rejstříků', 'shortLabel' => 'Rejstříky', 'type' => 'numeric'],
        ['column' => 'original_stops_count', 'label' => 'Rejstříků původně', 'shortLabel' => 'Rejstříků původně', 'type' => 'numeric'],
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric'],
        ['column' => 'views', 'label' => 'Počet zobrazení', 'type' => 'numeric'],
    ];

    public function boot(OrganRepository $repository, Organ $model, OrganCategoryModel $categoryModel)
    {
        $this->viewTypes[] = 'chart';
        $this->bootCommon();

        $this->repository = $repository;
        $this->model = $model;
        $this->categoryModel = $categoryModel;
        $this->hasMunicipalityInfo = true;

        $this->createRoute = 'organs.create';
        $this->exportRoute = 'organs.export';
        $this->customCategoriesRoute = 'organs.organ-custom-categories';
        $this->customCategoryRoute = 'organs.custom-category-organs.index';
        $this->categorySelectPlaceholder = __('Zvolte kategorii varhan');
        $this->customCategoriesTitle = __('Spravovat vlastní kategorie varhan');
        $this->gateUseCustomCategories = 'useOrganCustomCategories';
        $this->categoryClass = OrganCategory::class;
        $this->gateLike = 'likeOrgans';
        $this->entityPageViewComponent = 'organs-view';
        $this->entityClass = Organ::class;
        $this->entityNamePluralNominativ = __('varhany');
        $this->entityNamePluralAkuzativ = __('varhany');
        $this->filtersModalScrollable = true;
        $this->filtersModalAutofocus = '#filterLocality';
        $this->filters[] = 'filterLocality';
        $this->filters[] = 'filterDisposition';
        $this->filters[] = 'filterManualsCount';
        $this->filters[] = 'filterOrganBuilderId';
        $this->filters[] = 'filterCaseOrganBuilderId';
        $this->filters[] = 'filterpreservedCase';
        $this->filters[] = 'filterpreservedOrgan';
        $this->filters[] = 'filterConcertHall';
        $this->filters[] = 'filterForeignOrganBuilder';
        $this->filters[] = 'filterHasDisposition';
        $this->title = __('Varhany');
    }

    public function mount()
    {
        $this->mountCommon();

        if ($this->municipality && !$this->filterLocality) {
            $this->filterLocality = $this->municipality;
            if (!request()->query('viewType')) $this->viewType = 'thumbnails';
        }
    }

    public function setViewType($viewType)
    {
        if ($viewType === 'chart' && !in_array($this->sortColumn, ['stops_count', 'manuals_count'])) {
            $this->sortColumn = 'stops_count';
            $this->sortDirection = 'desc';
        }
        $this->setViewTypeHelp($viewType);
    }

    private function getCategoryEnum()
    {
        return OrganCategory::class;
    }

    #[Computed]
    public function organBuilders()
    {
        return OrganBuilder::query()->orderByName()->get();
    }

    private function getOrganCategoryOrganCount(Category $category)
    {
        $categoryModel = $this->getOrganCategoryModel($category);
        return $categoryModel->organs_count;
    }

    private function getOrganCustomCategoryOrganCount(CustomCategory $category)
    {
        return $category->organs_count;
    }

    #[Computed]
    public function municipalityInfo()
    {
        if ($this->filterLocality) {
            return $this->repository->getMunicipalityInfos($this->filterLocality);
        }
    }

    #[Computed]
    public function metaDescription()
    {
        if (app()->getLocale() === 'cs' && isset($this->municipalityInfo?->description)) {
            return $this->municipalityInfo->getMetaDescription();
        }
        return __('Prohlédněte si nejvýznamnější varhany v České republice. Zjistěte jejich stylové zařazení, seznam rejstříků (dispozici) a varhanáře, který je postavil.');
    }

}; ?>

<x-organomania.entity-page :metaDescription="$this->metaDescription" />