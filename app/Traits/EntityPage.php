<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use RuntimeException;
use App\Models\Category as CategoryModel;
use App\Models\Region;
use App\Models\CustomCategory;
use App\Interfaces\Category;
use App\Traits\HasSorting;

trait EntityPage
{
    
    use HasSorting;
    
    #[Session(key: 'viewType')]
    #[Url(keep: true)]
    public $viewType = 'thumbnails';
    
    #[Url(keep: true)]
    public $filterId;
    #[Url(keep: true)]
    public $filterCategories;
    #[Url(keep: true)]
    public $filterRegionId;
    #[Url(keep: true)]
    public $filterImportance;
    #[Url(keep: true)]
    public $filterFavorite;
    #[Url(keep: true)]
    public $filterPrivate;
    #[Url(keep: true)]
    public $filterNearLatitude;
    #[Url(keep: true)]
    public $filterNearLongitude;
    #[Url(keep: true)]
    public $filterNearDistance;

    #[Url(keep: true)]
    public $perPage = 9;

    public $favoriteOrgansCount;
    public $privateOrgansCount;

    /** zda jde o zobrazení soukromé custom kategorie v signed routě */
    private $isCustomCategoryOrgans = false;
    
    private bool $isLikeable = true;
    private bool $isEditable = true;
    private bool $isExportable = true;
    private bool $isCategorizable = true;
    private bool $showQuickFilter = true;
    
    private ?string $createRoute;
    private ?string $exportRoute;
    private ?string $customCategoriesRoute;
    private ?string $customCategoryRoute;
    private ?string $categorySelectPlaceholder;
    private ?string $customCategoriesTitle;
    private ?string $gateUseCustomCategories;
    private int $maxImportance = 5;
    private ?string $categoryClass = null;
    private ?string $gateLike;
    private string $entityPageViewComponent;
    private string $entityClass;
    private string $entityNamePluralNominativ;
    private string $entityNamePluralAkuzativ;
    private string $filtersModalAutofocus;
    private bool $filtersModalScrollable = false;
    private array $filters = [];
    private array $commonFilters = [
        'filterId', 'filterCategories', 'filterRegionId', 'filterImportance', 'filterFavorite', 'filterPrivate',
        'filterNearLatitude', 'filterNearLongitude', 'filterNearDistance'
    ];
    private array $invisibleFilters = [
        'filterId', 'filterNearLatitude', 'filterNearLongitude', 'filterNearDistance'
    ];
    private array $viewTypes = ['thumbnails', 'table', 'map'];
    private string $title;
    
    abstract private function getCategoryEnum();
    
    abstract private function getOrganCategoryOrganCount(Category $category);
    abstract private function getOrganCustomCategoryOrganCount(CustomCategory $category);
    
    public function mountCommon()
    {
        $this->isCustomCategoryOrgans = request()->route()->getName() === $this->customCategoryRoute;
        if ($this->isCustomCategoryOrgans) {
            // TODO: 401 vzniká po jakékoli interakci se stranou (seřazení záznamů atd.) a přenačtení stránky
            //  - ::hasValidSignatureWhileIgnoring() problém nevyřešilo
            if (!request()->hasValidSignature(false)) abort(401);
            if (empty($this->getCustomCategoryIds())) throw new RuntimeException;
        }

        if ($this->isLikeable) $this->favoriteOrgansCount = $this->getFavoriteOrgansCount();
        if ($this->isEditable) $this->privateOrgansCount = $this->getPrivateOrgansCount();
    }
    
    protected function bootCommon()
    {
        // viewType se sdílí mezi entitami, může tedy být aktivní viewType, který aktuální entita nepodporuje
        if (!in_array($this->viewType, $this->viewTypes)) $this->viewType = reset($this->viewTypes);
    }
    
    private function getCustomCategoryIds()
    {
        // TODO: podobná funkcionalita je v OrganForm
        return collect($this->filterCategories)->flatMap(function ($id) {
            return str_starts_with($id, 'custom-')
                ? [str_replace('custom-', '', $id)]
                : [];
        })->all();
    }
    
    public function updated($property)
    {
        $jsViewType = in_array($this->viewType, ['map', 'timeline', 'chart']);
        $reloaded = false;
        
        // pokud se odmazává z multiselectu, $property je ve tvaru "$filter.$index"
        $filterPropertiesDot = array_map(
            fn ($filter) => "$filter.",
            $this->getFilters(),
        );
        
        if (in_array($property, [...$this->getFilters(), 'perPage']) || str($property)->startsWith($filterPropertiesDot)) {
            $this->dispatch('filtering-changed');
            
            // Google mapa má z tech. důvodů nastaveno wire:replace, při aktualizaci zobrazených varhan tedy musíme přenačíst celou stranu
            if ($jsViewType) {
                $this->js('location.reload()');
                $reloaded = true;
            }
        }
    }
    
