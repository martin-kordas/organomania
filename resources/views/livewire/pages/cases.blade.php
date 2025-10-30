<?php

use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\OrganCaseImage;
use App\Helpers;
use App\Enums\OrganCategory;
use App\Interfaces\Category;
use App\Models\Category as CategoryModel;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderAdditionalImage;
use App\Repositories\OrganRepository;
use App\Traits\HasSorting;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    #[Url(keep: true)]
    public $filterCategories;
    #[Url(keep: true)]
    public $filterOrganBuilders;

    #[Url(keep: true)]
    public $groupBy = 'organBuilder';
    #[Url(keep: true)]
    public $sortDirection = 'asc';

    private OrganRepository $organRepository;


    public function boot(OrganRepository $organRepository)
    {
        $this->organRepository = $organRepository;
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('cases');
    }

    public function rendered()
    {
        $this->dispatch("bootstrap-rendered");
        $this->dispatch("select2-rendered");
    }

    public function rendering(View $view): void
    {
        $view->title(__('Varhanní skříně'));
    }

    #[Computed]
    public function cases()
    {
        $organsQuery = Organ::query()
            ->with(['organBuilder', 'organCategories'])
            ->withCount('organRebuilds')
            ->public()
            ->whereNotNull('outside_image_url')
            // rok postavení nutné znát vždy kvůli seřazení
            ->whereRaw('
                IF(
                    case_organ_builder_id IS NOT NULL OR case_organ_builder_name IS NOT NULL,
                    case_year_built,
                    year_built
                )
                IS NOT NULL
            ')
            ->whereNotIn('id', [Organ::ORGAN_ID_PRAHA_EMAUZY]);

        $additionalImagesQuery = OrganBuilderAdditionalImage::query()
            ->with('organBuilder')
            ->where('nonoriginal_case', 0)
            ->where('organ_exists', 0)
            ->whereNotNull('year_built');

        if ($this->filterCategories) {
            $organsQuery->whereHas('organCategories', function (Builder $query) {
                $query->whereIn('organ_category_id', $this->filterCategories);
            });
            $additionalImagesQuery->whereIn('case_organ_category_id', $this->filterCategories);
        }
        if ($this->filterOrganBuilders) {
            $organsQuery
                ->whereIn(
                    DB::raw('IFNULL(case_organ_builder_id, organ_builder_id)'),
                    $this->filterOrganBuilders
                )
                // nechceme hledat varhany, které mají skříň od jiného varhanáře
                ->whereNull('case_organ_builder_name');
            $additionalImagesQuery->whereIn('organ_builder_id', $this->filterOrganBuilders);
        }

        // údaje, podle kterých se groupuje, musí být vyplněny
        switch ($this->groupBy) {
            case 'organBuilder':
                $organsQuery
                    ->where(function (Builder $query) {
                        $query
                            ->whereNotNull('organ_builder_id')
                            ->orWhereNotNull('case_organ_builder_id');
                    })
                    ->whereNull('case_organ_builder_name')
                    ->Where('organ_builder_id', '!=', OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED);
                $additionalImagesQuery->whereNotNull('organ_builder_id');
                break;

            case 'caseCategory':
                $organsQuery->whereHas('organCategories', function (Builder $query) {
                    $categories = OrganCategory::getCategoryGroups()['caseCategories'];
                    $categoryIds = collect($categories)->pluck('value');
                    $query->whereIn('organ_category_id', $categoryIds);
                });
                $additionalImagesQuery->whereNotNull('case_organ_category_id');
                break;
        }

        $organCases = $organsQuery->get()->map(
            OrganCaseImage::fromOrgan(...)
        );
        $additionalImageCases = $additionalImagesQuery->get()->map(
            OrganCaseImage::fromAdditionalImage(...)
        );

        $cases = collect($organCases)->merge($additionalImageCases);
        return $this->sortCases($cases);
    }

    #[Computed]
    public function casesGroups()
    {
        $groupProperty = match ($this->groupBy) {
            'organBuilder' => 'organBuilder.id',
            'periodCategory' => 'periodCategory',
            'caseCategory' => 'caseCategory',
            default => throw new LogicException,
        };
        return $this->cases->groupBy($groupProperty);
    }

    private function sortCases(Collection $cases)
    {
        $sortDef = match ($this->groupBy) {
            'organBuilder' => ['organBuilderActiveFromYear', $this->sortDirection],
            'periodCategory' => function (OrganCaseImage $case1, OrganCaseImage $case2) {
                if ($this->sortDirection === 'desc') Helpers::swap($case1, $case2);
                return $case1->periodCategory->getOrderValue() <=> $case2->periodCategory->getOrderValue();
            },
            'caseCategory' => ['caseCategory.value', $this->sortDirection],
            default => throw new LogicException,
        };

        return $cases->sortBy([
            $sortDef,
            ['yearBuilt', $this->sortDirection],
        ]);
    }

    #[Computed]
    public function organBuilders()
    {
        return OrganBuilder::query()->public()->orderByName()->get();
    }

    #[Computed]
    public function organCategoriesGroups()
    {
        $groups = OrganCategory::getCategoryGroups();
        $groups = array_intersect_key($groups, array_flip(['caseCategories']));
        return $groups;
    }

    #[Computed]
    public function organCategories()
    {
        $categories = OrganCategory::getCategoryGroups()['caseCategories'];
        $ids = collect($categories)->pluck('value');

        return $this->organRepository->getCategories(
            allowIds: $ids
        );
    }

    private function getOrganCategoryModel($category)
    {
        return $this->organCategories->firstOrFail(
            fn(CategoryModel $categoryModel) => $categoryModel->id === $category->value
        );
    }

    private function getOrganCategoryOrganCount(Category $category)
    {
        // nepoužívá se, protože čísla jsou zavádějící - podle groupování se aplikují další filtry, které číslo nezohledňuje
        return null;

        $categoryModel = $this->getOrganCategoryModel($category);
        $count = $categoryModel->organs_count;

        $count += OrganBuilderAdditionalImage::query()
            ->where('case_organ_category_id', $category->value)
            ->count();

        return $count;
    }

    private function getCaseOrganBuilderName(OrganCaseImage $case)
    {
        if ($this->groupBy === 'organBuilder') {
            // jméno už je zobrazeno na začátku skupiny, zobrazíme jen upřesňující jméno
            if (isset($case->organBuilderExactName)) return $case->organBuilderExactName;
        }
        else return $case->organBuilderName;
    }

    #[Computed]
    private function groupByOptions()
    {
        return [
            'organBuilder' => [__('Varhanáře')],
            'periodCategory' => [__('Kategorie období'), __('Období')],
            'caseCategory' => [__('Kategorie skříně'), __('Skříně')],
        ];
    }

    #[Computed]
    private function sortOptions()
    {
        return [
            'asc' => __('Období'),
            'desc' => __('Období'),
        ];
    }

    private function getCaseDetails(OrganCaseImage $case)
    {
        if (isset($case->details)) return __('Varhany') . ": $case->details";
    }

    private function getCaseTitle(OrganCaseImage $case)
    {
        $rows = [];
        if ($details = $this->getCaseDetails($case)) $rows[] = $details;
        if (isset($case->imageCredits)) $rows[] = __('Licence obrázku') . ": $case->imageCredits";
        return !empty($rows) ? implode("\n", $rows) : null;
    }

}; ?>

