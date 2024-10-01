<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Route;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderCategory;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public OrganBuilder $organBuilder;

    const
        SESSION_KEY_SHOW_MAP = 'organ-builders.show.show-map',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function mount()
    {
        if (!request()->hasValidSignature()) {
            $this->authorize('view', $this->organBuilder);
        }
    }

    public function rendering(View $view): void
    {
        $view->title($this->organBuilder->name);
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
        if ($previousUrl === route('organ-builders.edit', [$this->organBuilder->id])) {
            return route('organ-builders.index');
        }
        return $previousUrl;
    }
    
}; ?>

<div class="organ-builder-show container">
    @if ($organBuilder->region)
        <img class="float-end ms-2 mb-2" src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" width="110" />
    @endif
        
    <h3>
        {{ $organBuilder->name }}
        @if (!$organBuilder->isPublic())
            <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
        @endif
    </h3>
    
    @if (isset($organBuilder->perex))
        <p class="lead">{{ $organBuilder->perex }}</p>
    @endif
    
    <table class="table">
        <tr>
            <th>{{ __('Místo působení') }}</th>
            <td>
                {{ $organBuilder->municipality }}
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
        @if (isset($organBuilder->active_period))
        <tr>
            <th>{{ __('Období působení') }}</th>
            <td>{{ $organBuilder->active_period }}</td>
        </tr>
        @endif
        <tr>
            <th>{{ __('Kategorie') }}</th>
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
        @if (isset($organBuilder->web))
        <tr>
            <th>{{ __('Web') }}</th>
            <td>
                <a href="{{ $organBuilder->web }}" target="_blank">{{ $organBuilder->web }}</a>
            </td>
        </tr>
        @endif
        @if ($organBuilder->organs->isNotEmpty())
            <tr>
                <th>{{ __('Významné varhany') }}</th>
                <td>
                    @foreach ($organBuilder->organs as $organ)
                        <x-organomania.organ-link :organ="$organ" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </td>
            </tr>
        @endif
        @if (isset($organBuilder->workshop_members))
        <tr>
            <th>{{ __('Členové dílny') }}</th>
            <td class="pre-line">{{ $organBuilder->workshop_members }}</td>
        </tr>
        @endif
        @if (isset($organBuilder->description))
        <tr class="d-none d-md-table-row">
            <th>{{ __('Popis') }}</th>
            <td>{{ $organBuilder->description }}</td>
        </tr>
        <tr class="d-md-none">
            <td colspan="2">
                <strong>{{ __('Popis') }}</strong>
                <br />
                {{ $organBuilder->description }}
            </td>
        </tr>
        @endif
    </table>
        
    <div class="accordion">
        <x-organomania.accordion-item
            id="accordion-map"
            title="{{ __('Mapa') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
        >
            <x-organomania.map-detail :latitude="$organBuilder->latitude" :longitude="$organBuilder->longitude" />
        </x-organomania.accordion-item>
        
        @isset($organBuilder->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                @foreach (explode("\n", $organBuilder->literature) as $literature1)
                    <p @class(['mb-0' => $loop->last])>{{ $literature1 }}</p>
                @endforeach
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> Zpět</a>&nbsp;
        @can('update', $organBuilder)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}" wire:navigate><i class="bi-pencil"></i> Upravit</a>
        @endcan
    </div>
</div>
