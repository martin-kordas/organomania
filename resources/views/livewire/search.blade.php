<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Models\Organ;
use App\Models\OrganBuilder;
use Illuminate\Database\Eloquent\Builder;

// https://forum.laravel-livewire.com/t/search-with-autocomplete/2966/2
new class extends Component {

    public $search;

    public $minSearchLength = 3;

    private Collection $resultsOrgans;
    private Collection $resultsOrganBuilders;
    private int $resultsCount = 0;
    
    public function updated($property)
    {
        if ($property === 'search') {
            $search = trim($this->search);
            if (mb_strlen($search) >= $this->minSearchLength) {
                $this->resultsOrgans = $this->getOrgans();
                $this->resultsOrganBuilders = $this->getOrganBuilders();
                $this->resultsCount = $this->resultsOrgans->count() + $this->resultsOrganBuilders->count();
            }
        }
    }

    private function getOrgans()
    {
        // https://laravel-news.com/laravel-scout-practical-guide#content-write-a-search-query
        return Organ::search($this->search)
            ->query(function (Builder $builder) {
                $builder
                    ->leftJoin('organ_builders', 'organs.organ_builder_id', 'organ_builders.id')
                    ->select([
                        'organs.id', 'organs.place', 'organs.municipality', 'organs.importance', 'organs.organ_builder_id',
                    ])
                    ->with('organBuilder:id,is_workshop,first_name,last_name,workshop_name')
                    ->orderBy('importance', 'DESC')
                    ->take(5);
            })
            ->get();
    }
    
    private function getOrganBuilders()
    {
        return OrganBuilder::search($this->search)
            ->query(function (Builder $builder) {
                $builder
                    ->orderBy('importance', 'DESC')
                    ->take(5)
                    ->select([
                        'id',
                        'is_workshop', 'workshop_name', 'first_name', 'last_name',
                        'active_period', 'municipality', 'importance'
                    ]);
            })
            ->get()
            ->append(['name']);
    }

}; ?>

<form class="col-12 col-lg-3 mb-3 mb-lg-0 me-lg-3" role="search">
    <div x-data="{isTyped: false}">
        <div class="position-relative">
            <div>
                <input
                    id="search"
                    type="search"
                    class="form-control"
                    placeholder="{{__('Hledat')}}&hellip; (/)"
                    aria-label="{{ __('Hledat') }}"
                    @input.debounce.400ms="isTyped = ($event.target.value != '' && $event.target.value.length >= $wire.minSearchLength)"
                    @keydown.esc="isTyped = false"
                    @click.outside="isTyped = false"
                    @keydown.slash.window.prevent="document.querySelector('#search').focus()"
                    wire:model.live.debounce.350ms="search"
                    autocomplete="off"
                />
            </div>
            <div class="card position-absolute shadow w-100" x-show="isTyped" x-cloak>
                @if ($this->resultsCount > 0)
                    @if ($this->resultsOrgans->isNotEmpty())
                        <div class="card-header fw-bold">
                            <i class="bi-file-music"></i> {{ __('Varhany') }}
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($this->resultsOrgans as $organ)
                                <a class="list-group-item list-group-item-action" href="{{ route('organs.show', ['organ' => $organ->id]) }}">
                                    {{ $organ->municipality }}, {{ $organ->place }}
                                    <br />
                                    <small class="hstack text-secondary">
                                        {{ $organ->organBuilder->name }} (1768)
                                        <x-organomania.stars class="ms-auto" :count="round($organ->importance / 2)" />
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                
                    @if ($this->resultsOrganBuilders->isNotEmpty())
                        <div class="card-header fw-bold">
                            <i class="bi-file-person"></i> {{ __('Varhanáři') }}
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($this->resultsOrganBuilders as $organBuilder)
                                <a class="list-group-item list-group-item-action" href="{{ route('organ-builders.show', ['organBuilder' => $organBuilder->id]) }}">
                                    {{ $organBuilder->name }}
                                    @if ($organBuilder->active_period)
                                        <span class="text-secondary">({{ $organBuilder->active_period }})</span>
                                        <br />
                                        <small class="hstack text-secondary">
                                            {{ $organBuilder->municipality }}
                                            <x-organomania.stars class="ms-auto" :count="round($organBuilder->importance / 2)" />
                                        </small>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="card-body">
                        {{ __('Nic nebylo nalezeno.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>