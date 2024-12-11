<?php

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use App\Enums\OrganCategory;
use App\Enums\Region;
use App\Services\MarkdownConvertorService;
use App\Models\Disposition;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Organ $organ;

    protected MarkdownConvertorService $markdownConvertor;

    const
        SESSION_KEY_SHOW_MAP = 'organs.show.show-map',
        SESSION_KEY_SHOW_DISPOSITION = 'organs.show.show-disposition',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function mount()
    {
        if (!request()->hasValidSignature(false)) {
            $this->authorize('view', $this->organ);
        }
    }

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;

        $this->organ->load(['dispositions' => function (HasMany $query) {
            if (request()->hasValidSignature(false))
                $query->withoutGlobalScope(OwnedEntityScope::class);
        }]);
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
        if ($this->organ->outside_image_url) $images[] = [$this->organ->outside_image_url, $this->organ->outside_image_credits] ;
        return $images;
    }
    
    #[Computed]
    public function discs()
    {
        if (($this->organ->discography ?? '') !== '') {
            return str($this->organ->discography)
                ->explode("\n")
                ->map(function ($discStr) {
                    [$name, $url] = explode('#', $discStr);
                    return [trim($name), trim($url)];
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
    private function dispositionColumnsCount()
    {
        $linesCount = str($this->organ->disposition ?? '')->substrCount("\n");
        return match (true) {
            $linesCount > 50 => 3,
            $linesCount > 25 => 2,
            default => 1
        };
    }

    #[Computed]
    public function organCategoriesGroups()
    {
        return OrganCategory::getCategoryGroups();
    }

    private function getDispositionUrl(Disposition $disposition)
    {
        $fn = !Gate::allows('view', $disposition) ? URL::signedRoute(...) : route(...);
        $relativeUrl = $fn('dispositions.show', $disposition->slug, absolute: false);
        return url($relativeUrl);
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
            default => []
        };
        return collect($relatedOrganIds)->map(
            fn ($organId) => Organ::find($organId)
        );
    }

}; ?>

<div class="organ-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="lh-sm fw-normal">
                <strong>{{ $organ->municipality }}</strong>
                <br />
                {{ $organ->place }}
                @if (!$organ->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif
                @if ($organ->region->id !== Region::Praha->value)
                    <br />
                    <small class="text-secondary position-relative" style="font-size: var(--bs-body-font-size); top: -6px;">
                        ({{ $organ->region->name }})
                    </small>
                @endif
            </h3>

            @if (isset($organ->perex))
                <p class="lead">{{ $organ->perex }}</p>
            @endif
        </div>
        
        <div class="text-center">
            <div class="position-relative d-inline-block">
                @if ($organ->image_url)
                    <a href="{{ $organ->image_url }}" target="_blank">
                        <img class="organ-img rounded border" src="{{ $organ->image_url }}" @isset($organ->image_credits) title="{{ __('Licence obrázku') }}: {{ $organ->image_credits }}" @endisset height="200" />
                    </a>
                @endif
                <img width="100" class="region position-absolute start-0 m-2 bottom-0" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
            </div>
        </div>
    </div>
    
    <table class="table mb-4">
        <tr>
            <th>{{ __('Varhanář') }}</th>
            <td>
                <div class="items-list">
                    @if ($organ->organBuilder)
                        @php $showYearBuilt = $organ->organRebuilds->isNotEmpty(); @endphp
                        <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" :yearBuilt="$showYearBuilt ? $organ->year_built : null" />
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
                <th>{{ __('Rok stavby') }}</th>
                <td>{{ $organ->year_built }}</td>
            </tr>
        @endif
        @if ($organ->renovationOrganBuilder)
            <tr>
                <th>{{ __('Oprava') }} /<br />{{ __('restaurování') }}</th>
                <td>
                    <x-organomania.organ-builder-link :organBuilder="$organ->renovationOrganBuilder" :yearBuilt="$organ->year_renovated" />
                </td>
            </tr>
        @endif
        @if ($organ->manuals_count)
            <tr>
                <th>{{ __('Velikost') }}</th>
                <td>
                    {{ $organ->manuals_count }} <small>{{ $organ->getDeclinedManuals() }}</small>
                    @if ($organ->stops_count)
                        <br />
                        {{ $organ->stops_count }} <small>{{ $organ->getDeclinedStops() }}</small>
                    @endif
                </td>
            </tr>
        @endif
        <tr>
            <th>
                {{ __('Kategorie') }}
                @php $nonCustomCategoryIds = $organ->organCategories->pluck('id') @endphp
                @if ($nonCustomCategoryIds->isNotEmpty())
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}">
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
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="round($organ->importance / 2)" :showCount="true" />
            </td>
        </tr>
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
        @if (isset($organ->web))
            <tr>
                <th>
                    <span class="d-none d-md-inline">{{ __('Webové odkazy') }}</span>
                    <span class="d-md-none">{{ __('Web') }}</span>
                </th>
                <td class="text-break">
                    @foreach (explode("\n", $organ->web) as $url)
                        <a class="icon-link icon-link-hover" target="_blank" href="{{ $url }}">
                            <i class="bi bi-link-45deg"></i>
                            {{ str($url)->limit(65) }}
                        </a>
                        @if (!$loop->last) <br /> @endif
                    @endforeach
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
        @if (!empty($this->discs))
            <tr>
                <th>{{ __('Diskografie') }}</th>
                <td>
                    <div class="items-list">
                        @foreach ($this->discs as [$discName, $discUrl])
                            <a class="icon-link icon-link-hover align-items-start link-primary" href="{{ $discUrl }}" target="_blank">
                                <i class="bi bi-disc"></i>
                                <span>{{ $discName }}</span>
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
        @if (isset($organ->description))
            <tr class="d-none d-md-table-row">
                <th>{{ __('Popis') }}</th>
                <td><div class="markdown">{!! $this->descriptionHtml !!}</div></td>
            </tr>
            <tr class="d-md-none">
                <td colspan="2">
                    <strong>{{ __('Popis') }}</strong>
                    <br />
                    <div class="markdown">{!! $this->descriptionHtml !!}</div>
                </td>
            </tr>
        @endif
    </table>
    
    @if (count($this->images) > 1)
        <x-organomania.gallery-carousel :images="$this->images" class="mb-4" />
    @endif
    
    <div class="accordion">
        @if (isset($organ->disposition) || $organ->dispositions->isNotEmpty())
            <x-organomania.accordion-item
                id="accordion-disposition"
                title="{{ __('Disposition_1') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_DISPOSITION)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_DISPOSITION }}')"
            >
                <x-organomania.info-alert>
                    {!! __('<strong>Varhanní dispozice</strong> je souhrnem zvukových a&nbsp;technických vlastností varhan.') !!}
                    {!! __('Kromě seznamu rejstříků (píšťalových řad) a&nbsp;pomocných zařízení může obsahovat i základní technickou charakteristiku varhan.') !!}
                </x-organomania.info-alert>
                
                @if ($organ->dispositions->isNotEmpty())
                    <h5>{{ __('Podrobné interaktivní zobrazení') }}</h5>
                    <div class="list-group">
                        @foreach ($organ->dispositions as $disposition)
                            <a wire:navigate class="icon-link icon-link-hover align-items-start list-group-item list-group-item-primary list-group-item-action link-primary" href="{{ $this->getDispositionUrl($disposition) }}">
                                <i class="bi bi-card-list"></i>
                                <span>
                                    {{ $disposition->name }}
                                    @if (!$disposition->isPublic())
                                        <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
                @isset($organ->disposition)
                    @if ($organ->dispositions->isNotEmpty())
                        <h5 class="mt-4">{{ __('Jednoduché zobrazení') }}</h5>
                    @endif
                    <div class="markdown accordion-disposition" style="column-count: {{ $this->dispositionColumnsCount }}">{!! $this->markdownConvertor->convert($organ->disposition) !!}</div>
                @endisset
            </x-organomania.accordion-item>
        @endif

        <x-organomania.accordion-item
            id="accordion-map"
            title="{{ __('Mapa') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
        >
            <x-organomania.map-detail :latitude="$organ->latitude" :longitude="$organ->longitude" />
        </x-organomania.accordion-item>

        @isset($organ->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                <ul class="list-group list-group-flush small">
                    @foreach (explode("\n", $organ->literature) as $literature1)
                        <li @class(['list-group-item', 'px-0', 'pt-0' => $loop->first, 'pb-0' => $loop->last])>{{ $literature1 }}</li>
                    @endforeach
                </ul>
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="hstack mt-3">
        <a class="btn btn-sm btn-outline-primary" href="{{ route('dispositions.create', ['organId' => $organ->id]) }}">
            <i class="bi-plus-lg"></i> {{ __('Přidat dispozici') }}
        </a>
        
        <a class="btn btn-sm btn-secondary ms-auto" wire:navigate href="{{ $this->previousUrl }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
        @can('update', $organ)
            &nbsp;<a class="btn btn-sm btn-outline-primary" wire:navigate href="{{ route('organs.edit', ['organ' => $organ->id]) }}"><i class="bi-pencil"></i> {{ __('Upravit') }}</a>
        @endcan
    </div>
            
    <x-organomania.modals.categories-modal :categoriesGroups="$this->organCategoriesGroups" :categoryClass="OrganCategory::class" />
</div>
