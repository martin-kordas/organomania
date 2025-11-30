<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Traits\EntityPage;
use App\Repositories\OrganBuilderRepository;
use App\Interfaces\Category;
use App\Models\Category as CategoryModel;
use App\Enums\OrganBuilderCategory;
use App\Models\OrganBuilder;
use App\Models\CustomCategory;
use App\Models\OrganBuilderCategory as OrganBuilderCategoryModel;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use EntityPage;

    #[Url(keep: true)]
    public $filterName;
    #[Url(keep: true)]
    public $filterMunicipality;
    #[Url(keep: true)]
    public $filterSearch;
    #[Url(keep: true)]
    public $search;

    public $municipality;

    private OrganBuilderRepository $repository;
    private OrganBuilder $model;
    private OrganBuilderCategoryModel $categoryModel;

    const SORT_OPTIONS = [
        ['column' => 'name', 'label' => 'Varhanář/dílna', 'type' => 'alpha'],
        ['column' => 'municipality', 'label' => 'Lokalita', 'type' => 'alpha'],
        ['column' => 'distance', 'label' => 'Vzdálenost', 'shortLabel' => 'Vzdál.', 'type' => 'numeric'],
        ['column' => 'active_from_year', 'label' => 'Období', 'type' => 'numeric'],
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric', 'directions' => ['desc']],
        ['column' => 'organs_count', 'label' => 'Počet varhan', 'type' => 'numeric', 'shortLabel' => 'Varhany', 'directions' => ['desc']],
        ['column' => 'views', 'label' => 'Počet zobrazení', 'type' => 'numeric', 'directions' => ['desc']],
        ['column' => 'random', 'label' => 'Řadit náhodně', 'shortLabel' => 'Náhodně', 'type' => 'random', 'directions' => ['asc']],
    ];

    public function boot(OrganBuilderRepository $repository, OrganBuilder $model, OrganBuilderCategoryModel $categoryModel)
    {
        $this->viewTypes[] = 'timeline';
        $this->bootCommon();

        $this->repository = $repository;
        $this->model = $model;
        $this->categoryModel = $categoryModel;
        $this->hasMunicipalityInfo = true;

        $this->createRoute = 'organ-builders.create';
        $this->exportRoute = 'organ-builders.export';
        $this->customCategoriesRoute = 'organ-builders.organ-builder-custom-categories';
        $this->customCategoryRoute = 'organ-builders.custom-category-organ-builders.index';
        $this->categorySelectPlaceholder = __('Zvolte kategorii varhanářů');
        $this->customCategoriesTitle = __('Spravovat vlastní kategorie varhanářů');
        $this->gateUseCustomCategories = 'useOrganBuilderCustomCategories';
        $this->categoryClass = OrganBuilderCategory::class;
        $this->gateLike = 'likeOrganBuilders';
        $this->entityPageViewComponent = 'organ-builders-view';
        $this->entityClass = OrganBuilder::class;
        $this->entityNamePluralNominativ = __('varhanáři');
        $this->entityNamePluralAkuzativ = __('varhanáře');
        $this->filtersModalScrollable = true;
        $this->filtersModalAutofocus = '#filterName';
        $this->filters[] = 'filterName';
        $this->filters[] = 'filterMunicipality';
        $this->filters[] = 'filterSearch';
        $this->title = __('Varhanáři');
    }

    public function mount()
    {
        $this->perPage = 12;
        $this->mountCommon();

        if ($this->municipality && !$this->filterMunicipality) {
            $this->filterMunicipality = $this->municipality;
            if (!request()->query('viewType')) $this->viewType = 'thumbnails';
            if (!request()->query('perPage')) $this->perPage = 40;      // aby text municipalityInfo nevyskakoval zvlášť na každé stránce
        }
        if (isset($this->municipalityInfo)) $this->title = $this->municipalityInfo->heading;
    }

    private function getCategoryEnum()
    {
        return OrganBuilderCategory::class;
    }

    private function getOrganCategoryOrganCount(Category $category)
    {
        $categoryModel = $this->getOrganCategoryModel($category);
        return $categoryModel->organ_builders_count;
    }

    private function getOrganCustomCategoryOrganCount(CustomCategory $category)
    {
        return $category->organ_builders_count;
    }

    private function getPrivateOrgansCount()
    {
        return $this->model->query()->inland()->whereNotNull('user_id')->count();
    }

    #[Computed]
    public function municipalityInfo()
    {
        if ($this->filterMunicipality) {
            return $this->repository->getMunicipalityInfos($this->filterMunicipality);
        }
    }

    #[Computed]
    public function metaDescription()
    {
        if (app()->getLocale() === 'cs' && isset($this->municipalityInfo?->description)) {
            return $this->municipalityInfo->getMetaDescription();
        }
        return __('Prohlédněte si historické i soudobé varhanářské dílny na území ČR. Zjistěte jejich stylové zařazení (barokní, romantické varhanářství) a nejslavnější postavené varhany.');
    }

}; ?>

<x-organomania.entity-page :metaDescription="$this->metaDescription" />