    public function setViewType($viewType)
    {
        $this->setViewTypeHelp($viewType);
    }
            
    protected function setViewTypeHelp($viewType)
    {
        $this->viewType = $viewType;
        $jsViewType = in_array($this->viewType, ['map', 'timeline', 'chart']);
        if ($jsViewType) $this->js('setTimeout(() => location.reload())');
    }
    
    #[On('pagination-changed')]
    #[On('sort-changed')]
    #[On('sort-direction-changed')]
    public function onSortChanged()
    {
        if ($this->viewType === 'chart') $this->js('location.reload()');
    }
    
    private function getFilters()
    {
        return [...$this->commonFilters, ...$this->filters];
    }
    
    public function updatedFilterCategories()
    {
        if (in_array('custom-new', $this->filterCategories)) {    
            $this->filterCategories = array_diff($this->filterCategories, ['custom-new']);
            $this->redirectRoute($this->customCategoriesRoute, navigate: true);
        }
    }
    
    public function rendering(View $view): void
    {
        $view->title($this->title);
    }
    
    private function getFavoriteOrgansCount()
    {
        return $this->model->query()->whereHas('likes', function (Builder $query) {
            $query->where('user_id', Auth::id());
        })->count();
    }

    private function getPrivateOrgansCount()
    {
        return $this->model->query()->whereNotNull('user_id')->count();
    }

    private function getCustomCategoryGroupName($group)
    {
        return match($group) {
            'shared' => __('Sdílené kategorie'),
            'custom' => __('Vlastní kategorie'),
        };
    }

    #[Computed]
    public function organCategoriesGroups()
    {
        $enum = $this->getCategoryEnum();
        return $enum::getCategoryGroups();
    }
    
    #[Computed]
    public function organCategories()
    {
        return $this->repository->getCategories();
    }

    #[Computed]
    public function organCustomCategories()
    {
        $allowIds = $this->isCustomCategoryOrgans ? $this->getCustomCategoryIds() : null;
        
        return $this->repository->getCustomCategories(
            allowIds: $allowIds
        );
    }
    
    private function getCustomCategoriesGroups($modal = false)
    {
        $groups = [];
        foreach ($this->organCustomCategories as $category) {
            $userId = Auth::id();
            $shared = !$userId || $category->user_id !== $userId;
            $group = $shared ? 'shared' : 'custom';
            $groups[$group] ??= [];
            $groups[$group][] = $category;
        }

        if (!$modal && Gate::allows('useOrganCustomCategories')) {
            $groups['custom'][] = new class extends CustomCategory {
                public function getValue(): int|string {
                    return 'new';
                }
                public function getName(): string {
                    return __('Přidat kategorii...');
                }
                public function getDescription(): ?string {
                    return null;
                }
                public function getColor(): string {
                    return 'primary';
                }
                public function getItemsUrl(): string {
                    return '/';
                }
            };
        }
        return $groups;
    }

    public function export()
    {
        $this->dispatch('export-organs');
    }

    #[Computed]
    public function organCustomCategoriesGroups()
    {
        return $this->getCustomCategoriesGroups();
    }

    #[Computed]
    public function organCustomCategoriesGroupsForModal()
    {
        return $this->getCustomCategoriesGroups(modal: true);
    }
    
    public function getModel()
    {
        return $this->model;
    }

    private function getOrganCategoryModel($category)
    {
        return $this->organCategories->firstOrFail(
            fn(CategoryModel $categoryModel) => $categoryModel->id === $category->value
        );
    }

    #[Computed]
    public function activeFiltersCount()
    {
        $count = 0;
        foreach ($this->getFilters() as $filter) {
            if ($this->{$filter}) $count++;
        }
        return $count;
    }

    #[Computed]
    public function activeVisibleFiltersCount()
    {
        $count = 0;
        foreach ($this->getFilters() as $filter) {
            if ($this->{$filter} && !in_array($filter, $this->invisibleFilters)) $count++;
        }
        return $count;
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }
    
    #[On('organ-like-updated')] 
    public function updateFavoriteOrgansCount(int $diff)
    {
        $this->favoriteOrgansCount += $diff;
    }

    public function rendered()
    {
        $this->dispatch("select2-rendered");
    }

    #[Computed]
    public function perPageValues()
    {
        return [
            ...range(1, 15),
            ...array_map(
                fn($val) => $val * 10,
                range(2, 10),
            ),
            200, 300
        ];
    }
    
}
