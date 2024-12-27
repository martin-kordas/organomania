<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Route;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;
use App\Enums\OrganBuilderCategory;
use App\Services\MarkdownConvertorService;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public OrganBuilder $organBuilder;

    protected MarkdownConvertorService $markdownConvertor;

    private $showActivePeriodInHeading;

    const
        SESSION_KEY_SHOW_MAP = 'organ-builders.show.show-map',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;

        $this->showActivePeriodInHeading
            = isset($this->organBuilder->active_period)
            && !$this->organBuilder->is_workshop
            && $this->organBuilder->active_period !== 'současnost';
    }

    public function mount()
    {
        if (!request()->hasValidSignature(false)) {
            $this->authorize('view', $this->organBuilder);
        }
    }

    public function rendering(View $view): void
    {
        $title = $this->organBuilder->name;
        // alternativy: varhanářská výroba, výroba varhan
        $type = __($this->organBuilder->is_workshop ? 'varhanářství' : 'varhanář');
        $title .= " - $type";
        $view->title($title);
    }

    #[Computed]
    public function categoryGroups()
    {
        $groups = [];
        foreach ($this->organBuilder->organBuilderCategories as $category) {
            $categoryEnum = $category->getEnum();
            $color = $categoryEnum->getColor();
            $groups[$color] ??= [];
            $groups[$color][] = $categoryEnum;
        }
        return $groups;
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome') || $previousUrl === route('organ-builders.edit', [$this->organBuilder->id])) {
            return route('organ-builders.index');
        }
        return $previousUrl;
    }

    #[Computed]
    private function descriptionHtml()
    {
        $description = $this->markdownConvertor->convert($this->organBuilder->description);
        return trim($description);
    }

    #[Computed]
    private function organBuilderCategoriesGroups()
    {
        return OrganBuilderCategory::getCategoryGroups();
    }

    #[Computed]
    private function municipalityCountry()
    {
        $matches = [];
        if (preg_match('/\((.+)\)/', $this->organBuilder->municipality, $matches)) {
            $country = $matches[1];
            $municipality = trim(preg_replace('/\(.+\)/', '', $this->organBuilder->municipality));
        }
        else {
            $country = null;
            $municipality = $this->organBuilder->municipality;
        }
        return [$municipality, $country];
    }

    #[Computed]
    private function organs()
    {
        $organs = $this->organBuilder->organs->map(
            fn (Organ $organ) => ['isRebuild' => false, 'organ' => $organ, 'year' => $organ->year_built]
        );
        $rebuiltOrgans = $this->organBuilder->organRebuilds->map(
            fn (OrganRebuild $rebuild) => ['isRebuild' => true, 'organ' => $rebuild->organ, 'year' => $rebuild->year_built]
        );

        return $organs
            ->merge($rebuiltOrgans)
            ->sortBy('year');
    }

    #[Computed]
    private function relatedOrganBuilders()
    {
        $relatedOrganBuilderIds = match ($this->organBuilder->id) {
            // Rieger
            59 => [1, 2],
            1 => [59, 2],
            2 => [59, 1],
            // Burkhardt
            28 => [8],
            // Richter
            60 => [4],
            // Silberbauer
            69 => [29],
            // Stark
            8 => [28],
            // Neusser
            50 => [72],
            // Prediger
            55 => [76, 5],
            // Eisenhut
            33 => [77],
            // Hubička
            40 => [67],
            // Paštikové
            53 => [67],
            // Organa
            52 => [7, 47],
            // Jozefy
            42 => [38],
            38 => [42],
            // Schwarz
            68 => [4],
            // Harbich
            37 => [72],
            default => []
        };
        return collect($relatedOrganBuilderIds)->map(
            fn ($organBuilderId) => OrganBuilder::find($organBuilderId)
        );
    }
    
}; ?>

