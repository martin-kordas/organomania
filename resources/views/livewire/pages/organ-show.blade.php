<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\DispositionLanguage;
use App\Enums\OrganCategory;
use App\Enums\Region;
use App\Helpers;
use App\Repositories\AbstractRepository;
use App\Repositories\OrganRepository;
use App\Services\MarkdownConvertorService;
use App\Services\DispositionTextualFormatter;
use App\Services\AI\DescribeDispositionAI;
use App\Services\AI\SuggestRegistrationAI;
use App\Models\Disposition;
use App\Models\Organ;
use App\Models\RegisterName;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\HasRegisterModal;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;
    use HasRegisterModal;

    #[Locked]
    public $organSlug;
    #[Locked]
    public Organ $organ;

    protected MarkdownConvertorService $markdownConvertor;
    protected DispositionTextualFormatter $dispositionFormatter;

    protected OrganRepository $repository;

    #[Locked]
    public bool $signed;

    public $suggestRegistrationDisposition;
    private $suggestRegistrationInfo;
    private $dispositionDescription;
    private DispositionLanguage $dispositionLanguage;

    const
        SESSION_KEY_SHOW_MAP = 'organs.show.show-map',
        SESSION_KEY_SHOW_DISPOSITION = 'organs.show.show-disposition',
        SESSION_KEY_SHOW_SIMILAR_ORGANS = 'organs.show.show-similar-organs',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function mount()
    {
        if (!$this->signed) {
            $this->authorize('view', $this->organ);
        }

        $this->organ->viewed();
    }

    public function boot(OrganRepository $repository, MarkdownConvertorService $markdownConvertor, DispositionTextualFormatter $dispositionFormatter)
    {
        $this->repository = $repository;
        $this->markdownConvertor = $markdownConvertor;
        $this->dispositionLanguage = DispositionLanguage::getDefault();
        $this->dispositionFormatter = $dispositionFormatter;
        $this->dispositionFormatter->setDispositionLanguage($this->dispositionLanguage);

        $this->signed ??= request()->hasValidSignature(false);
        // nepoužíváme klasický route model binding, protože potřebujeme ručně odebrat OwnedEntityScope
        //  - musí to fungovat i Livewire AJAX requestech
        $this->organ = $repository->getBySlug($this->organSlug, $this->signed);

        $this->organ->load([
            'organBuilder' => function (BelongsTo $query) {
                if ($this->signed)
                    $query->withoutGlobalScope(OwnedEntityScope::class);
            },
            'dispositions' => function (HasMany $query) {
                $query->withCount('realDispositionRegisters');
                if ($this->signed)
                    $query->withoutGlobalScope(OwnedEntityScope::class);
            },
        ]);
    }

    public function rendering(View $view): void
    {
        $title = "{$this->organ->municipality}, {$this->organ->place}";
        $title .= ' - ' . __('varhany');
        $view->title($title);
    }

    #[Computed]
    public function categoryGroups()
    {
        $groups = [];
        foreach ($this->organ->organCustomCategories as $category) {
            $groups['custom'] ??= [];
            $groups['custom'][] = $category;
        }

        foreach ($this->organ->organCategories as $category) {
            $categoryEnum = $category->getEnum();
            $color = $categoryEnum->getColor();
            $groups[$color] ??= [];
            $groups[$color][] = $categoryEnum;
        }
        return $groups;
    }
    
    #[Computed]
    public function images()
    {
        $images = [];
        if ($this->organ->image_url) $images[] = [$this->organ->image_url, $this->organ->image_credits];
        if ($this->organ->outside_image_url) $images[] = [$this->organ->outside_image_url, $this->organ->outside_image_credits];
        
        $path = $this->organ->getImageStoragePath();
        $pattern = storage_path("app/public/$path") . '/*.*';
        foreach (File::glob($pattern) as $filename) {
            $imageUrl = "/storage/$path/" . basename($filename);
            $images[] = [$imageUrl, null];
        }

        return $images;
    }
    
    #[Computed]
    public function recordings()
    {
        $recordings = [];
        
        $path = $this->organ->getRecordingStoragePath();
        $pattern = storage_path("app/public/$path") . '/*.*';
        foreach (File::glob($pattern) as $filename) {
            $name = basename($filename);
            $recordingUrl = "/storage/$path/" . $name;
            $recordings[] = [$recordingUrl, $name];
        }

        return $recordings;
    }
    
    #[Computed]
    public function discs()
    {
        if (($this->organ->discography ?? '') !== '') {
            return str($this->organ->discography)
                ->explode("\n")
                ->map(function ($discStr) {
                    [$name, $info, $url] = explode('#', $discStr, 3);

                    $host = parse_url($url, PHP_URL_HOST);
                    if (in_array($host, ['youtube.com', 'youtu.be', 'www.youtube.com'])) $icon = 'youtube';
                    else $icon = 'volume-up';

                    return array_map(trim(...), [$name, $info, $url, $icon]);
                });
        }
        return [];
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome') || $previousUrl === route('organs.edit', [$this->organ->id])) {
            return route('organs.index');
        }
        return $previousUrl;
    }

    #[Computed]
    private function descriptionHtml()
    {
        $description = $this->markdownConvertor->convert($this->organ->description);
        return trim($description);
    }

    #[Computed]
    public function organCategoriesGroups()
    {
        return OrganCategory::getCategoryGroups();
    }

    #[Computed]
    public function similarOrgans()
    {
        return $this->repository->getSimilarOrgans($this->organ);
    }

    private function getDispositionUrl(Disposition $disposition)
    {
        $fn = !Gate::allows('view', $disposition) ? URL::signedRoute(...) : route(...);
        $relativeUrl = $fn('dispositions.show', $disposition->slug, absolute: false);
        return url($relativeUrl);
    }

    #[Computed]
    public function disposition()
    {
        $disposition = $this->suggestRegistrationDisposition ?? $this->organ->disposition;
        return $this->dispositionFormatter->format($disposition, links: true);
    }

    public function exportDispositionAsPdf()
    {
        $disposition = $this->organ->disposition;
        // "podrobnosti viz zdroj" v dispozici odřízneme
        $pos = mb_strrpos($disposition, '---');
        if ($pos !== false) $disposition = mb_substr($disposition, 0, $pos);
        $disposition = $this->dispositionFormatter->format($disposition);

        $filename = __('Disposition_1') . " - {$this->organ->municipality}, {$this->organ->place}.pdf";

        return response()
            ->streamDownload(
                function () use ($disposition) {
                    $pdf = Pdf::loadView('components.organomania.pdf.disposition-textual', [
                        'organ' => $this->organ,
                        'disposition' => $disposition,
                    ]);
                    echo $pdf->stream();
                },
                name: $filename,
                headers: ['Content-Type' => 'application/pdf'],
            );
    }

    #[Computed]
    private function relatedOrgans()
    {
        $relatedOrganIds = match ($this->organ->id) {
            1 => [53],
            53 => [1],
            36 => [142],
            142 => [36],
            38 => [37],
            37 => [38],
            52 => [51],
            51 => [52],
            55 => [122],
            122 => [55],
            6 => [68],
            68 => [6],
            86 => [87],
            87 => [86],
            75 => [76],
            76 => [75],
            88 => [166],
            166 => [88],
            46 => [58],
            default => []
        };
        return collect($relatedOrganIds)->map(
            fn ($organId) => Organ::find($organId)
        );
    }

    #[Computed]
    private function timelineUrl()
    {
        return route('organ-builders.index', [
            'filterId' => $this->organ->organBuilder->id,
            'viewType' => 'timeline',
            'selectedTimelineEntityType' => 'organ',
            'selectedTimelineEntityId' => $this->organ->id
        ]);
    }

    private function highlightSuggestedRegisters(string $disposition, array $registerRowNumbers)
    {
        return str($disposition)->explode("\n")->mapWithKeys(function ($row, $key) use ($registerRowNumbers) {
            $rowNumber = $key + 1;
            $row = trim($row);
            if (in_array($rowNumber, $registerRowNumbers)) $row = "=={$row}==";
            return [$key => $row];
        })->implode("\n");
    }

    public function suggestRegistration(string $piece)
    {
        Gate::authorize('useAI');
        $disposition = $this->organ->disposition ?? throw new \RuntimeException;

        // appendix odmažeme
        $pos = str($disposition)->position(DispositionTextualFormatter::APPENDIX_DELIMITER);
        if ($pos !== false) {
            $appendix = str($disposition)->substr($pos);
            $disposition = str($disposition)->substr(0, $pos);
        }
        else $appendix = '';
        $disposition = Helpers::normalizeLineBreaks($disposition);

        try {
            $AI = app()->makeWith(SuggestRegistrationAI::class, [
                'disposition' => $disposition,
                'organ' => $this->organ,
            ]);
            $res = $AI->suggest($piece);
            
            $this->suggestRegistrationDisposition = $this->highlightSuggestedRegisters($disposition, $res['registerRowNumbers']);
            $this->suggestRegistrationDisposition .= $appendix;
            $this->suggestRegistrationInfo = $res['recommendations'];
        }
        catch (\Exception $ex) {
            $this->js('showToast("suggestRegistrationFail")');
        }
    }

    public function describeDisposition()
    {
        Gate::authorize('useAI');
        $disposition = $this->organ->disposition ?? throw new \RuntimeException;
        $disposition = Helpers::normalizeLineBreaks($disposition);

        try {
            $AI = app()->makeWith(DescribeDispositionAI::class, [
                'disposition' => $disposition,
                'organ' => $this->organ,
            ]);
            $this->dispositionDescription = $AI->describe();
        }
        catch (\Exception $ex) {
            $this->js('showToast("describeDispositionFail")');
        }
    }

}; ?>

