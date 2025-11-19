<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Route;
use App\Helpers;
use App\Enums\Region;
use App\Http\Controllers\ThumbnailController;
use App\Models\Competition;
use App\Services\MarkdownConvertorService;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Competition $competition;

    protected MarkdownConvertorService $markdownConvertor;

    const
        SESSION_KEY_SHOW_MAP = 'competitions.show.show-map',
        SESSION_KEY_SHOW_COMPETITION_YEARS = 'competitions.show.competition-years';

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;
    }

    public function mount()
    {
        $this->competition->viewed();
    }

    public function rendering(View $view): void
    {
        $view->title($this->competition->name);
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome')) {
            return route('competitions.index');
        }
        return $previousUrl;
    }

    #[Computed]
    public function images()
    {
        $images = [];
        if ($this->competition->image_url) $images[] = [$this->competition->image_url, $this->competition->image_credits];
        foreach ($this->competition->organs as $organ) {
            if ($organ->outside_image_url) $images[] = [$organ->outside_image_url, $organ->outside_image_credits];
            if ($organ->image_url) $images[] = [$organ->image_url, $organ->image_credits];
        }
        return $images;
    }

    #[Computed]
    public function image()
    {
        if (!empty($this->images)) return $this->images[0];
    }

    #[Computed]
    public function metaDescription()
    {
        if (app()->getLocale() === 'cs') {
            if (isset($this->competition->perex)) return str($this->competition->perex)->replace('*', '')->replaceMatches('/\s+/u', ' ')->limit(200);
        }
    }
    
}; ?>

