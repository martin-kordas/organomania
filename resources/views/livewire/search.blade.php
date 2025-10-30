<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\RegisterName;
use App\Repositories\OrganRepository;
use App\Helpers;

// návrh komponenty: https://forum.laravel-livewire.com/t/search-with-autocomplete/2966/2
new class extends Component {

    public $search;

    public $minSearchLength = 3;

    public $placeholder;
    public $id = 'search';

    private Collection $resultsOrgans;
    private Collection $resultsOrganBuilders;
    private Collection $resultsRegisterNames;
    private int $resultsCount = 0;
    private bool $showOrganBuildersFirst = false;
    
    public function boot()
    {
        $this->placeholder ??= __('Hledat varhany, varhanáře, rejstříky') . ' (/)';
    }

    public function updatedSearch()
    {
        $this->doSearch();
    }

    public function doSearch()
    {
        if ($this->showLastViewed || mb_strlen($this->sanitizedSearch) >= $this->minSearchLength) {
            $this->resultsOrgans = $this->getOrgans();
            $this->resultsOrganBuilders = $this->getOrganBuilders();
            $this->resultsRegisterNames = $this->getRegisterNames();
            $this->resultsCount = $this->resultsOrgans->count() + $this->resultsOrganBuilders->count() + $this->resultsRegisterNames->count();
            
            if ($this->resultsCount <= 0 && mb_strlen($this->sanitizedSearch) >= 4) {
                $sanitizedSearch = $this->sanitizedSearch;
                
                $searchLength = mb_strlen($this->sanitizedSearch);
                $maxDistance = match(true) {
                    $searchLength >= 7 => 3,
                    $searchLength >= 5 => 2,
                    default => 1
                };
                
                if ($repairedSearch = $this->repairSearchWithOrgans($sanitizedSearch, $maxDistance)) {
                    $this->sanitizedSearch = $repairedSearch;
                    $this->resultsOrgans = $this->getOrgans();
                    $this->resultsCount = $this->resultsOrgans->count();
                }
                if ($this->resultsCount <= 0 && $repairedSearch = $this->repairSearchWithOrganBuilders($sanitizedSearch, $maxDistance)) {
                    $this->sanitizedSearch = $repairedSearch;
                    $this->resultsOrgans = $this->getOrgans();
                    $this->resultsOrganBuilders = $this->getOrganBuilders();
                    $this->resultsCount = $this->resultsOrgans->count() + $this->resultsOrganBuilders->count();
                }
            }

            $this->showOrganBuildersFirst = $this->resultsOrganBuilders->contains(
                fn (OrganBuilder $organBuilder) => $organBuilder->exact_name_match
            );
        }
    }

    #[Computed]
    public function sanitizedSearch()
    {
        return trim($this->search ?? '');
    }

    #[Computed]
    public function showLastViewed()
    {
        return $this->sanitizedSearch === '';
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
                        'organs.year_built', 'organs.baroque', 'organs.user_id',
                    ])
                    ->with('organBuilder:id,is_workshop,first_name,last_name,workshop_name');
                
                if ($this->showLastViewed) {
                    $organIds = OrganRepository::getLastViewedOrganIds();
                    if ($organIds->isEmpty()) $builder->whereRaw('false');
                    else {
                        $idsStr = $organIds->map(intval(...))->implode(', ');
                        $builder
                            ->whereIn('organs.id', $organIds)
                            ->orderByRaw("FIELD(organs.id, $idsStr)");
                    }
                }
                else {
                    $searchWildcard = "%{$this->sanitizedSearch}%";
                    $builder
                        ->selectRaw('
                            IFNULL(organs.municipality, "") LIKE ?
                            OR IFNULL(organs.place, "") LIKE ?
                            OR IFNULL(organ_builders.first_name, "") LIKE ?
                            OR IFNULL(organ_builders.last_name, "") LIKE ?
                            OR IFNULL(organ_builders.workshop_name, "") LIKE ?
                            AS highlighted
                        ', [$searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard])
                        ->selectRaw(
                            'MATCH(organs.place, organs.municipality, organs.description, organs.perex) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance',
                            [$this->sanitizedSearch]
                        )
                        // přednostně varhany, kde je nějaký výskyt hledaného výrazu v údajích v našeptávači (lokalita, varhanář)
                        ->orderBy('highlighted', 'DESC')
                        // pokud je výskyt hledaného výrazu jen ve skrytých textech (description atd.), řadit podle míry shody
                        ->orderByRaw('IF(highlighted, 1, relevance) DESC')
                        ->orderBy('importance', 'DESC')
                        ->orderBy('municipality')
                        ->orderBy('place')
                        ->take(8);
                }
            })
            ->get();
    }
    
    private function getOrganBuilders()
    {
        if ($this->showLastViewed) return collect();

        return OrganBuilder::search($this->sanitizedSearch)
            ->query(function (Builder $builder) {
                $searchWildcard = "%{$this->sanitizedSearch}%";

                $builder
                    ->leftJoin('organ_builder_additional_images', 'organ_builder_additional_images.organ_builder_id', 'organ_builders.id')
                    ->groupBy('organ_builders.id')
                    // přednostně varhanáři, kde je nějaký výskyt hledaného výrazu v údajích v našeptávači (jméno, lokalita)
                    ->orderBy('highlighted', 'DESC')
                    // pokud je výskyt hledaného výrazu jen ve skrytých textech (description atd.), řadit podle míry shody
                    ->orderByRaw('IF(highlighted, 1, relevance) DESC')
                    ->orderBy('importance', 'DESC')
                    ->orderByName()
                    ->take(8)
                    ->select([
                        'organ_builders.id', 'slug',
                        'is_workshop', 'workshop_name', 'first_name', 'last_name',
                        'active_period', 'municipality', 'importance',
                        'baroque', 'user_id',
                    ])
                    ->selectRaw('
                        IFNULL(workshop_name, "") LIKE ?
                        OR IFNULL(first_name, "") LIKE ?
                        OR IFNULL(last_name, "") LIKE ?
                        OR IFNULL(municipality, "") LIKE ?
                        AS highlighted
                    ', [$searchWildcard, $searchWildcard, $searchWildcard, $searchWildcard])
                    ->selectRaw(
                        'MATCH(first_name, last_name, perex, description, workshop_members) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance',
                        [$this->sanitizedSearch]
                    )
                    ->selectRaw('
                        IFNULL(workshop_name, "") = ?
                        OR IFNULL(last_name, "") = ?
                        OR CONCAT(
                            IFNULL(first_name, ""),
                            " ",
                            IFNULL(last_name, "")
                        ) = ?
                        AS exact_name_match
                    ', [$this->sanitizedSearch, $this->sanitizedSearch, $this->sanitizedSearch]);
            })
            ->get()
            ->append(['name']);
    }
    
    private function repairSearchWithOrgans(string $search, int $maxDistance)
    {
        // TODO: zvážit cachování
        $organs = Organ::query()
            ->public()
            ->groupBy('municipality')
            ->orderByRaw('COUNT(id) DESC')
            ->select(['municipality'])
            ->get();
          
        $distance = INF;
        $municipality = null;
        foreach ($organs as $organ) {
            $distance1 = $this->compareSearches($search, $organ->municipality);
            
            if ($distance1 <= $maxDistance && $distance1 < $distance) {
                $distance = $distance1;
                $municipality = $organ->municipality;
            }
        }
        return $municipality;
    }
    
    private function repairSearchWithOrganBuilders(string $search, int $maxDistance)
    {
        // TODO: zvážit cachování
        $organBuilders = OrganBuilder::query()
            ->public()
            ->groupByRaw('IFNULL(last_name, workshop_name)')
            ->orderByRaw('COUNT(id) DESC')
            ->selectRaw('IFNULL(last_name, workshop_name) AS search_name')
            ->get();
          
        $distance = INF;
        $name = null;
        foreach ($organBuilders as $organBuilder) {
            $distance1 = $this->compareSearches($search, $organBuilder->search_name);
            
            if ($distance1 <= $maxDistance && $distance1 < $distance) {
                $distance = $distance1;
                $name = $organBuilder->search_name;
            }
        }
        return $name;
    }
    
    private function getSearchForComparison(string $search)
    {
        $search = mb_strtolower($search);
        $search = Helpers::stripAccents($search);
        return $search;
    }
    
    private function compareSearches(string $search1, string $search2)
    {
        // porovnáváme jen zadanou část slova
        if (mb_strlen($search1) < mb_strlen($search2)) {
            $search2 = mb_substr($search2, 0, mb_strlen($search1));
        }
        
        $str1 = $this->getSearchForComparison($search1);
        $str2 = $this->getSearchForComparison($search2);
        return levenshtein($str1, $str2);
    }

    private function getRegisterNames()
    {
        if ($this->showLastViewed) return collect();

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
        if ($this->sanitizedSearch == '' || $text == '') return $text;
        return Helpers::highlightEscapeText($text, $this->sanitizedSearch, words: true);
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
                        @input.debounce.400ms="isTyped = isValueTyped($event.target.value)"
                        @keydown.esc="isTyped = false"
                        @click.outside="isTyped = false"
                        @keydown.slash.window="focusSearch"
                        @focus="isTyped = isValueTyped($event.target.value)"
                        wire:focus="doSearch"
                        wire:model.live.debounce.350ms="search"
                        autocomplete="off"
                    />
                </div>
                <div class="search-results card position-absolute shadow w-100 z-1" x-show="isTyped" x-cloak style="display: none;">
                    @if ($this->resultsCount > 0)
                        @if ($this->showOrganBuildersFirst && $this->resultsOrganBuilders->isNotEmpty())
                            <x-organomania.search.organ-builders :organBuilders="$this->resultsOrganBuilders" />
                        @endif

                        @if ($this->resultsOrgans->isNotEmpty())
                            <x-organomania.search.organs :organs="$this->resultsOrgans" :showLastViewed="$this->showLastViewed" />
                        @endif
                        
                        @if (!$this->showOrganBuildersFirst && $this->resultsOrganBuilders->isNotEmpty())
                            <x-organomania.search.organ-builders :organBuilders="$this->resultsOrganBuilders" />
                        @endif

                        @if (!$this->showLastViewed)
                            <div class="list-group list-group-flush position-relative text-center border-top-0">
                                <div class="list-group-item list-group-item-action">
                                    <a type="submit" class="link-primary text-decoration-none stretched-link" href="#" onclick="$('#searchVarhanyNet').submit()">
                                        <i class="bi bi-search"></i>
                                        {{ __('Hledat v katalogu varhany.net') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($this->resultsRegisterNames->isNotEmpty())
                            <x-organomania.search.register-names :registerNames="$this->resultsRegisterNames" />
                        @endif
                    @else
                        @if (!$this->showLastViewed)
                            <div class="list-group position-relative text-center" wire:loading.remove>
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
                    
                        <div class="list-group list-group-flush position-relative text-center border-top-0" wire:loading>
                            <div class="list-group-item my-2">
                                <x-organomania.spinner :margin="false" />
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
    
    window.isValueTyped = function (value) {
        return value.trim() === '' || value.length >= $wire.minSearchLength
    }
</script>
@endscript
