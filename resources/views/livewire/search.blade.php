<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\RegisterName;
use App\Helpers;

// https://forum.laravel-livewire.com/t/search-with-autocomplete/2966/2
// TODO: jsou-li zadána 2 slova z různých nefulltextových sloupců, vyhovující záznam není nalezen (možná musí mít oba sloupce fulltext index)
//  - obecně po zadání druhého slova hledání nefunguje dobře
new class extends Component {

    public $search;

    public $minSearchLength = 3;

    public $placeholder;
    public $id = 'search';

    private Collection $resultsOrgans;
    private Collection $resultsOrganBuilders;
    private Collection $resultsRegisterNames;
    private int $resultsCount = 0;
    
    public function boot()
    {
        $this->placeholder ??= __('Hledat varhany, varhanáře, rejstříky') . ' (/)';
    }

    public function updatedSearch()
    {
        $search = trim($this->search);
        if (mb_strlen($search) >= $this->minSearchLength) {
            $this->resultsOrgans = $this->getOrgans();
            $this->resultsOrganBuilders = $this->getOrganBuilders();
            $this->resultsRegisterNames = $this->getRegisterNames();
            $this->resultsCount = $this->resultsOrgans->count() + $this->resultsOrganBuilders->count() + $this->resultsRegisterNames->count();
        }
    }

    #[Computed]
    public function sanitizedSearch()
    {
        return trim($this->search ?? '');
    }

    private function getOrgans()
    {
        // https://laravel-news.com/laravel-scout-practical-guide#content-write-a-search-query
        return Organ::search($this->sanitizedSearch)
            ->query(function (Builder $builder) {
                $builder
                    ->leftJoin('organ_builders', 'organs.organ_builder_id', 'organ_builders.id')
                    ->select([
                        'organs.id', 'organs.slug', 'organs.place', 'organs.municipality', 'organs.importance', 'organs.organ_builder_id',
                        'organs.year_built', 'organs.user_id',
                    ])
                    ->selectRaw(
                        'MATCH(organs.municipality, organs.place) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance',
                        [$this->sanitizedSearch]
                    )
                    ->with('organBuilder:id,is_workshop,first_name,last_name,workshop_name')
                    // přednostně varhany, kde je nějaký výskyt hledaného výrazu v lokalitě (municipality, place)
                    ->orderByRaw('relevance > 0 DESC')
                    ->orderBy('importance', 'DESC')
                    ->orderBy('municipality')
                    ->orderBy('place')
                    ->take(8);
            })
            ->get();
    }
    
    private function getOrganBuilders()
    {
        return OrganBuilder::search($this->sanitizedSearch)
            ->query(function (Builder $builder) {
                $builder
                    ->orderBy('relevance', 'DESC')
                    ->orderBy('importance', 'DESC')
                    ->orderByName()
                    ->take(8)
                    ->select([
                        'id', 'slug',
                        'is_workshop', 'workshop_name', 'first_name', 'last_name',
                        'active_period', 'municipality', 'importance',
                        'user_id',
                    ])
                    ->selectRaw(
                        'MATCH(workshop_name, first_name, last_name) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance',
                        [$this->sanitizedSearch]
                    );
            })
            ->get()
            ->append(['name']);
    }

    private function getRegisterNames()
    {
        return RegisterName::search($this->sanitizedSearch)
            ->query(function (Builder $builder) {
                $builder
                    ->select([
                        'register_id', 'name', 'slug', 'language', 'hide_language'
                    ])
                    ->with('register:id,register_category_id')
                    ->orderBy('name')
                    ->take(12);
            })
            ->get()
            ->unique(
                fn (RegisterName $registerName1) => $registerName1->getVisualIdentifier()
            );
    }

    private function highlight($text)
    {
        if ($this->search == '' || $text == '') return $text;
        return Helpers::highlightEscapeText($text, $this->search);
    }

}; ?>

