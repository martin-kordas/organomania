<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Helpers;

// https://forum.laravel-livewire.com/t/search-with-autocomplete/2966/2
// TODO: jsou-li zadána 2 slova z různých nefulltextových sloupců, vyhovující záznam není nalezen (možná musí mít oba sloupce fulltext index)
//  - obecně po zadání druhého slova hledání nefunguje dobře
new class extends Component {

    public $search;

    public $minSearchLength = 3;

    private Collection $resultsOrgans;
    private Collection $resultsOrganBuilders;
    private int $resultsCount = 0;
    
    public function updatedSearch()
    {
        $search = trim($this->search);
        if (mb_strlen($search) >= $this->minSearchLength) {
            $this->resultsOrgans = $this->getOrgans();
            $this->resultsOrganBuilders = $this->getOrganBuilders();
            $this->resultsCount = $this->resultsOrgans->count() + $this->resultsOrganBuilders->count();
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
                        'organs.id', 'organs.slug', 'organs.place', 'organs.municipality', 'organs.importance', 'organs.organ_builder_id',
                        'organs.year_built', 'organs.user_id',
                    ])
                    ->with('organBuilder:id,is_workshop,first_name,last_name,workshop_name')
                    ->orderBy('importance', 'DESC')
                    ->take(8);
            })
            ->get();
    }
    
    private function getOrganBuilders()
    {
        return OrganBuilder::search($this->search)
            ->query(function (Builder $builder) {
                $builder
                    ->orderBy('importance', 'DESC')
                    ->take(8)
                    ->inland()
                    ->select([
                        'id', 'slug',
                        'is_workshop', 'workshop_name', 'first_name', 'last_name',
                        'active_period', 'municipality', 'importance',
                        'user_id',
                    ]);
            })
            ->get()
            ->append(['name']);
    }

    private function highlight($text)
    {
        if ($this->search == '' || $text == '') return $text;
        return Helpers::highlightEscapeText($text, $this->search);
    }

}; ?>

<form role="search" class="col" style="font-size: 95%;">
    <div x-data="{isTyped: false}">
        <div class="position-relative">
            <div>
                <input
                    id="search"
                    type="search"
                    class="form-control"
                    placeholder="{{__('Hledat varhany a varhanáře')}}&hellip; (/)"
                    aria-label="{{ __('Hledat') }}"
                    size="27"
                    @input.debounce.400ms="isTyped = ($event.target.value != '' && $event.target.value.length >= $wire.minSearchLength)"
                    @keydown.esc="isTyped = false"
                    @click.outside="isTyped = false"
                    @keydown.slash.window="focusSearch"
                    wire:model.live.debounce.350ms="search"
                    autocomplete="off"
                />
            </div>
            <div class="search-results card position-absolute shadow w-100" x-show="isTyped" x-cloak style="display: none;">
                @if ($this->resultsCount > 0)
                    @if ($this->resultsOrgans->isNotEmpty())
                        <div class="card-header fw-bold">
                            <i class="bi-file-music"></i> {{ __('Varhany') }}
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($this->resultsOrgans as $organ)
                                <a class="list-group-item list-group-item-action" href="{{ route('organs.show', ['organ' => $organ->slug]) }}" wire:navigate>
                                    {!! $this->highlight($organ->municipality) !!}, {!! $this->highlight($organ->place) !!}
                                    @if (!$organ->isPublic())
                                        <i class="bi-lock text-warning"></i>
                                    @endif
                                    <br />
                                    <small class="hstack text-secondary">
                                        {!! $this->highlight($organ->organBuilder?->name ?? __('neznámý varhanář')) !!}
                                        @isset($organ->year_built)
                                            ({{ $organ->year_built }})
                                        @endisset
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
                                <a class="list-group-item list-group-item-action" href="{{ route('organ-builders.show', ['organBuilder' => $organBuilder->slug]) }}" wire:navigate>
                                    {!! $this->highlight($organBuilder->name) !!}
                                    @if (!$organBuilder->isPublic()) 
                                        <i class="bi-lock text-warning"></i>
                                    @endif
                                    @if ($organBuilder->active_period)
                                        <span class="text-secondary">({{ $organBuilder->active_period }})</span>
                                        <br />
                                        <small class="hstack text-secondary">
                                            {!! $this->highlight($organBuilder->municipality) !!}
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

@script
<script>
    window.focusSearch = function (e) {
        if (!['input', 'textarea'].includes(e.target.tagName.toLowerCase())) {
            document.querySelector('#search').focus()
            e.preventDefault()
        }
    }
</script>
@endscript