<div class="cases container">
  
    @push('meta')
        <meta name="description" content="{{ __('Prohlédněte si výtvarné prvky varhanních skříní, pozorujte jejich stylový vývoj a specifické znaky konkrétních varhanářů.') }}">
    @endpush

    <h3 class="text-center">{{ __('Varhanní skříně') }}</h3>

    
    <div class="m-auto mb-5" style="max-width: 600px;">
        <x-organomania.info-alert class="mb-3 mt-1 d-inline-block m-auto">
            {{ __('Porovnání fotografií varhanních skříní umožní porozumět jejich výtvarnému vývoji a identifikovat specifické znaky varhanářů.') }}
        </x-organomania.info-alert>

        <div class="mb-3">
            <label class="form-label" for="filterOrganBuilders">{{ __('Varhanář') }}</label>
            <x-organomania.selects.organ-builder-select model="filterOrganBuilders" :organBuilders="$this->organBuilders" :allowClear="true" small multiple live />
        </div>

        <div class="mb-4">
            <label class="form-label" for="filterCategories">{{ __('Kategorie skříně') }}</label>
            <x-organomania.selects.organ-category-select
                model="filterCategories"
                placeholder="{{ __('Zvolte kategorii skříně') }}..."
                :categoriesGroups="$this->organCategoriesGroups"
                :allowClear="true"
                small
                live
            />
        </div>

        <div class="mb-1">
            <label class="form-check-label radio-label">{{ __('Seskupit dle') }}</label>
            @foreach ($this->groupByOptions as $value => $label)
                <input
                    type="radio"
                    class="btn-check"
                    wire:model.change="groupBy"
                    value="{{ $value }}"
                    id="groupBy_{{ $value }}"
                >
                <label class="btn btn-sm btn-outline-secondary" for="groupBy_{{ $value }}">
                    <span class="d-none d-md-inline">{{ $label[0] }}</span>
                    <span class="d-md-none">{{ $label[1] ?? $label[0] }}</span>
                </label>
            @endforeach
        </div>
        <div>
            <label class="form-check-label radio-label">{{ __('Seřadit dle') }}</label>
            @foreach ($this->sortOptions as $value => $label)
                <input
                    type="radio"
                    class="btn-check"
                    wire:model.change="sortDirection"
                    value="{{ $value }}"
                    id="sortDirection_{{ $value }}"
                >
                <label class="btn btn-sm btn-outline-secondary" for="sortDirection_{{ $value }}">
                    {{ $label }}
                    <i class="bi-sort-numeric-{{ $value === 'asc' ? 'up' : 'down' }}"></i>
                </label>
            @endforeach
        </div>
    </div>

    <hr>

    @if ($this->cases->isEmpty())
        <div class="alert alert-secondary text-center" role="alert">
            {{ __('Nebyly nalezeny žádné fotografie.') }}
        </div>
    @else
        <p class="small text-muted text-center">
            Celkem
            <span class="fw-semibold">{{ $this->cases->count() }}</span>
            {{ Helpers::declineCount($this->cases->count(), __('fotografií'), __('fotografie'), __('fotografie')) }}
        </p>

        @foreach ($this->casesGroups as $groupId => $cases)
            <h4 class="fs-5 mt-4 mb-3 text-center" wire:key="{{ "organBuilder$groupId" }}">
                @switch($groupBy)
                    @case('organBuilder')
                        <x-organomania.organ-builder-link :organBuilder="$cases[0]->organBuilder" :newTab="true" showActivePeriod showMunicipality />
                        @break
                
                    @case('periodCategory')
                        {{ __('Období') }} {{ __($cases[0]->periodCategory->getName()) }}
                        @break
                
                    @case('caseCategory')
                        {{ $cases[0]->caseCategory->getName() }}
                        @break
                @endswitch
            </h4>

            <div class="d-flex flex-wrap flex-row column-gap-4 row-gap-4 justify-content-center">
                @foreach ($cases as $case)
                    <div class="text-center">
                        <a href="{{ $case->imageUrl }}" target="_blank">
                            <img 
                                src="{{ $case->imageUrl }}" 
                                alt="{{ $case->name }}"
                                class="case-image rounded border"
                                loading="lazy"
                                @if ($title = $this->getCaseTitle($case))
                                    title="{{ $title }}"
                                @endif
                            >
                        </a>
                        <div class="small text-center mt-1">
                            <p
                                class="text-truncate m-auto"
                                style="max-width: 21em;"
                                @if ($details = $this->getCaseDetails($case))
                                    title="{{ $details }}"
                                @endif
                            >
                                @isset ($case->organ)
                                    <x-organomania.organ-link :organ="$case->organ" :year="false" :showDescription="false" :iconLink="false" :newTab="true" showShortPlace />
                                @else
                                    <i class="bi bi-music-note-list"></i>
                                    {{ $case->name }}
                                @endisset
                            </p>
                            <div class="text-secondary small">
                                @if ($organBuilderName = $this->getCaseOrganBuilderName($case))
                                    @if ($case->organBuilder)
                                        @if ($case->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED)
                                            <x-organomania.organ-builder-link
                                                :organBuilder="$case->organBuilder"
                                                :name="$organBuilderName"
                                                :showDescription="false"
                                                :newTab="true"
                                                :iconLink="false"
                                            />
                                        @endif
                                    @else
                                        <i class="bi-person-circle"></i>
                                        {{ $organBuilderName }}
                                    @endif
                                @endif
                                ({{ $case->yearBuilt }})
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
