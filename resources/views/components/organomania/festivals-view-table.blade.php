<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Festival;
use App\Helpers;

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
                            <a class="fw-semibold link-dark link-underline-opacity-10 link-underline-opacity-50-hover" href="{{ $this->getViewUrl($festival) }}" wire:navigate>
                                {{ $festival->name }}
                            </a>
                        </td>
                        <td class="fw-semibold">
                            {{ $festival->locality }}
                        </td>
                        <td>
                            {{ $festival->place }}
                        </td>
                        <td>
                            @isset($festival->region_id)
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $festival->region->name }}">
                                    <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$festival->region_id}.png") }}" />
                                </span>
                            @endisset
                        </td>
                        <td>
                            @isset($festival->organ)
                                <x-organomania.organ-organ-builder-link :organ="$festival->organ" />
                            @endisset
                        </td>
                        <td>
                            <span @class(['mark' => $festival->shouldHighlightFrequency()])>
                                {{ $festival->frequency }}
                            </span>
                        </td>
                        <td>
                            @isset($festival->url)
                                <a href="{{ $festival->url }}" target="_blank">
                                    {{ str(Helpers::formatUrl($festival->url))->limit(20) }}
                                </a>
                            @endisset
                        </td>
                        <td>
                            <x-organomania.stars class="responsive" :count="round($festival->importance / 2)" :showCount="true" />
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
