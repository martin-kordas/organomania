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
use App\Http\Controllers\ThumbnailController;
use App\Interfaces\Category;
use App\Models\Category as CategoryModel;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderAdditionalImage;
use App\Repositories\OrganRepository;
use App\Services\MarkdownConvertorService;
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

    public ?OrganBuilder $organBuilder;

    private OrganRepository $organRepository;
    private MarkdownConvertorService $markdownConvertor;


    #[Locked]
    public bool $showCollapseAll = true;


    public function boot(OrganRepository $organRepository, MarkdownConvertorService $markdownConvertor)
    {
        $this->organRepository = $organRepository;
        $this->markdownConvertor = $markdownConvertor;
    }

    public function mount()
    {
        Helpers::logPageViewIntoCache('cases');

        if (isset($this->organBuilder) && !$this->filterOrganBuilders) {
            $this->filterOrganBuilders = [$this->organBuilder->id];
        }

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
        $this->js('setTimeout(() => window.initMagnifier())');
    }

    public function rendering(View $view): void
    {
        $title = __('Galerie varhanních skříní');
        if ($this->filterOrganBuilders && count($this->filterOrganBuilders) === 1) {
            $case = $this->cases->first(
                fn ($case) => isset($case->organBuilder)
            );
            if ($case) $title = "{$case->organBuilder->name} – $title";
        }
        
        $view->title($title);
    }

    #[Computed]
    public function cases()
    {
        // TODO: logika dohledávání obrázků je obdobná jako v organ-builder-show.blade.php (tam je akorát implicitně filtr na varhanáře)
        //  - přesto se data na obou místech dotazují zvlášť a používají se různé struktury (zde OrganCaseImage, tam prosté pole)

        $organsQuery = $this->organRepository->getCaseImagesOrgansQuery()
            ->with(['organBuilder', 'timelineItem', 'organCategories'])
            ->withCount('organRebuilds');
            
        // zobrazují-li se fotografie jen 1 varhanáře, nemusíme odfiltrovávat fotky zobrazené u jiných varhanářů
        //  - matoucí: počet fotek bude vyšší než počet uvedený v selecu varhanářů
        $withoutOrganExists = !($this->filterOrganBuilders && count($this->filterOrganBuilders) === 1);
        $additionalImagesQuery = $this->organRepository->getCaseImagesAdditionalImagesQuery($withoutOrganExists)
            ->with('organBuilder');

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
            /*
            // nově není potřeba - varhany bez varhanáře se groupují do vlastní kategorie
            case 'organBuilder':
                $organsQuery
                    ->where(function (Builder $query) {
                        $query
                            ->whereNotNull('organ_builder_id')
                            ->orWhereNotNull('case_organ_builder_id');
                    })
                    ->whereNull('case_organ_builder_name')
                    ->where('organ_builder_id', '!=', OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED);
                $additionalImagesQuery->whereNotNull('organ_builder_id');
                break;
            */

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
            'organBuilder' => fn (OrganCaseImage $case) => $case->organBuilder?->id ?? -1,
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

    private function getOrganCategoryOrganCount(Category $category)
    {
        return $this->organRepository->getOrganCategoryCaseImagesCount($category);
    }

    private function getOrganBuilderOrganCount(OrganBuilder $organBuilder)
    {
        return $this->organRepository->getOrganBuilderCaseImagesCount($organBuilder);
    }

    private function getCaseOrganBuilderName(OrganCaseImage $case)
    {
        // jméno už je zobrazeno na začátku skupiny, zobrazíme jen upřesňující jméno
        //  - jeli však skříň bez varhanáře, zařadí se do skupiny "ostatní varhanáři" a musíme zobrazit jméno ("neznámý")
        if ($this->groupBy === 'organBuilder' && isset($case->organBuilder)) {
            return $case->organBuilderExactName ?? null;
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

    #[Computed]
    private function organBuilderGroups()
    {
        return [
            'brněnská varh. škola' => [4, 3, 60, 62],
            'loketská varh. škola' => [8, 28],
        ];
    }

    #[Computed]
    private function showCaseParts()
    {
        // zobrazit jen při výchozím nastavení
        return
            !$this->filterCategories && !$this->filterPeriodCategories && !$this->filterOrganBuilders && !$this->filterOrganomaniaOrgans
            && $this->groupBy === 'organBuilder'
            && $this->sort === 'yearBuiltAsc';
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
  
    @if (!isset($this->organBuilder))
        @push('meta')
            <meta name="description" content="{{ __('Prohlédněte si výtvarné prvky varhanních skříní, pozorujte jejich stylový vývoj a specifické znaky konkrétních varhanářů.') }}">
        @endpush
    @endif

    @push('scripts')
        <script src="/assets/magnifier-js/Event.js" defer></script>
        <script src="/assets/magnifier-js/Magnifier.js" defer></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" type="text/css" href="/assets/magnifier-js/magnifier.css">
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
            <x-organomania.selects.organ-builder-select model="filterOrganBuilders" :organBuilders="$this->organBuilders" small multiple live counts />
            <div class="form-text">
                {{ __('Skupiny') }}:
                @foreach ($this->organBuilderGroups as $name => $organBuilderIds)
                    <a class="text-decoration-none" href="#" onclick="return setFilterOrganBuilders({{ Js::from($organBuilderIds) }})">{{ $name }}</a>@if (!$loop->last), @endif
                @endforeach
            </div>
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
            {{ __('Zobrazeno') }}
            <span class="fw-semibold">{{ $this->cases->count() }}</span>
            {{ Helpers::declineCount($this->cases->count(), __('fotografií'), __('fotografie'), __('fotografie')) }}
        </p>

        @if ($this->showCaseParts)
            <x-organomania.case-parts class="mt-4 mb-4" />
        @endif

        @if ($this->showCollapseAll)
            <div class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" onclick="collapseAll()">
                    <i class="bi-chevron-contract"></i>
                    {{ __('Sbalit všechny skupiny') }}
                </button>
            </div>
        @endif

        @foreach ($this->casesGroups as $groupId => $cases)
            <div class="group-container border rounded p-2 p-md-3 my-4">
                <h4 class="d-flex align-items-center fs-5 my-2 my-md-0" wire:key="{{ "organBuilder$groupId" }}">
                    <span class="me-1">
                        @switch($groupBy)
                            @case('organBuilder')
                                @isset($cases[0]->organBuilder)
                                    <x-organomania.organ-builder-link :organBuilder="$cases[0]->organBuilder" :newTab="true" :iconLink="false" />
                                @else
                                    {{ __('Ostatní varhanáři') }}
                                @endisset
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

                        <span class="badge text-bg-secondary rounded-pill ms-1" style="font-size: 55%;">
                            {{ count($cases) }}
                        </span>

                        @switch($groupBy)
                            @case('organBuilder')
                                @isset($cases[0]->organBuilder)
                                    <span class="d-block mt-1 fw-normal text-secondary lh-base" style="font-size: 65%">
                                        {{ $cases[0]->organBuilder->active_period }} ({{ $cases[0]->organBuilder->municipalityWithoutParenthesis }})
                                    </span>
                                @endisset
                                @break

                            @case('caseCategory')
                                @if ($description = $cases[0]->caseCategory->getDescription())
                                    <span class="d-block mt-1 fw-normal text-secondary lh-base" style="font-size: 65%">{{ $description }}</span>
                                @endif
                                @break
                        @endswitch
                    </span>

                    <span class="ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Sbalit/rozbalit skupinu') }}">
                        <button type="button" class="btn btn-sm collapse-btn btn-outline-secondary ms-1 rounded-pill" data-bs-toggle="collapse" href="#group{{ $groupId }}" onclick="collapseBtnOnclick(this)">
                            <i class="bi-chevron-contract"></i>
                        </button>
                    </span>
                </h4>

                @if (isset($this->organBuilder?->description) && count($this->filterOrganBuilders ?? []) === 1)
                    <div class="markdown mt-2 small">{!! $this->markdownConvertor->convert($this->organBuilder->description, newTab: true) !!}</div>
                @endif

                <div id="group{{ $groupId }}" class="group flex-wrap flex-row column-gap-4 row-gap-3 mt-3 justify-content-center collapse show">
                    @foreach ($cases as $case)
                        <div class="text-center">
                            <a href="{{ $case->imageUrl }}" target="_blank">
                                <div
                                    class="position-relative d-inline-block"
                                    @if ($title = $this->getCaseTitle($case))
                                        title="{{ $title }}"
                                    @endif
                                >
                                    <img 
                                        src="{{ ThumbnailController::getThumbnailUrl($case->imageUrl) }}" 
                                        alt="{{ $case->name }} &ndash; {{ __('varhany') }}"
                                        data-large-img-url="{{ $case->imageUrl }}"
                                        class="case-image rounded border"
                                        loading="lazy"
                                    >
                                </div>
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
                                        @if ($case->organBuilder && $case->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED && $this->groupBy !== 'organBuilder')
                                            <x-organomania.organ-builder-link
                                                :organBuilder="$case->organBuilder"
                                                :name="$organBuilderName"
                                                :showDescription="false"
                                                :newTab="true"
                                                :iconLink="false"
                                            />
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
    
    <p class="small text-center text-secondary">
        <strong>{{ __('Poděkování přispěvatelům') }}</strong>:
        <br />
        Lukáš Dvořák, Jan Fejgl, Jiří Fuks, Filip Harant, Robert Hlavatý, Jaroslav Kocůrek, Kristýna Kosíková, Jiří Krátký, Karel Martínek, Martin Moudrý, Jiří Stodůlka, Štěpán Svoboda, Petr Vacek, Ondřej Valenta a další
    </p>
</div>

@script
<script>
    window.initMagnifier = function () {
        let evt = new EventHelper()
        let m = new Magnifier(evt)

        m.attach({
            thumb: '.case-image',
            mode: 'inside',
            zoom: 1.75,
            zoomable: true,
        })
    }

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

    window.setFilterOrganBuilders = function (organBuilderIds) {
        $wire.set('filterOrganBuilders', organBuilderIds)
        return false
    }
</script>
@endscript