<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Competition;
use App\Helpers;

new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    use WithPagination;

    public function rendering(View $view): void
    {
        $view->title(__('Soutěže'));
    }

    public function updatedPage()
    {
        $this->dispatch("bootstrap-rendered");
    }

}; ?>

<div class="competitions">
    <div class="container px-0 table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th colspan="6">&nbsp;</th>
                    <th colspan="3" class="table-light text-center fw-bold small">{{ __('Obvyklé soutěžní podmínky') }}</th>
                    <th colspan="2">&nbsp;</th>
                </tr>  
                <tr>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('name')" :sticky="true" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('locality')" />
                    <th>{{ __('Kraj') }}</th>
                    <th>{{ __('Období') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('next_year')" />
                    <th class="text-center">{!! __('Mezi&shy;národní') !!}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('max_age')" class="table-light" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('participation_fee')" class="table-light" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('first_prize')" class="table-light" />
                    <th>{{ __('Web') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($this->organs as $competition)
                    <tr>
                        <td class="table-light position-sticky start-0">
                            <a class="fw-semibold link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($competition) }}" wire:navigate>
                                {{ $competition->name }}
                            </a>
                        </td>
                        <td class="fw-semibold">
                            {{ $competition->locality }}
                        </td>
                        <td>
                            @isset($competition->region_id)
                                <span data-bs-toggle="tooltip" data-bs-title="{{ $competition->region->name }}">
                                <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$competition->region_id}.png") }}" />
                                </span>
                            @endisset
                        </td>
                        <td>
                            {{ $competition->frequency }}
                        </td>
                        <td>
                            <span @class(['mark' => $competition->shouldHighlightNextYear()])>
                                {{ $competition->next_year }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($competition->international)
                                <i class="bi bi-check-lg"></i>
                            @endif
                        </td>
                        <td>
                            {{ $competition->max_age }}
                        </td>
                        <td class="text-end">
                            @isset($competition->participation_fee)
                                {!! Helpers::formatCurrency($competition->participation_fee) !!}
                            @endisset
                        </td>
                        <td class="text-end">
                            @isset($competition->first_prize)
                                @if ($competition->first_prize > 0)
                                    {!! Helpers::formatCurrency($competition->first_prize) !!}
                                @else
                                    &ndash;
                                @endif
                            @endisset
                        </td>
                        <td>
                            @isset($competition->url)
                                <a href="{{ $competition->url }}" target="_blank">
                                    {{ str(Helpers::formatUrl($competition->url))->limit(20) }}
                                </a>
                            @endisset
                        </td>
                        <td class="text-nowrap">
                            <x-organomania.entity-page-view-table-buttons :record="$competition" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
