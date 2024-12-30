<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Route;
use App\Models\Festival;
use App\Traits\HasAccordion;
use App\Helpers;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Festival $festival;

    const
        SESSION_KEY_SHOW_MAP = 'festivals.show.show-map';

    public function mount()
    {
        $this->festival->viewed();
    }

    public function rendering(View $view): void
    {
        $view->title($this->festival->name);
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome')) {
            return route('festivals.index');
        }
        return $previousUrl;
    }

    #[Computed]
    public function images()
    {
        $images = [];
        if ($this->festival->image_url) $images[] = [$this->festival->image_url, $this->festival->image_credits];
        if ($this->festival->organ_image_url) $images[] = [$this->festival->organ_image_url, $this->festival->organ_image_credits];
        if ($organ = $this->festival->organ) {
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
    
}; ?>

<div class="organ-builder-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2" @if (Auth::user()?->admin) title="ID: {{ $festival->id }}" @endif>
                {{ $festival->name }}
            </h3>
            
            @if (isset($festival->perex))
                <p class="lead">{{ $festival->perex }}</p>
            @endif
        </div>
        
        @if ($this->image || $festival->region)
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    @if ($this->image)
                        <a href="{{ $this->image[0] }}" target="_blank">
                            <img class="organ-img rounded border" src="{{ $this->image[0] }}" @isset($this->image[1]) title="{{ __('Licence obrázku') }}: {{ $this->image[1] }}" @endisset height="200" />
                        </a>
                    @endif
                    @if ($festival->region)
                        <img width="100" class="region position-absolute start-0 m-2 bottom-0" src="{{ Vite::asset("resources/images/regions/{$festival->region_id}.png") }}" />
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <table class="table mb-2">
        @isset($festival->locality)
            <tr>
                <th>{{ __('Obec') }}</th>
                <td>
                    {{ $festival->locality }}
                    @if ($festival->region)
                        <span class="text-secondary">({{ $festival->region->name }})</span>
                    @endif
                </td>
            </tr>
        @endisset
        @isset($festival->place)
            <tr>
                <th>{{ __('Místo') }}</th>
                <td>{{ $festival->place }}</td>
            </tr>
        @endisset
        @isset($festival->organ)
            <tr>
                <th>{{ __('Varhany') }}</th>
                <td>
                    <x-organomania.organ-organ-builder-link :organ="$festival->organ" />
                </td>
            </tr>
        @endisset
        @isset($festival->frequency)
            <tr>
                <th>
                    <span class="d-md-none">{{ __('Období') }}</span>
                    <span class="d-none d-md-inline">{{ __('Období konání') }}</span>
                </th>
                <td>
                    <span @class(['mark' => $festival->shouldHighlightFrequency()])>
                        {{ $festival->frequency }}
                    </span>
                </td>
            </tr>
        @endisset
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="$festival->importance" countAll="3" :showCount="true" />
            </td>
        </tr>
        @isset($festival->url)
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $festival->url) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endisset
    </table>
    
    <div class="small text-secondary text-end mb-4">
        {{ __('Zobrazeno') }}: {{ Helpers::formatNumber($festival->views) }}&times;
    </div>
        
    @if (count($this->images) > 1)
        <x-organomania.gallery-carousel :images="$this->images" class="mb-4" />
    @endif
            
    <div class="accordion">
        <x-organomania.accordion-item
            id="accordion-map"
            class="d-print-none"
            title="{{ __('Mapa') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
        >
            <x-organomania.map-detail :latitude="$festival->latitude" :longitude="$festival->longitude" />
        </x-organomania.accordion-item>
    </div>
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
    </div>
</div>