<div class="col">
    <form role="search" id="{{ $id }}-form" style="font-size: 95%;" onsubmit="return false">
        <div x-data="{isTyped: false}">
            <div class="position-relative">
                <div class="input-group search-input-group">
                    @if ($id === 'welcomeSearch')
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                    @endif
                    <input
                        id="{{ $id }}"
                        type="search"
                        class="search form-control px-1 px-xxl-2"
                        placeholder="{{ $placeholder }}"
                        aria-label="{{ __('Hledat') }}"
                        size="{{ app()->getLocale() === 'cs' ? 30 : 29 }}"
                        @input.debounce.400ms="isTyped = ($event.target.value != '' && $event.target.value.length >= $wire.minSearchLength)"
                        @keydown.esc="isTyped = false"
                        @click.outside="isTyped = false"
                        @keydown.slash.window="focusSearch"
                        wire:model.live.debounce.350ms="search"
                        autocomplete="off"
                    />
                </div>
                <div class="search-results card position-absolute shadow w-100 z-1" x-show="isTyped" x-cloak style="display: none;">
                    @if ($this->resultsCount > 0)
                        @if ($this->resultsOrgans->isNotEmpty())
                            <div class="card-header fw-bold">
                                <i class="bi-music-note-list"></i> {{ __('Varhany') }}
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
                                            <span>
                                                {!! $this->highlight($organ->organBuilder?->name ?? __('neznámý varhanář')) !!}
                                                @isset($organ->year_built)
                                                    ({{ $organ->year_built }})
                                                @endisset
                                            </span>
                                            <x-organomania.stars class="ms-auto" :count="round($organ->importance / 2)" />
                                        </small>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if ($this->resultsOrganBuilders->isNotEmpty())
                            <div class="card-header fw-bold">
                                <i class="bi-person-circle"></i> {{ __('Varhanáři') }}
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
                                                @if (!$organBuilder->shouldHideImportance())
                                                    <x-organomania.stars class="ms-auto" :count="round($organBuilder->importance / 2)" />
                                                @endif
                                            </small>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    
                        <div class="list-group list-group-flush position-relative text-center border-0">
                            <div class="list-group-item list-group-item-action">
                                <a type="submit" class="link-primary text-decoration-none stretched-link" href="#" onclick="$('#searchVarhanyNet').submit()">
                                    <i class="bi bi-search"></i>
                                    {{ __('Hledat v katalogu varhany.net') }}
                                </a>
                            </div>
                        </div>

                        @if ($this->resultsRegisterNames->isNotEmpty())
                            <div class="card-header fw-bold">
                                <i class="bi-record-circle"></i> {{ __('Rejstříky') }}
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach ($this->resultsRegisterNames as $registerName)
                                    <a
                                        class="list-group-item list-group-item-action d-flex column-gap-1 align-items-center"
                                        href="{{ route('dispositions.registers.show', ['registerName' => $registerName->slug]) }}"
                                        wire:navigate
                                    >
                                        <span class="me-auto">
                                            {!! $this->highlight($registerName->name) !!}
                                            @if (!$registerName->hide_language)
                                                <span class="text-body-secondary">({{ $registerName->language }})</span>
                                            @endif
                                        </span>

                                        <span class="badge text-bg-primary">
                                            {{ $registerName->register->registerCategory->getName() }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="list-group position-relative text-center">
                            <div class="list-group-item list-group-item-action">
                                <div>
                                    {{ __('Nic nebylo nalezeno.') }}
                                </div>
                                <div>
                                    <a type="submit" class="link-primary text-decoration-none stretched-link" href="#" onclick="$('#searchVarhanyNet').submit()">
                                        <i class="bi bi-search"></i>
                                        {{ __('Hledat v katalogu varhany.net') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
    
    <form class="d-none" id="searchVarhanyNet" method="post" accept-charset="windows-1250" action="http://www.varhany.net/search.php" target="_blank">
        <input type="hidden" name="obeca" value="{{ $this->sanitizedSearch }}" />
        <input type="hidden" name="ob" value="1" />
    </form>
</div>

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
