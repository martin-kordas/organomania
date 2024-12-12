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
    public $filterOrganBuilderId;
    #[Url(keep: true)]
    public $filterConcertHall;
    #[Url(keep: true)]
    public $filterForeignOrganBuilder;
    #[Url(keep: true)]
    public $filterHasDisposition;

    private OrganRepository $repository;
    private Organ $model;
    private OrganCategoryModel $categoryModel;

    const SORT_OPTIONS = [
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric'],
        ['column' => 'organ_builder', 'label' => 'Varhanář', 'type' => 'numeric'],
        ['column' => 'year_built', 'label' => 'Rok', 'type' => 'numeric'],
        ['column' => 'manuals_count', 'label' => 'Počet manuálů', 'shortLabel' => 'Man.', 'type' => 'numeric'],
        ['column' => 'stops_count', 'label' => 'Počet rejstříků', 'shortLabel' => 'Rejstříky', 'type' => 'numeric'],
        ['column' => 'municipality', 'label' => 'Obec', 'type' => 'alpha'],
    ];

    public function boot(OrganRepository $repository, Organ $model, OrganCategoryModel $categoryModel)
    {
        $this->repository = $repository;
        $this->model = $model;
        $this->categoryModel = $categoryModel;

        $this->createRoute = 'organs.create';
        $this->exportRoute = 'organs.export';
        $this->customCategoriesRoute = 'organs.organ-custom-categories';
        $this->customCategoryRoute = 'organs.custom-category-organs.index';
        $this->categorySelectPlaceholder = __('Zvolte kategorii varhan');
        $this->gateUseCustomCategories = 'useOrganCustomCategories';
        $this->categoryClass = OrganCategory::class;
        $this->gateLike = 'likeOrgans';
        $this->entityPageViewComponent = 'organs-view';
        $this->entityClass = Organ::class;
        $this->entityNamePluralAkuzativ = __('varhany');
        $this->filters[] = 'filterOrganBuilderId';
        $this->filters[] = 'filterConcertHall';
        $this->filters[] = 'filterForeignOrganBuilder';
        $this->filters[] = 'filterHasDisposition';
        $this->title = __('Varhany');
    }

    public function mount()
    {
        $this->mountCommon();
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

}; ?>

<x-organomania.entity-page :metaDescription="__('Prohlédněte si nejvýznamnější varhany v České republice. Zjistěte jejich stylové zařazení, seznam rejstříků (dispozici) a varhanáře, který je postavil.')" />