<div class="organ-builder-show container">  
    @isset($this->metaDescription)
        @push('meta')
            <meta name="description" content="{{ $this->metaDescription }}">
        @endpush
    @endisset
    
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2" @if (Auth::user()?->admin) title="ID: {{ $competition->id }}" @endif>
                {{ $competition->name }}
            </h3>
            
            @if (isset($competition->perex))
                <p class="lead">{{ $competition->perex }}</p>
            @endif
        </div>
        
        @if ($this->image || $this->region)
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    @if ($this->image)
                        <a href="{{ $this->image[0] }}" target="_blank">
                            <img class="organ-img rounded border" src="{{ ThumbnailController::getThumbnailUrl($this->image[0]) }}" @isset($this->image[1]) title="{{ __('Licence obrázku') }}: {{ $this->image[1] }}" @endisset height="200" />
                        </a>
                    @endif
                    @if ($competition->region)
                        <img width="100" class="region position-absolute start-0 m-2 bottom-0" src="{{ Vite::asset("resources/images/regions/{$competition->region_id}.png") }}" />
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <div class="text-center">
        <x-organomania.warning-alert class="d-inline-block mb-3">
            {!! __('Uváděné parametry soutěže vychází z posledního známého ročníku a <strong>nemusí být aktuální</strong>!') !!}
            <br class="d-none d-md-inline" />
            {{ __('Pro aktuální informace navštivte vždy oficiální web soutěže.') }}
        </x-organomania.warning-alert>
    </div>
    
    <table class="table show-table">
        @isset($competition->locality)
            <tr>
                <th>{{ __('Obec') }}</th>
                <td>
                    {{ $competition->locality }}
                    @if ($competition->region && $competition->region->id !== Region::Praha->value)
                        <span class="text-secondary">({{ $competition->region->name }})</span>
                    @endif
                </td>
            </tr>
        @endisset
        @isset($competition->place)
            <tr>
                <th>{{ __('Místo') }}</th>
                <td>{{ $competition->place }}</td>
            </tr>
        @endisset
        @isset($competition->frequency)
            <tr>
                <th>
                    <span class="d-md-none">{{ __('Období') }}</span>
                    <span class="d-none d-md-inline">{{ __('Období konání') }}</span>
                </th>
                <td>
                    {{ $competition->frequency }}
                </td>
            </tr>
        @endisset
        @isset($competition->next_year)
            <tr>
                <th>{{ __('Příští ročník') }}</th>
                <td>
                    <span @class(['mark' => $competition->shouldHighlightNextYear()])>
                        {{ $competition->next_year }}
                    </span>
                </td>
            </tr>
        @endisset
        @if ($competition->organs->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Varhany') }}">
                <div class="text-break items-list">
                    @foreach ($competition->organs as $organ)
                        <x-organomania.organ-link :organ="$organ" :year="$organ->year_built" :showOrganBuilder="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @isset($competition->url)
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $competition->url) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endisset
    </table>

    <h5 class="mt-4">{{ __('Obvyklé soutěžní podmínky') }}</h5>

    <table class="table mb-2">
        <tr>
            <th>{{ __('Mezinárodní soutěž') }}</th>
            <td>
                {{ __($competition->international ? 'Ano' : 'Ne') }}
            </td>
        </tr>
        @isset($competition->max_age)
            <tr>
                <th>{{ __('Maximální věk') }}</th>
                <td>
                    {{ $competition->max_age }}
                </td>
            </tr>
        @endisset
        @if (isset($competition->participation_fee) || isset($competition->participation_fee_eur))
            <tr>
                <th>{{ __('Účastnický poplatek') }}</th>
                <td>
                    @isset($competition->participation_fee)
                        {!! Helpers::formatCurrency($competition->participation_fee) !!}
                        @isset($competition->participation_fee_eur)
                            <br />
                            ({!! Helpers::formatCurrency($competition->participation_fee_eur, 'EUR') !!})
                        @endisset
                    @else
                        {!! Helpers::formatCurrency($competition->participation_fee_eur, 'EUR') !!}
                    @endisset
                </td>
            </tr>
        @endif
        @isset($competition->first_prize)
            <tr>
                <th>{{ __('1. cena') }}</th>
                <td>
                    @if ($competition->first_prize > 0)
                        {!! Helpers::formatCurrency($competition->first_prize) !!}
                    @else
                        &ndash;
                    @endif
                </td>
            </tr>
        @endisset
    </table>

    <div class="small text-secondary text-end mb-4">
        {{ __('Zobrazeno') }}: {{ Helpers::formatNumber($competition->views) }}&times;
    </div>
        
    @if (count($this->images) > 1)
        <x-organomania.gallery-carousel :images="$this->images" class="mb-4" />
    @endif
    
    <div class="accordion">
        @if ($competition->competitionYears->isNotEmpty())
            <x-organomania.accordion-item
                id="accordion-map"
                class="d-print-none"
                title="{{ __('Soutěžní ročníky') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_COMPETITION_YEARS)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_COMPETITION_YEARS }}')"
            >
                <ul class="nav nav-tabs" id="competitionYearsTabs" role="tablist">
                    @foreach ($competition->competitionYears as $competitionYear)
                        <li class="nav-item" role="presentation">
                            <button
                                @class(['nav-link', 'active' => $loop->first])
                                id="competitionYearTab{{ $competitionYear->id }}"
                                data-bs-toggle="tab" data-bs-target="#competitionYearContent{{ $competitionYear->id }}"
                                type="button"
                                role="tab" aria-controls="competitionYearContent{{ $competitionYear->id }}" aria-selected="true"
                            >
                                {{ $competitionYear->year }}
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content mt-3" id="competitionYearsContent">
                    @foreach ($competition->competitionYears as $competitionYear)
                        <div
                            @class(['tab-pane', 'fade', 'show' => $loop->first, 'active' => $loop->first])
                            id="competitionYearContent{{ $competitionYear->id }}"
                            role="tabpanel" aria-labelledby="competitionYearTab{{ $competitionYear->id }}" tabindex="0"
                        >
                            <div class="markdown">{!! trim($this->markdownConvertor->convert($competitionYear->description)) !!}</div>
                        </div>
                    @endforeach
                </div>
            </x-organomania.accordion-item>
        @endif
        
        <x-organomania.accordion-item
            id="accordion-map"
            title="{{ __('Mapa') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
        >
            <x-organomania.map-detail :latitude="$competition->latitude" :longitude="$competition->longitude" />
        </x-organomania.accordion-item>
    </div>
            
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
        &nbsp;
        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ route('competitions.show', $competition->slug) }}">
            <i class="bi-share"></i> <span class="d-none d-sm-inline">{{ __('Sdílet') }}</span>
        </a>
    </div>
            
    <x-organomania.modals.share-modal />
</div>
