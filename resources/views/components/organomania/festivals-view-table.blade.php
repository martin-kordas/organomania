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
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('name')" />
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('locality')" />
                    <th>{{ __('Místo konání') }}</th>
                    <th>{{ __('Kraj') }}</th>
                    <th>{{ __('Varhany') }}</th>
                    <th>{{ __('Období konání') }}</th>
                    <th>{{ __('Web') }}</th>
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('importance')" />
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($this->organs as $festival)
                    <tr>
                        <td class="table-light">
                          <strong>{{ $festival->name }}</strong>
                        </td>
                        <td>
                            <strong>{{ $festival->locality }}</strong>
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
                            {{ $festival->frequency }}
                        </td>
                        <td>
                            @isset($festival->url)
                                <a href="{{ $festival->url }}" target="_blank">
                                    {{ Helpers::formatUrl($festival->url) }}
                                </a>
                            @endisset
                        </td>
                        <td>
                            <x-organomania.stars class="responsive" :count="round($festival->importance / 2)" :showCount="true" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
