<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\OrganBuilder;

new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    use WithPagination;

    public function rendering(View $view): void
    {
        $view->title(__('Varhanáři'));
    }

    public function updatedPage()
    {
        $this->dispatch("bootstrap-rendered");
    }

}; ?>

<div class="organ-builders">
    <div class="organ-builders-container container table-responsive px-0">
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('name')" :sticky="true" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('municipality')" />
                    <th>{{ __('Kraj') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('active_from_year')" />
                    <th>{{ __('Kategorie') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('importance')" />
                    <th colspan="2">&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($this->organs as $organBuilder)
                    <tr>
                        <td>
                            @if ($organBuilder->user_id)
                                <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                                    <i class="bi-lock text-warning"></i>
                                </span>
                            @endif
                        </td>
                        <td class="table-light fw-semibold position-sticky start-0">
                            <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($organBuilder) }}" wire:navigate>
                                {{ $organBuilder->name }}
                            </a>
                        </td>
                        <td>{{ $organBuilder->municipality }}</td>
                        <td data-bs-toggle="tooltip" data-bs-title="{{ $organBuilder->region->name }}">
                            <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" />
                        </td>
                        <td>{{ $organBuilder->active_period }}</td>
                        <td>
                            @foreach ($organBuilder->getGeneralCategories() as $category)
                                <x-organomania.category-badge :category="$category->getEnum()" />
                            @endforeach
                        </td>
                        <td>
                            @if (!$organBuilder->shouldHideImportance())
                                <x-organomania.stars class="responsive" :count="round($organBuilder->importance / 2)" :showCount="true" />
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <x-organomania.entity-page-view-table-buttons :record="$organBuilder" />
                        </td>
                        <td>
                            @if ($organBuilder->organs_count > 0)
                                <a
                                    class="btn btn-sm btn-outline-secondary text-nowrap w-100"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ __('Zobrazit varhany tohoto varhanáře') }}"
                                    href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id]) }}"
                                    wire:navigate
                                >
                                    <i class="bi-music-note-list"></i>
                                    <span class="d-none d-xxl-inline">{{ __('Varhany') }}</span>
                                    <span class="badge text-bg-secondary rounded-pill">{{ $organBuilder->organs_count }}</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