<div class="organ-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2 mb-3 lh-sm fw-normal" @if (Auth::user()?->admin) title="ID: {{ $organ->id }}" @endif>
                {{ $organ->municipality }}
                <br />
                <span class="fs-4">{{ $organ->place }}</span>
                @if (!$organ->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif
                @if ($organ->region->id !== Region::Praha->value)
                    <br />
                    <small class="text-secondary position-relative" style="font-size: var(--bs-body-font-size); top: -4px;">
                        {{ $organ->region->name }}
                    </small>
                @endif
            </h3>

            @if (isset($organ->perex))
                <p class="lead">{{ $organ->perex }}</p>
            @endif
        </div>
        
        <div class="text-center">
            <div class="position-relative d-inline-block">
                @foreach ($this->images as [$imageUrl, $imageCredits])
                    <a href="{{ $imageUrl }}" target="_blank">
                        <img class="organ-img rounded border" src="{{ $imageUrl }}" @isset($imageCredits) title="{{ __('Licence obrázku') }}: {{ $imageCredits }}" @endisset height="200" />
                    </a>
                    @break
                @endforeach
                <img width="100" @class(['region', 'start-0', 'm-2', 'bottom-0', 'position-absolute' => !empty($this->images)]) src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
            </div>
        </div>
    </div>
    
    <table class="table mb-2">
        <tr>
            <th>{{ __('Varhanář') }}</th>
            <td>
                <div class="items-list">
                    @if ($organ->organBuilder)
                        @php $showYearBuilt = $organ->organRebuilds->isNotEmpty(); @endphp
                        <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" :yearBuilt="$showYearBuilt ? $organ->year_built : null" :signed="$this->signed" />
                        @if (!$showYearBuilt && !$organ->organBuilder->is_workshop && isset($organ->organBuilder->active_period))
                            <span class="text-body-secondary">({{ $organ->organBuilder->active_period }})</span>
                        @endif
                        @foreach ($organ->organRebuilds as $rebuild)
                            @if ($rebuild->organBuilder)
                                <br />
                                <x-organomania.organ-builder-link :organBuilder="$rebuild->organBuilder" :yearBuilt="$rebuild->year_built" :isRebuild="true" />
                            @endif
                        @endforeach
                    @else
                        {{ __('neznámý') }}
                    @endif
                </div>
            </td>
        </tr>
        @if ($organ->year_built)
            <tr>
                <th class="align-middle">{{ __('Rok stavby') }}</th>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-auto">{{ $organ->year_built }}</span>
                        @if (isset($organ->organBuilder?->region_id))
                            <a class="btn btn-sm btn-outline-secondary" href="{{ $this->timelineUrl }}" wire:navigate>
                                <i class="bi bi-clock"></i>
                                {{ __('Časová osa') }}
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endif
        @if ($organ->renovationOrganBuilder || $organ->year_renovated)
            <tr>
                <th>{{ __('Oprava') }} /<br />{{ __('restaurování') }}</th>
                <td>
                    @if (!$organ->renovationOrganBuilder)
                        {{ $organ->year_renovated }}
                    @else
                        <x-organomania.organ-builder-link :organBuilder="$organ->renovationOrganBuilder" :yearBuilt="$organ->year_renovated" />
                    @endif
                </td>
            </tr>
        @endif
        @if ($organ->manuals_count)
            <tr>
                <th>{{ __('Velikost') }}</th>
                <td>
                    <div class="d-flex column-gap-3">
                        @isset($organ->original_manuals_count)
                            <div>
                                {{ __('Původní') }}
                                @isset($organ->year_built) ({{ $organ->year_built }}) @endisset
                                <br />
                                {{ $organ->original_manuals_count }}&nbsp;<small>{{ $organ->getDeclinedManuals(original: true) }}</small>
                                @if ($organ->original_stops_count)
                                    <br />
                                    {{ $organ->original_stops_count }}&nbsp;<small>{{ $organ->getDeclinedStops(original: true) }}</small>
                                @endif
                            </div>

                        @endisset
                        <div>
                            @isset($organ->original_manuals_count)
                                {{ __('Současná') }}
                                <br />
                            @endisset
                            {{ $organ->manuals_count }}&nbsp;<small>{{ $organ->getDeclinedManuals() }}</small>
                            @if ($organ->stops_count)
                                <br />
                                {{ $organ->stops_count }}&nbsp;<small>{{ $organ->getDeclinedStops() }}</small>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <th>
                {{ __('Kategorie') }}
                @php $nonCustomCategoryIds = $organ->organCategories->pluck('id') @endphp
                @if ($nonCustomCategoryIds->isNotEmpty())
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}" onclick="setTimeout(removeTooltips);">
                        <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#categoriesModal" @click="highlightCategoriesInModal(@json($nonCustomCategoryIds))">
                            <i class="bi bi-question-circle"></i>
                        </a>
                    </span>
                @endif
            </th>
            <td>
                @foreach ($this->categoryGroups as $group)
                    @foreach ($group as $category)
                        <x-organomania.category-badge :category="$category" />
                    @endforeach
                    @if (!$loop->last)
                        <br />
                    @endif
                @endforeach
            </td>
        </tr>
        @if (!$organ->shouldHideImportance())
            <tr>
                <th>
                    {{ __('Význam') }}
                    <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#importanceHintModal">
                        <i class="bi bi-question-circle"></i>
                    </a>
                </th>
                <td>
                    <x-organomania.stars :count="round($organ->importance / 2)" :showCount="true" />
                </td>
            </tr>
        @endif
        @if ($organ->festivals->isNotEmpty())
            <tr>
                <th>
                    {{ __('Festivaly') }}
                </th>
                <td>
                    <div class="items-list">
                        @foreach ($organ->festivals as $festival)
                            <a class="icon-link icon-link-hover align-items-start link-primary text-decoration-none" wire:navigate href="{{ route('festivals.show', [$festival->id]) }}">
                                <i class="bi bi-calendar-date"></i>
                                <span>
                                    {{ $festival->name }}
                                    @if (isset($festival->locality) || isset($festival->frequency))
                                        <span class="text-body-secondary">
                                            ({{ collect([$festival->locality ?? null, $festival->frequency ?? null])->filter()->join(', ') }})
                                        </span>
                                    @endif
                                </span>
                            </a>
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
        @if ($organ->competitions->isNotEmpty())
            <tr>
                <th>
                    {{ __('Soutěže') }}
                </th>
                <td>
                    <div class="items-list">
                        @foreach ($organ->competitions as $competition)
                            <a class="icon-link icon-link-hover align-items-start link-primary text-decoration-none" wire:navigate href="{{ route('competitions.show', [$competition->id]) }}">
                                <i class="bi bi-trophy"></i>
                                <span>
                                    {{ $competition->name }}
                                </span>
                            </a>
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
        @if ($this->relatedOrgans->isNotEmpty())
            <tr>
                <th>{{ __('Související varhany') }}</th>
                <td>
                    <div class="items-list">
                        @foreach ($this->relatedOrgans as $relatedOrgan)
                            <x-organomania.organ-link :organ="$relatedOrgan" :year="$relatedOrgan->year_built" :showOrganBuilder="true" />
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
        @isset($organ->varhany_net_id)
            <tr>
                <th>
                    {{ __('Katalog') }}
                    <span class="d-none d-md-inline">{{ __('varhan') }}</span>
                </th>
                <td>
                    <a class="icon-link icon-link-hover" target="_blank" href="{{ url()->query('http://www.varhany.net/cardheader.php', ['lok' => $organ->varhany_net_id]) }}">
                        <i class="bi bi-link-45deg"></i>
                        varhany.net
                    </a>
                </td>
            </tr>
        @endisset
        @if (isset($organ->web))
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $organ->web) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if (isset($organ->place_web))
            <x-organomania.tr-responsive title="{{ __('Web místa') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $organ->place_web) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if (!empty($this->discs) || !empty($this->recordings))
            <x-organomania.tr-responsive title="{{ __('Nahrávky') }}">
                <div class="items-list">
                    <div>
                        @foreach ($this->discs as [$discName, $info, $discUrl, $icon])
                            <a class="icon-link icon-link-hover align-items-start link-primary text-decoration-none" href="{{ $discUrl }}" target="_blank">
                                <i class="bi bi-{{ $icon }}"></i>
                                <span>
                                    <span class="text-decoration-underline">{{ $discName }}</span>
                                    @if ($info !== '')
                                        <span class="text-secondary">({{ $info }})</span>
                                    @endif
                                </span>
                            </a>
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    </div>
                        
                    <div>
                        @foreach ($this->recordings as [$recordingUrl, $name])
                            <a class="icon-link icon-link-hover align-items-start link-primary text-decoration-none" href="{{ $recordingUrl }}" target="_blank">
                                <i class="bi bi-volume-up"></i>
                                <span class="text-decoration-underline">{{ $name }}</span>
                            </a>
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    </div>
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if (isset($organ->description))
            <x-organomania.tr-responsive title="{{ __('Popis') }}">
                <div class="markdown">{!! $this->descriptionHtml !!}</div>
            </x-organomania.tr-responsive>
        @endif
    </table>
    
    <div class="mb-4">
        @if ($organ->isPublic())
            <div class="small text-secondary text-end mb-4">
                {{ __('Zobrazeno') }}: {{ Helpers::formatNumber($organ->views) }}&times;
            </div>
        @endif

        @if (count($this->images) > 1)
            <x-organomania.gallery-carousel :images="$this->images" class="mb-4" />
        @endif
    </div>
    
    <div class="accordion">
        @if (isset($organ->disposition) || $organ->dispositions->isNotEmpty())
            <x-organomania.accordion-item
                id="accordion-disposition"
                class="position-relative"
                title="{{ __('Disposition_1') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_DISPOSITION)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_DISPOSITION }}')"
            >
                
                <x-organomania.info-alert class="mb-2">
                    {!! __('<strong>Varhanní dispozice</strong> je souhrnem zvukových a&nbsp;technických vlastností varhan.') !!}
                    <span class="d-none d-md-inline">
                        {!! __('Kromě seznamu rejstříků (píšťalových řad) a&nbsp;pomocných zařízení může obsahovat i základní technickou charakteristiku varhan.') !!}
                    </span>
                </x-organomania.info-alert>
                
                @if ($organ->dispositions->isNotEmpty())
                    <h5>{{ __('Podrobné interaktivní zobrazení') }}</h5>
                    <div class="list-group">
                        @foreach ($organ->dispositions as $disposition)
                            <a wire:navigate class="icon-link icon-link-hover align-items-start list-group-item list-group-item-primary list-group-item-action link-primary" href="{{ $this->getDispositionUrl($disposition) }}">
                                <i class="bi bi-card-list"></i>
                                <span class="d-flex w-100 column-gap-2">
                                    <span class="me-auto">
                                        {{ $disposition->name }}
                                        @if (!$disposition->isPublic())
                                            <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                                        @endif
                                    </span>
                                    @if ($disposition->real_disposition_registers_count > 0)
                                        <span class="text-secondary">
                                            {{ $disposition->real_disposition_registers_count }}&nbsp;<small>{{ $disposition->getDeclinedRealDispositionRegisters() }}</small>
                                        </span>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
                @isset($organ->disposition)
                    @if ($organ->dispositions->isNotEmpty())
                        <h5 class="mt-4">
                            {{ __('Jednoduché zobrazení') }}
                        </h5>
                    @endif
                        
                    <x-organomania.info-alert class="mb-2">
                        <span class="d-none d-sm-inline">
                            {!! __('Jednotlivé rejstříky jsou popsány v') !!} <a class="link-primary text-decoration-none" href="{{ route('dispositions.registers.index') }}" target="_blank">{{ __('Encyklopedii rejstříků') }}</a>.
                        </span>
                        <span class="d-sm-none">
                            {!! __('Rejstříky jsou popsány v') !!} <a class="link-primary text-decoration-none" href="{{ route('dispositions.registers.index') }}" target="_blank">{{ __('Encyklopedii') }}</a>.
                        </span>
                    </x-organomania.info-alert>
                        
                    <div class="mb-3 lh-lg">
                        <span class="position-relative" style="top: 2px">
                            {{ __('AI') }}
                            <span class="d-none d-sm-inline">{{ __('funkce') }}</span>
                        </span>
                        <span class="ms-1" data-bs-toggle="tooltip" data-bs-title="{{ __('Charakterizovat dispozici a popsat důležité rejstříky') }}">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                @can('useAI')
                                    wire:click="describeDisposition"
                                @else
                                    data-bs-toggle="modal"
                                    data-bs-target="#premiumModal"
                                @endcan
                            >
                                <span wire:loading.remove wire:target="describeDisposition">
                                    <i class="bi-magic"></i>
                                </span>
                                <span wire:loading wire:target="describeDisposition">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                                </span>
                                {{ __('Popis dispozice') }}
                            </button>
                        </span>
                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Naregistrovat zadanou skladbu s pomocí umělé inteligence') }}" onclick="setTimeout(removeTooltips);">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                @can('useAI')
                                    data-bs-toggle="modal"
                                    data-bs-target="#suggestRegistrationModal"
                                @else 
                                    data-bs-toggle="modal"
                                    data-bs-target="#premiumModal"
                                @endcan
                            >
                                <span wire:loading.remove wire:target="suggestRegistration">
                                    <i class="bi-magic"></i>
                                </span>
                                <span wire:loading wire:target="suggestRegistration">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                                </span>
                                <span class="d-none d-sm-inline">{{ __('Naregistrovat skladbu') }}</span>
                                <span class="d-sm-none">{{ __('Registrace') }}</span>
                            </button>
                        </span>
                        
                        <span class="float-end position-relative">
                            <button
                                type="button"
                                class="btn btn-sm px-1"
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ __('Exportovat dispozici do PDF') }}"
                                wire:click="exportDispositionAsPdf"
                                style="font-size: 115%"
                            >
                                <i class="bi-file-pdf"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm px-1"
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ __('Kopírovat dispozici do schránky') }}"
                                @click="copyDispositionToCliboard()"
                                style="font-size: 115%"
                            >
                                <i class="bi-copy"></i>
                            </button>
                        </span>
                    </div>
                        
                    <div class="position-relative" style="clear: both">
                        <div wire:loading.block wire:target="suggestRegistration, describeDisposition" wire:loading.class="opacity-75" class="position-absolute text-center bg-white w-100 h-100" style="z-index: 10;">
                            <x-organomania.spinner class="align-items-center h-100" :margin="false" />
                        </div>
                        <div @class(['markdown', 'accordion-disposition', 'm-auto' => $organ->getDispositionColumnsCount() > 1]) style="column-count: {{ $organ->getDispositionColumnsCount() }}">{!! $this->disposition !!}</div>
                    </div>
                @endisset
            </x-organomania.accordion-item>
        @endif

        @if ($organ->latitude > 0)
            <x-organomania.accordion-item
                id="accordion-map"
                class="d-print-none"
                title="{{ __('Mapa') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
            >
                <x-organomania.map-detail :latitude="$organ->latitude" :longitude="$organ->longitude" />
                <div class="mt-2">
                    {{ __('Zobrazit zajímavé varhany v okruhu') }}:
                    @foreach ([25, 50] as $distance)
                        <a
                            class="link-primary text-decoration-none"
                            href="{{ route('organs.index', ['filterNearLatitude' => $this->organ->latitude, 'filterNearLongitude' => $this->organ->longitude, 'filterNearDistance' => $distance, 'viewType' => 'map']) }}"
                            wire:navigate
                        >
                            {{ $distance }}&nbsp;km
                            @if (!$loop->last) | @endif
                        </a>
                    @endForeach
                </div>
            </x-organomania.accordion-item>
        @endif
        
        @if ($this->similarOrgans->isNotEmpty())
            <x-organomania.accordion-item
                id="accordion-similarOrgans"
                title="{{ __('Podobné varhany') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_SIMILAR_ORGANS)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_SIMILAR_ORGANS }}')"
            >
                <small class="text-secondary">
                    {{ __('Za podobné považujeme varhany přibližně stejné velikosti, postavené v tomtéž období a patřící do stejných kategorií podle typu a stavby.') }}
                </small>
                <div class="items-list mt-2">
                    @foreach ($this->similarOrgans as $similarOrgan)
                        <x-organomania.organ-link :organ="$similarOrgan" :year="$similarOrgan->year_built" :showOrganBuilder="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.accordion-item>
        @endif

        @isset($organ->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                <ul class="list-group list-group-flush small">
                    @foreach (explode("\n", $organ->literature) as $literature1)
                        <li @class(['list-group-item', 'px-0', 'pt-0' => $loop->first, 'pb-0' => $loop->last])>{!! Helpers::formatUrlsInLiterature($literature1) !!}</li>
                    @endforeach
                </ul>
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="hstack mt-3">
        <a class="btn btn-sm btn-outline-primary" href="{{ route('dispositions.create', ['organId' => $organ->id]) }}">
            <i class="bi-plus-lg"></i> {{ __('Přidat dispozici') }}
        </a>
        
        <a class="btn btn-sm btn-secondary ms-auto me-1" wire:navigate href="{{ $this->previousUrl }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
        @can('update', $organ)
            &nbsp;
            <a class="btn btn-sm btn-outline-primary" wire:navigate href="{{ route('organs.edit', ['organ' => $organ->id]) }}">
                <i class="bi-pencil"></i> <span class="d-none d-sm-inline">{{ __('Upravit') }}</span>
            </a>
        @endcan
        &nbsp;
        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $organ->getShareUrl() }}">
            <i class="bi-share"></i> <span class="d-none d-sm-inline">{{ __('Sdílet') }}</span>
        </a>
    </div>
            
    <x-organomania.modals.categories-modal :categoriesGroups="$this->organCategoriesGroups" :categoryClass="OrganCategory::class" />
        
    <x-organomania.modals.register-modal
        :registerName="$registerName"
        :pitch="$pitch"
        :language="$this->dispositionLanguage"
        :excludeOrganIds="[$organ->id]"
    />
        
    @isset($this->suggestRegistrationInfo)
        <x-organomania.toasts.ai-info-toast title="{{ __('Podrobnosti k registraci') }}">
            <x-organomania.warning-alert class="mb-2 d-print-none">
                {{ __('Buďte obezřetní – umělá inteligence poskytuje nepřesné výsledky.') }}
            </x-organomania.warning-alert>
            {!! trim($this->markdownConvertor->convert($this->suggestRegistrationInfo)) !!}
        </x-organomania.toasts.ai-info-toast>
    @endisset
    @isset($this->dispositionDescription)
        <x-organomania.toasts.ai-info-toast title="{{ __('Popis dispozice') }}">
            <x-organomania.warning-alert class="mb-2 d-print-none">
                {{ __('Buďte obezřetní – umělá inteligence poskytuje nepřesné výsledky.') }}
            </x-organomania.warning-alert>
            {!! trim($this->markdownConvertor->convert($this->dispositionDescription)) !!}
        </x-organomania.toasts.ai-info-toast>
    @endisset

    <x-organomania.toast toastId="suggestRegistrationFail" color="danger">
        {{ __('Omlouváme se, při zjišťování registrace došlo k chybě.') }}
    </x-organomania.toast>
    <x-organomania.toast toastId="describeDispositionFail" color="danger">
        {{ __('Omlouváme se, při zjišťování popisu dispozice došlo k chybě.') }}
    </x-organomania.toast>
        
    <x-organomania.toast toastId="dispositionCopied">
        {{ __('Dispozice byla úspěšně zkopírována do schránky.') }}
    </x-organomania.toast>

    <x-organomania.modals.share-modal :hintAppend="__('Sdílením varhan sdílíte i jejich varhanáře a dispozice.')" />
    <x-organomania.modals.suggest-registration-modal />
    <x-organomania.modals.premium-modal />
        
    <x-organomania.modals.importance-hint-modal :title="__('Význam varhan')">
        {{ __('Význam varhan se eviduje, aby bylo možné nástroje přibližně seřadit podle důležitosti.') }}
        {{ __('Význam je určen hrubým odhadem na základě řady kritérií a nejde o hodnocení kvality varhan.') }}
    </x-organomania.modals.importance-hint-modal>
</div>

@script
<script>
    window.copyDispositionToCliboard = async function () {
        // TODO: kopíruje to bez mezer mezi manuály
        let disposition = $('.accordion-disposition').text()
        await navigator.clipboard.writeText(disposition)
        showToast('dispositionCopied')
    }
</script>
@endscript