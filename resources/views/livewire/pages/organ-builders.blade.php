<?php

use Livewire\Volt\Component;
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

    private OrganBuilderRepository $repository;
    private OrganBuilder $model;
    private OrganBuilderCategoryModel $categoryModel;

    const SORT_OPTIONS = [
        ['column' => 'name', 'label' => 'Varhanář/dílna', 'type' => 'alpha'],
        ['column' => 'municipality', 'label' => 'Lokalita', 'type' => 'alpha'],
        ['column' => 'active_from_year', 'label' => 'Období', 'type' => 'numeric'],
        ['column' => 'importance', 'label' => 'Význam', 'type' => 'numeric'],
    ];

    public function boot(OrganBuilderRepository $repository, OrganBuilder $model, OrganBuilderCategoryModel $categoryModel)
    {
        $this->repository = $repository;
        $this->model = $model;
        $this->categoryModel = $categoryModel;

        $this->createRoute = 'organ-builders.create';
        $this->exportRoute = 'organ-builders.export';
        $this->customCategoriesRoute = 'organ-builders.organ-builder-custom-categories';
        $this->customCategoryRoute = 'organ-builders.custom-category-organ-builders.index';
        $this->categorySelectPlaceholder = __('Zvolte kategorii varhanářů...');
        $this->gateUseCustomCategories = 'useOrganBuilderCustomCategories';
        $this->gateLike = 'likeOrganBuilders';
        $this->entityPageViewComponent = 'organ-builders-view';
        $this->entityClass = OrganBuilder::class;
        $this->title = __('Varhanáři');
    }

    public function mount()
    {
        $this->perPage = 12;
        $this->mountCommon();
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

}; ?>

<x-organomania.entity-page />
