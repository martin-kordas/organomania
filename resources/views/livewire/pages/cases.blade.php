<?php

use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
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
    public $filterPeriodCategories;
    #[Url(keep: true)]
    public $filterOrganBuilders;
    #[Url(keep: true)]
    public $filterOrganomaniaOrgans;

    #[Url(keep: true)]
    public $groupBy = 'organBuilder';
    #[Url(keep: true)]
    public $sort = 'yearBuiltAsc';

    private OrganRepository $organRepository;

    #[Locked]
    public bool $showCollapseAll = true;


    public function boot(OrganRepository $organRepository)
    {
        $this->organRepository = $organRepository;
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('cases');

        if (
            $this->groupBy === 'organBuilder' && $this->filterOrganBuilders && count($this->filterOrganBuilders) === 1
            || $this->groupBy === 'periodCategory' && $this->filterPeriodCategories && count($this->filterPeriodCategories) === 1
            || $this->groupBy === 'caseCategory' && $this->filterCategories && count($this->filterCategories) === 1
        ) {
            $this->showCollapseAll = false;
        }
    }

    public function rendered()
    {
        $this->dispatch("bootstrap-rendered");
        $this->dispatch("select2-rendered");
    }

    public function rendering(View $view): void
    {
        $view->title(__('Galerie varhanních skříní'));
    }

    private function getOrgansQuery(): Builder
    {
        return Organ::query()
            ->select('*')
            ->selectRaw('
                IF(
                    case_organ_builder_id IS NOT NULL OR case_organ_builder_name IS NOT NULL,
                    case_year_built,
                    year_built
                )
                AS year_built1
            ')
            ->with(['organBuilder', 'organCategories'])
            ->withCount('organRebuilds')
            ->public()
            ->whereNotNull('outside_image_url')
            ->whereNotIn('id', [Organ::ORGAN_ID_PRAHA_EMAUZY, Organ::ORGAN_ID_PARDUBICE_ZUS_POLABINY])
            // rok postavení nutné znát vždy kvůli seřazení
            ->havingNotNull('year_built1');
    }

    private function getAdditionalImagesQuery(): Builder
    {
        return OrganBuilderAdditionalImage::query()
            ->select('*')
            ->selectRaw('year_built AS year_built1')
            ->with('organBuilder')
            ->where('nonoriginal_case', 0)
            ->where('organ_exists', 0)
            ->whereNotNull('year_built');
    }

    #[Computed]
    public function cases()
    {
        // TODO: logika dohledávání obrázků je obdobná jako v organ-builder-show.blade.php (tam je akorát implicitně filtr na varhanáře)
        //  - přesto se data na obou místech dotazují zvlášť a používají se různé struktury (zde OrganCaseImage, tam prosté pole)

        $organsQuery = $this->getOrgansQuery();
        $additionalImagesQuery = $this->getAdditionalImagesQuery();

        if ($this->filterCategories) {
            $organsQuery->whereHas('organCategories', function (Builder $query) {
                $query->whereIn('organ_category_id', $this->filterCategories);
            });
            $additionalImagesQuery->whereIn('case_organ_category_id', $this->filterCategories);
        }
        if ($this->filterPeriodCategories) {
            $periodRanges = $this->getPeriodRangesFromOrganCategoryIds($this->filterPeriodCategories);

            if (!empty($periodRanges)) {
                foreach ([$organsQuery, $additionalImagesQuery] as $query) {
                    $query->having(function (QueryBuilder $query) use ($periodRanges) {
                        foreach ($periodRanges as [$from, $to]) {
                            $query->orHaving(function (QueryBuilder $query) use ($from, $to) {
                                if (isset($from)) $query->having('year_built1', '>=', $from);
                                if (isset($to)) $query->having('year_built1', '<=', $to);
                            });
                        }
                    });
                }
            }
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

        // údaje, podle kterých se řadí, musí být vyplněny
        switch ($this->sort) {
            case 'stopsCountDesc':
                $organsQuery
                    // udaná velikost se musí vztahovat k původním varhanám ve skříni
                    ->whereNull('case_organ_builder_id')
                    ->whereNull('case_organ_builder_name')
                    // musí být známa původní velikost
                    ->where(function (Builder $query) {
                        $query
                            ->whereNotNull('original_stops_count')
                            ->orWhere(function (Builder $query) {
                                $query
                                    ->whereNotNull('stops_count')
                                    ->whereDoesntHave('organRebuilds');
                            });
                    });
                $additionalImagesQuery->whereNotNull('stops_count');
                break;
        }

        $organCases = $organsQuery->get()->map(
            OrganCaseImage::fromOrgan(...)
        );
        $cases = collect($organCases);
        
        if (!$this->filterOrganomaniaOrgans) {
            $additionalImageCases = $additionalImagesQuery->get()->map(
                OrganCaseImage::fromAdditionalImage(...)
            );
            $cases = $cases->merge($additionalImageCases);
        }

        return $this->sortCases($cases);
    }

    private function getPeriodRangesFromOrganCategoryIds($categoryIds)
    {
        // TODO: na sebe navazující rozsahy lze sloučit
        $ranges = [];
        foreach ($categoryIds as $id) {
            $category = OrganCategory::tryFrom($id);
            if ($category->isPeriodCategory()) {
                $ranges[] = $category->getPeriodRange();
            }
        }
        return $ranges;
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
        $timeSortDirection = $this->sort === 'yearBuiltDesc' ? 'desc' : 'asc';

        // seřazení skupin
        //  - je ovlivněno řazením dle období - např. řadíme-li skříně podle období, řadíme i varhanáře/kategorie (skupiny) dle období
        $sortDefinitionGroup = match ($this->groupBy) {
            'organBuilder' => ['organBuilderActiveFromYear', $timeSortDirection],
            'periodCategory' => function (OrganCaseImage $case1, OrganCaseImage $case2) use ($timeSortDirection) {
                if ($timeSortDirection === 'desc') Helpers::swap($case1, $case2);
                return $case1->periodCategory->getOrderValue() <=> $case2->periodCategory->getOrderValue();
            },
            'caseCategory' => ['caseCategory.value', $timeSortDirection],
            default => throw new LogicException,
        };

        // seřazení jednotlivých skříní
        $sortDefinitionCase = match ($this->sort) {
            'yearBuiltAsc', 'yearBuiltDesc' => ['yearBuilt', $timeSortDirection],
            'stopsCountDesc' => ['stopsCount', 'desc'],
            default => throw new LogicException,
        };

        return $cases->sortBy([
            $sortDefinitionGroup,
            $sortDefinitionCase,
            ['yearBuilt', $timeSortDirection],
            ['id', 'asc'],
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
            'organBuilder' => __('Varhanáře'),
            'caseCategory' => __('Kategorie'),
            'periodCategory' => __('Období'),
        ];
    }

    #[Computed]
    private function sortOptions()
    {
        return [
            'yearBuiltAsc' => __('Období'),
            'yearBuiltDesc' => __('Období'),
            'stopsCountDesc' => __('Velikost'),
        ];
    }

    private function getCaseDetails(OrganCaseImage $case)
    {
        $details = [$case->yearBuilt];
        if (isset($case->details)) $details[] = $case->details;
        return implode(', ', $details);
    }

    private function getCaseTitle(OrganCaseImage $case)
    {
        $rows = [];
        if (isset($case->imageCredits)) $rows[] = __('Licence obrázku') . ": $case->imageCredits";
        return !empty($rows) ? implode("\n", $rows) : null;
    }

}; ?>

<div class="cases container">
  
    @push('meta')
        <meta name="description" content="{{ __('Prohlédněte si výtvarné prvky varhanních skříní, pozorujte jejich stylový vývoj a specifické znaky konkrétních varhanářů.') }}">
    @endpush

    <h3 class="text-center">
        <a class="link-primary text-decoration-none" href="{{ route('organs.cases') }}" wire:navigate>
            {{ __('Galerie varhanních skříní') }}
        </a>
    </h3>

    
    <div class="m-auto mb-5" style="max-width: 600px;">
        <x-organomania.info-alert class="mb-3 mt-1 d-inline-block m-auto">
            {{ __('Porovnání fotografií varhanních skříní umožní porozumět jejich výtvarnému vývoji a identifikovat specifické znaky varhanářů.') }}
        </x-organomania.info-alert>

        <div class="mb-3">
            <label class="form-label" for="filterOrganBuilders">{{ __('Varhanář') }}</label>
            {{-- allowClear nepoužito, protože u multiselectů bez <optgroup> a bez prázdné <option> blbne na mobilu (po vymazání se vybere první <option>) --}}
            <x-organomania.selects.organ-builder-select model="filterOrganBuilders" :organBuilders="$this->organBuilders" small multiple live />
        </div>

        <div class="mb-4">
            <label class="form-label" for="filterCategories">{{ __('Kategorie skříně') }}</label>
            {{-- allowClear nepoužito (důvod viz výše) --}}
                <x-organomania.selects.organ-category-select
                model="filterCategories"
                placeholder="{{ __('Zvolte kategorii skříně') }}..."
                :categoriesGroups="$this->organCategoriesGroups"
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
                    {{ $label }}
                </label>
            @endforeach
        </div>
        <div>
            <label class="form-check-label radio-label">{{ __('Seřadit dle') }}</label>
            @foreach ($this->sortOptions as $value => $label)
                <input
                    type="radio"
                    class="btn-check"
                    wire:model.change="sort"
                    value="{{ $value }}"
                    id="sort_{{ $value }}"
                >
                <label class="btn btn-sm btn-outline-secondary" for="sort_{{ $value }}">
                    {{ $label }}
                    <i class="bi-sort-numeric-{{ str($value)->endsWith('Asc') ? 'up' : 'down' }}"></i>
                </label>
            @endforeach
        </div>

        <div class="form-check form-switch mt-2">
            <label class="form-check-label" for="filterOrganomaniaOrgans">{{ __('Jen varhany s článkem v Organomanii') }}</label>
            <input class="form-check-input" type="checkbox" role="switch" id="filterOrganomaniaOrgans" wire:model.live="filterOrganomaniaOrgans">
        </div>
    </div>

    <hr>

    @if ($this->cases->isEmpty())
        <div class="alert alert-secondary text-center" role="alert">
            {{ __('Nebyly nalezeny žádné fotografie.') }}
        </div>
    @else
        <p class="small text-muted text-center">
            {{ __('Celkem') }}
            <span class="fw-semibold">{{ $this->cases->count() }}</span>
            {{ Helpers::declineCount($this->cases->count(), __('fotografií'), __('fotografie'), __('fotografie')) }}
        </p>

        @if ($this->showCollapseAll)
            <div class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" onclick="collapseAll()">
                    <i class="bi-chevron-contract"></i>
                    {{ __('Skrýt všechny skupiny') }}
                </button>
            </div>
        @endif

        @foreach ($this->casesGroups as $groupId => $cases)
            <div class="group-container border rounded p-2 p-md-3 my-4">
                <h4 class="d-flex align-items-center fs-5 my-2 my-md-0" wire:key="{{ "organBuilder$groupId" }}">
                    <span class="me-1">
                        <span class="">
                            @switch($groupBy)
                                @case('organBuilder')
                                    <x-organomania.organ-builder-link :organBuilder="$cases[0]->organBuilder" :newTab="true" :iconLink="false" showActivePeriod showMunicipality />
                                    @break
                            
                                @case('periodCategory')
                                    <a class="text-decoration-none" href="{{ route('about-organ') }}#periodCategory{{ $cases[0]->periodCategory->value }}" target="_blank">
                                        {{ __('Období') }} {{ __($cases[0]->periodCategory->getName()) }}
                                    </a>
                                    @break
                            
                                @case('caseCategory')
                                    {{ $cases[0]->caseCategory->getName() }}
                                    @break
                            @endswitch
                        </span>

                        <span class="badge text-bg-secondary rounded-pill ms-1" style="font-size: 55%;">
                            {{ count($cases) }}
                        </span>

                        @if ($groupBy === 'caseCategory')
                            @if ($description = $cases[0]->caseCategory->getDescription())
                                <br />
                                <span class="d-block mt-1 fw-normal text-secondary lh-base" style="font-size: 65%">{{ $description }}</span>
                            @endif
                        @endif
                    </span>

                    <span class="ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit/skrýt skupinu') }}">
                        <button type="button" class="btn btn-sm collapse-btn btn-outline-secondary ms-1 rounded-pill" data-bs-toggle="collapse" href="#group{{ $groupId }}" onclick="collapseBtnOnclick(this)">
                            <i class="bi-chevron-contract"></i>
                        </button>
                    </span>
                </h4>

                <div id="group{{ $groupId }}" class="group flex-wrap flex-row column-gap-4 row-gap-3 mt-3 justify-content-center collapse show">
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
                                    title="{{ $case->name }}"
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
                                    ({{ $this->getCaseDetails($case) }})
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

@script
<script>
    window.collapseBtnOnclick = function (button) {
        $(button).find('i').toggleClass('bi-chevron-contract, bi-chevron-expand')
    }

    window.collapseAll = function (e) {
        $('.collapse-btn').each((_i, button) => {
            let href = $(button).attr('href')
            let collapse = $(href)
            if (collapse.hasClass('show')) {
                collapse.removeClass('show')
                collapseBtnOnclick(button)
            }
        })
    }
</script>
@endscript