<div class="organ-builder-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2" @if (Auth::user()?->admin) title="ID: {{ $organBuilder->id }}" @endif>
                {{ $organBuilder->name }}
                @if ($this->showActivePeriodInHeading)
                    <span class="text-body-tertiary">({{ $organBuilder->active_period }})</span>
                @endif
                @if (!$organBuilder->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif
            </h3>

            @if (isset($organBuilder->perex))
                <p class="lead">{{ $organBuilder->perex }}</p>
            @endif
        </div>
            
         @if ($organBuilder->image_url || $organBuilder->region)
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    @if ($organBuilder->image_url)
                        <a href="{{ $organBuilder->image_url }}" target="_blank">
                            <img class="organ-img rounded border" src="{{ $organBuilder->image_url }}" @isset($organBuilder->image_credits) title="{{ __('Licence obrázku') }}: {{ $organBuilder->image_credits }}" @endisset height="200" />
                        </a>
                    @endif
                    @if ($organBuilder->region)
                        <img width="100" @class(['region', 'start-0', 'm-2', 'bottom-0', 'position-absolute' => isset($organBuilder->image_url)]) src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" />
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <table class="table">
        <tr>
            <th>{{ __('Místo působení') }}</th>
            <td>
                {{ $this->municipalityCountry[0] }}
                @isset ($this->municipalityCountry[1])
                    <span class="text-secondary">({{ $this->municipalityCountry[1] }})</span>
                @endisset
                @if ($organBuilder->region)
                    <span class="text-secondary">({{ $organBuilder->region->name }})</span>
                @endif
            </td>
        </tr>
        @if (isset($organBuilder->place_of_birth))
            <tr>
                <th>{{ __('Místo narození') }}</th>
                <td>{{ $organBuilder->place_of_birth }}</td>
            </tr>
        @endif
        @if (isset($organBuilder->place_of_death))
        <tr>
            <th>{{ __('Místo úmrtí') }}</th>
            <td>{{ $organBuilder->place_of_death }}</td>
        </tr>
        @endif
        @if (isset($organBuilder->active_period) && !$this->showActivePeriodInHeading)
        <tr>
            <th>{{ __('Období') }}</th>
            <td>{{ $organBuilder->active_period }}</td>
        </tr>
        @endif
        <tr>
            <th>
                {{ __('Kategorie') }}
                @php $nonCustomCategoryIds = $organBuilder->organBuilderCategories->pluck('id') @endphp
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
        @if (!$organBuilder->shouldHideImportance())
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="round($organBuilder->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @endif
        @isset($organBuilder->varhany_net_id)
            <tr>
                <th>
                    {{ __('Katalog') }}
                    <span class="d-none d-md-inline">{{ __('varhanářů') }}</span>
                </th>
                <td>
                    <a class="icon-link icon-link-hover" target="_blank" href="{{ url()->query('http://www.varhany.net/zivotopis.php', ['idv' => $organBuilder->varhany_net_id]) }}">
                        <i class="bi bi-link-45deg"></i>
                        varhany.net
                    </a>
                </td>
            </tr>
        @endisset
        @if (isset($organBuilder->workshop_members))
        <tr>
            <th>{{ __('Členové dílny') }}</th>
            <td class="pre-line">{{ $organBuilder->workshop_members }}</td>
        </tr>
        @endif
        @if (isset($organBuilder->web))
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $organBuilder->web) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if ($organBuilder->organs->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Významné varhany') }}">
                <div class="text-break items-list">
                    @foreach ($this->organs as ['isRebuild' => $isRebuild, 'organ' => $organ, 'year' => $year])
                            <x-organomania.organ-link :organ="$organ" :isRebuild="$isRebuild" :year="$year" />
                            @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
                @if ($this->organs->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id]) }}">
                        <i class="bi bi-music-note-list"></i>
                        {{ __('Zobrazit vše') }}
                        <span class="badge text-bg-secondary rounded-pill">{{ $this->organs->count() }}</span>
                    </a>
                @endif
            </x-organomania.tr-responsive>
        @endif
        @if ($organBuilder->renovatedOrgans->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Opravy') }} / {{ __('restaurování') }}">
                <div class="text-break items-list">
                    @foreach ($organBuilder->renovatedOrgans as $organ)
                        <x-organomania.organ-link :organ="$organ" :year="$organ->year_renovated ?? false" :isRenovation="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if ($this->relatedOrganBuilders->isNotEmpty())
            <tr>
                <th>{{ __('Související varhanáři') }}</th>
                <td>
                    <div class="items-list">
                        @foreach ($this->relatedOrganBuilders as $relatedOrganBuilder)
                            <x-organomania.organ-builder-link :organBuilder="$relatedOrganBuilder" :showActivePeriod="true" />
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    <div>
                </td>
            </tr>
        @endif
        @if (isset($organBuilder->description))
            <x-organomania.tr-responsive title="{{ __('Popis') }}">
                <div class="markdown">{!! $this->descriptionHtml !!}</div>
            </x-organomania.tr-responsive>
        @endif
    </table>
        
    <div class="accordion">
        @isset($organBuilder->region_id)
            <x-organomania.accordion-item
                id="accordion-map"
                class="d-print-none"
                title="{{ __('Mapa') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
            >
                <x-organomania.map-detail :latitude="$organBuilder->latitude" :longitude="$organBuilder->longitude" />
            </x-organomania.accordion-item>
        @endisset
        
        @isset($organBuilder->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                <ul class="list-group list-group-flush small">
                    @foreach (explode("\n", $organBuilder->literature) as $literature1)
                        <li @class(['list-group-item', 'px-0', 'pt-0' => $loop->first, 'pb-0' => $loop->last])>{!! Helpers::formatUrlsInLiterature($literature1) !!}}</li>
                    @endforeach
                </ul>
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
        @can('update', $organBuilder)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}" wire:navigate><i class="bi-pencil"></i> {{ __('Upravit') }}</a>
        @endcan
    </div>
        
    <x-organomania.modals.categories-modal :categoriesGroups="$this->organBuilderCategoriesGroups" :categoryClass="OrganBuilderCategory::class" />
</div>
