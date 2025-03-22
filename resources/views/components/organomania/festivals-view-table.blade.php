<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Festival;
use App\Helpers;

// TODO: proč je to Livewire komponenta? organs-view-table není
new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    use WithPagination;

    public function rendering(View $view): void
    {
        $view->title(__('Festivaly'));
    }

    public function updatedPage()
    {
        $this->dispatch("bootstrap-rendered");
    }

}; ?>

<div class="festivals">
    <div class="container px-0 table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('name')" :sticky="true" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('locality')" />
                    <th>{{ __('Místo konání') }}</th>
                    @if ($this->hasDistance)
                        <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('distance')" />
                    @endif
                    <th>{{ __('Kraj') }}</th>
                    <th>{{ __('Varhany') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('starting_month')" />
                    <th>{{ __('Web') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('importance')" />
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($this->organs as $festival)
                    <tr>
                        <td class="table-light position-sticky start-0">
                            <a class="fw-semibold link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($festival) }}" wire:navigate>
                                {{ $festival->name }}
                            </a>
                        </td>
                        <td class="fw-semibold">
                            {{ $festival->locality }}
                        </td>
                        <td>
                            {{ $festival->place }}
                        </td>
                        @if ($this->hasDistance)
                            <td class="text-end">
                                @if (!$this->isFilterNearCenter($festival))
                                    {{ Helpers::formatNumber($festival->distance / 1000, decimals: 1) }}&nbsp;<span class="text-body-secondary">km</span>
                                    <i class="bi bi-arrow-up d-inline-block text-info" style="transform: rotate({{ $festival->angle }}deg)"></i>
                                @endif
                            </td>
                        @endif
                        <td>
                            @isset($festival->region_id)
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $festival->region->name }}">
                                    <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$festival->region_id}.png") }}" />
                                </span>
                            @endisset
                        </td>
                        <td>
                            @isset($festival->organ)
                                <x-organomania.organ-organ-builder-link :organ="$festival->organ" :showIcon="false" />
                            @endisset
                        </td>
                        <td>
                            <span @class(['mark' => $festival->shouldHighlightFrequency()])>
                                {{ $festival->frequency }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            @isset($festival->url)
                                <a class="icon-link icon-link-hover align-items-start" href="{{ $festival->firstUrl }}" target="_blank">
                                    {{ __('přejít') }} <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            @endisset
                        </td>
                        <td>
                            <x-organomania.stars class="responsive" countAll="3" :count="$festival->importance" :showCount="true"  />
                        </td>
                        <td class="text-nowrap">
                            <x-organomania.entity-page-view-table-buttons :record="$festival" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
