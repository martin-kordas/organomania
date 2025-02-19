<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL as URLFacade;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Enums\KancionalSongCategory;
use App\Helpers;
use App\Livewire\Forms\WorshipSongForm;
use App\Models\LiturgicalCelebration;
use App\Models\LiturgicalDay;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Models\Song;
use App\Models\WorshipSong;
use App\Repositories\OrganRepository;
use App\Traits\ConvertEmptyStringsToNull;

// TODO: nápady na vylepšení
//  - po uložení písně přesměrovat na stránku, kde je uložená (jsou-li ale zapnuty filtry, nemusí být na žádné stránce)

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use WithPagination;
    use ConvertEmptyStringsToNull;

    #[Locked]
    public $organSlug;
    #[Locked]
    public Organ $organ;

    public WorshipSongForm $form;

    #[Url(history: true)]
    public ?int $filterSongId = null;
    #[Session]
    #[Url(history: true)]
    public bool $filterSundays = false;
    #[Session]
    #[Url(history: true)]
    public bool $filterNonEmptyDays = false;

    #[Session]
    #[Url(history: true)]
    public bool $showLiturgicalCelebrations = true;

    public ?LiturgicalCelebration $liturgicalCelebration = null;

    public bool $isEdit = false;

    #[Locked]
    public bool $signed;

    private ?int $savedWorshipSongId = null;

    const PER_PAGE = 7;

    public function boot(OrganRepository $repository)
    {
        $this->signed ??= request()->hasValidSignature(false);
        // nepoužíváme klasický route model binding, protože potřebujeme ručně odebrat OwnedEntityScope
        //  - musí to fungovat i v Livewire AJAX requestech
        $this->organ = $repository->getBySlug($this->organSlug, $this->signed);

        $this->organ->load([
            'organBuilder' => function (BelongsTo $query) {
                if ($this->signed)
                    $query->withoutGlobalScope(OwnedEntityScope::class);
            },
        ]);

        $this->form->boot();
    }

    public function mount()
    {
        if (!$this->signed) {
            $this->authorize('viewWorshipSongs', $this->organ);
        }

        $this->form->date = now()->format('Y-m-d');

        if (request('page') === null) $this->setDefaultPage();
    }

    public function rendering(View $view): void
    {
        $view->title($this->getTitle());
    }

    private function setDefaultPage()
    {
        // jsou-li zobrazeny jen zapsané dny, dnešek se hledá obtízně
        if ($this->filterNonEmptyDays || $this->filterSongId) return;

        // výchozí je strana s dnešním dnem
        if ($this->filterSundays) {
            $maxDate = LiturgicalDay::getMaxDateSunday();
            $diff = $this->today->diffInDays($maxDate);
            if ($diff > 0) {
                $page = 1 + intdiv(floor($diff / 7), static::PER_PAGE);
                $this->setPage($page);
            }
        }
        else {
            $maxDate = LiturgicalDay::getMaxDate();
            $diff = $this->today->diffInDays($maxDate);
            if ($diff > 0) {
                $page = 1 + intdiv($diff, static::PER_PAGE);
                $this->setPage($page);
            }
        }
    }

    private function getShareUrl()
    {
        if (isset($this->organ->user_id)) $relativeUrl = URLFacade::signedRoute('organs.worship-songs', $this->organ->slug, absolute: false);
        else $relativeUrl = route('organs.worship-songs', $this->organ->slug, absolute: false);
        return url($relativeUrl);
    }

    private function getSelfUrl()
    {
        if ($this->signed) $relativeUrl = URLFacade::signedRoute('organs.worship-songs', $this->organ->slug, absolute: false);
        else $relativeUrl = route('organs.worship-songs', $this->organ->slug, absolute: false);
        return url($relativeUrl);
    }

    private function getTitle()
    {
        $title = __('Písně při bohoslužbě');
        $title .= " – ";
        $title .= "{$this->organ->municipality}, {$this->organ->place}";
        return $title;
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
        $this->dispatch('select2-sync-needed', componentName: 'pages.worship-songs');
    }

    public function updated($property)
    {
        if ($property === 'filterSundays') $this->setDefaultPage();
        elseif (in_array($property, ['filterSongId', 'filterNonEmptyDays'])) {
            $this->resetPage();
            if (!$this->$property) $this->setDefaultPage();
        }
    }

    #[Computed]
    public function songGroups()
    {
        $today = today()->format('Y-m-d');

        $preferredSongCategory = LiturgicalDay::getToday()?->getPreferredKancionalSongCategory();

        return Song::orderBy('number')
            ->withCount([
                'worshipSongs' => function (Builder $query) {
                    $query
                        ->where('organ_id', $this->organ->id)
                        ->whereRaw('date <= CURDATE()');
                    if ($this->filterSundays) $query->whereRaw('WEEKDAY(date) = 6');
                },
                'worshipSongs as worship_songs_month_count' => function (Builder $query) use ($today) {
                    $query
                        ->where('organ_id', $this->organ->id)
                        ->whereRaw('date BETWEEN ? - INTERVAL 3 MONTH AND ?', [$today, $today]);
                    if ($this->filterSundays) $query->whereRaw('WEEKDAY(date) = 6');
                },
            ])
            ->get()
            ->groupBy(
                fn (Song $song) => $song->category->value
            )
            ->sortBy(function (Collection $songs) use ($preferredSongCategory) {
                $song = $songs->first();
                if ($preferredSongCategory && $song->category === $preferredSongCategory) return 0;
                return $songs->first()->number;
            });
    }

    #[Computed]
    public function today()
    {
        if ($this->filterSundays) return today()->endOfWeek(Carbon::SUNDAY)->startOfDay();
        return today();
    }

    #[Computed]
    public function liturgicalDays()
    {
        return LiturgicalDay::where('date', '<=', LiturgicalDay::getMaxDisplayedDate())
            ->with([
                'liturgicalCelebrations',
                'worshipSongs' => function (HasMany $query) {
                    $query->where('organ_id', $this->organ->id);
                },
                'worshipSongs.user:id,name',
            ])
            ->when($this->filterNonEmptyDays || $this->filterSongId, function (Builder $query) {
                $query->whereHas('worshipSongs', function (Builder $query) {
                    $query->where('organ_id', $this->organ->id);
                    if ($this->filterSongId) {
                        $query->where('song_id', $this->filterSongId);
                    }
                });
            })
            ->when($this->filterSundays, function (Builder $query) {
                $query->whereRaw('WEEKDAY(date) = 6');
            })
            ->orderBy('date', 'desc')
            ->paginate(static::PER_PAGE);
    }

    #[Computed]
    public function worshipSongsGroups()
    {
        return $this->liturgicalDays->map(function (LiturgicalDay $liturgicalDay) {
            return $this->getWorshipSongsGroups($liturgicalDay);
        });
    }

    #[Computed]
    public function showTimes()
    {
        // buňku pro čas bohoslužby zobrazíme pouze, je-li alespoň v 1 dni vyplněn
        return $this->worshipSongsGroups->contains(function (Collection $worshipSongsGroups) {
            return $worshipSongsGroups->isNotEmpty() && $worshipSongsGroups->keys()->all() !== [''];
        });
    }

    private function getWorshipSongsUsers(Collection $worshipSongs)
    {
        return $worshipSongs->pluck('user')->unique()->sortBy('name');
    }

    private function getWorshipSongsGroups(LiturgicalDay $liturgicalDay)
    {
        $groups = $liturgicalDay->getWorshipSongsGroups()->sortBy(function ($_songs, $time) {
            // záznamy bez času na konec
            return $time === '' ? '9999' : $time;
        });

        // při aktivním filtru na píseň obsahují liturgicalDays dny, ve kterých se vyskytuje daná píseň, a v těchto dnech všechny časy
        //  - vyfiltrujeme pouze časy, ve kterých se nachází píseň
        if ($this->filterSongId) {
            $groups = $groups->filter(
                fn (Collection $worshipSongs) => $worshipSongs->contains(
                    fn (WorshipSong $worshipSong) => $worshipSong->song->id === $this->filterSongId
                )
            );
        }

        $groups = $groups->map(function (Collection $worshipSongsGroup) {
            return $worshipSongsGroup->sortBy(function (WorshipSong $worshipSong) {
                // ordinaria vždy zobrazit jako první
                $category = $worshipSong->song->category;
                if ($category === KancionalSongCategory::Ordinaries) return -1;
                return $category->value;
            });
        });

        return $groups;
    }

    private function getWorshipSongInfo(WorshipSong $worshipSong)
    {
        $info = trim($worshipSong->user->name ?? __('anonymní uživatel'));
        if (isset($worshipSong->updated_at)) {
            $info .= " (";
            $info .= Helpers::formatDate($worshipSong->updated_at) . ' v ' . Helpers::formatTime($worshipSong->updated_at->format('H:i:s'));
            $info .= ")";
        }
        return $info;
    }

    public function add($liturgicalDayId = null)
    {
        // pokud se ::resetValidation() zavolá jen v cancel(), stejně se zobrazí chybové hlášky - neznám důvod
        $this->form->resetValidation();
        $this->isEdit = true;

        if (isset($liturgicalDayId)) {
            $liturgicalDay = LiturgicalDay::findOrFail($liturgicalDayId);
            $this->form->date = $liturgicalDay->date->format('Y-m-d');
            $this->js('scrollToTop()');
        }
    }

    public function cancel()
    {
        $this->isEdit = false;
        $this->form->reset(['songIds']);
        $this->form->resetValidation();
    }

    public function save()
    {
        $this->form->validate();
        //dd($this->form->getErrorBag());

        DB::transaction(function () {
            foreach ($this->form->songIds as $songId) {
                $data = Helpers::arrayKeysSnake($this->form->except(['songIds']));
                $data['song_id'] = $songId;

                $worshipSong = new WorshipSong($data);
                $worshipSong->organ()->associate($this->organ);
                $worshipSong->user()->associate(Auth::user());
                $worshipSong->save();
                $this->savedWorshipSongId = $worshipSong->id;
            }
        });
        
        $toastId = count($this->form->songIds) > 1 ? 'savedMany' : 'saved';
        $this->js("showToast('$toastId')");
        $this->cancel();
    }

    public function delete($worshipSongId)
    {
        $worshipSong = WorshipSong::findOrFail($worshipSongId);
        Gate::authorize('delete', $worshipSong);
        $worshipSong->delete();
        $this->js('showToast("deleted")');
    }

    public function setLiturgicalCelebration($liturgicalCelebrationId)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->liturgicalCelebration = LiturgicalCelebration::findOrFail($liturgicalCelebrationId);
    }

}; ?>

<div class="worship-songs container">
    @push('meta')
        <meta name="robots" content="noindex,nofollow">
    @endpush
    
    <h3>
        <a href="{{ $this->getSelfUrl() }}" class="text-decoration-none" wire:navigate>
            {{ __('Písně při bohoslužbě') }}
        </a>
        @if (!$this->organ->isPublic())
            <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
        @endif
    </h3>
    <h4>{{ $organ->municipality }}, {{ $organ->place }}</h4>
    
    <div class="mb-4 mt-3">
        {{ __('Varhany_1') }}:&nbsp;
        <x-organomania.organ-organ-builder-link :$organ :$signed />
    </div>
    
    <div class="text-center">
        <div class="small text-start text-body-secondary mb-4 alert alert-light py-2 px-2 d-inline-block">
            <ul class="ps-3 mb-0">
                <li>{{ __('Evidujte si přehledně písně zpívané při bohoslužbách.') }}</li>
                <li>{{ __('Zjistěte, jak často se píseň zpívala – celkově nebo za poslední čtvrtletí (3 měsíce).') }}</li>
                <li>{{ __('Rozplánujte zpěv písní i do budoucna.') }}</li>
                <li>
                    {{ __('Nasdílejte evidenci svým kolegům a zapisujte písně společně.') }}
                    <div class="mt-1">
                        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl() }}">
                            <i class="bi-share"></i> {{ __('Sdílet') }}
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    @if ($this->isEdit)
        <div class="card mx-auto bg-light" style="max-width: 775px">
            <div class="card-body">
                <h4 class="card-title">{{ __('Zapsat píseň') }}</h4>

                <form class="row g-3 mt-sm-1 align-items-end" wire:submit="save">
                    <div class="col-12 col-sm-6">
                        <label for="date" class="form-label">{{ __('Datum') }}</label>
                        <input id="date" class="form-control @error('form.date') is-invalid @enderror" type="date" wire:model="form.date" />
                        @error('form.date')
                            <div id="dateFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="time" class="form-label">{{ __('Čas bohoslužby') }} <span class="form-text text-body-secondary">({{ __('nepovinné') }})</span></label>
                        <input id="time" class="form-control @error('form.time') is-invalid @enderror" type="time" wire:model="form.time" />
                        @error('form.time')
                            <div id="timeFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="songId" class="form-label">{{ __('Písně') }}</label>
                        <x-organomania.selects.song-select :songGroups="$this->songGroups" id="songId" model="form.songIds" placeholder="{{ __('Zvolte písně') }}" multiple />
                        <div class="form-text">
                            {{ __('Zvolte písně v pořadí, v jakém se hrály.') }}
                            {{ __('S výběrem písní poradí') }} <a href="https://www.duchovnihudba.cz/wp-content/uploads/2022/10/Direka%CC%81r%CC%8C-pro-varhani%CC%81ky.pdf" target="_blank">{{ __('Direktář pro varhaníky') }}</a>.
                        </div>
                        <div class="form-text" style="columns: 11em">
                            <div><a class="text-decoration-none" href="#" onclick="return addSong({{ Song::SONG_ID_502 }})">502: Ordinarium Olejník</a></div>
                            <div><a class="text-decoration-none" href="#" onclick="return addSong({{ Song::SONG_ID_503 }})">503: Ordinarium Bříza</a></div>
                            <div><a class="text-decoration-none" href="#" onclick="return addSong({{ Song::SONG_ID_504 }})">504: Ordinarium Eben</a></div>
                            @foreach (KancionalSongCategory::cases() as $category)
                                @if (!in_array($category, [KancionalSongCategory::Prayers, KancionalSongCategory::Ordinaries]))
                                    <div>{{ $category->getFullName() }}</div>
                                @endif
                            @endforeach
                            <hr class="my-2" />
                            <div>M: mešní píseň</div>
                            <div>M<sub>1</sub>: vstup</div>
                            <div>M<sub>2</sub>: před evangeliem</div>
                            <div>M<sub>3</sub>: obětní průvod</div>
                            <div>M<sub>4</sub>: přijímání</div>
                            <div>M<sub>5</sub>: díky po přijímání</div>
                        </div>
                    </div>
                </form>

                <div class="text-end mt-3">
                    <button type="button" class="btn btn-sm btn-secondary" wire:click="cancel">
                        <span wire:loading.remove wire:target="cancel">
                            <i class="bi-x-lg"></i>
                        </span>
                        <span wire:loading wire:target="cancel">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                            <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                        </span>
                        {{ __('Neukládat') }}
                    </button>
                    
                    <button type="submit" class="btn btn-sm btn-primary" wire:click="save" wire:loading.class="disabled" wire:loading.target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="bi-floppy"></i>
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                            <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                        </span>
                        {{ __('Uložit') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="text-center">
            <button type="button" class="btn btn-primary" wire:click="add">
                <span wire:loading.remove wire:target="add()">
                    <i class="bi-plus-lg"></i>
                </span>
                <span wire:loading wire:target="add()"
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                </span>
                {{ __('Zapsat píseň') }}
            </button>
        </div>
    @endif
        
    <div class="row mt-3 mb-3 g-3 align-items-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <x-organomania.selects.song-select :songGroups="$this->songGroups" model="filterSongId" placeholder="{{ __('Hledat píseň') }}" allowClear live frequency-in-selection />
        </div>
        <div class="col-12 col-lg-4 col-xl-6">
            <div class="row">
                <div class="col-12 col-sm-auto">
                    <div class="form-check form-switch">
                        <label for="filterSundays">{{ __('Jen neděle') }}</label>
                        <input id="filterSundays" class="form-check-input" type="checkbox" role="switch" wire:model.change="filterSundays" />
                    </div>
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="form-check form-switch">
                        <label for="filterNonEmptyDays">{{ __('Jen zapsané dny') }}</label>
                        <input id="filterNonEmptyDays" class="form-check-input" type="checkbox" role="switch" wire:model.change="filterNonEmptyDays" />
                    </div>
                </div>
                <div class="col-12 col-sm-auto">
                    <div class="form-check form-switch">
                        <label for="showLiturgicalCelebrations">{{ __('Názvy svátků') }}</label>
                        <input id="showLiturgicalCelebrations" class="form-check-input" type="checkbox" role="switch" wire:model.change="showLiturgicalCelebrations" />
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <div id="results" class="position-relative">
        <div
            wire:loading.block
            wire:target="filterSongId, filterSundays, filterNonEmptyDays, showLiturgicalCelebrations, gotoPage, previousPage, nextPage"
            @class(['position-absolute', 'text-center', 'w-100', 'h-100', 'start-0', 'opacity-75', 'bg-white', 'z-1'])
            @style(['padding-top: 4.1em' => $this->liturgicalDays->isNotEmpty()])
        >
            <x-organomania.spinner />
        </div>
        
        @if ($this->liturgicalDays->isEmpty())
            <div class="alert alert-secondary text-center mt-4" role="alert">
                @if ($this->filterNonEmptyDays || $this->filterSongId)
                    {{ __('Nebyly nalezeny žádné zapsané písně.') }}
                @else
                    {{ __('Nebyly nalezeny žádné dny v liturgickém kalendáři.') }}
                @endif
            </div>
        @else
            <div>
                {{ $this->liturgicalDays->links(data: ['scrollTo' => false]) }}
                
                <div class="table-responsive">
                    <table class="liturgical-days-table table table-hover align-middle w-100 d-block d-md-table">
                        <thead>
                            <tr class="d-none d-md-table-row">
                                <th>{{ __('Den') }} <i class="bi-sort-numeric-down-alt"></i></th>
                                <th>
                                    <div class="d-flex pe-2">
                                        {{ __('Písně') }}
                                    </div>
                                </th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider d-block">
                            @foreach ($this->liturgicalDays as $i => $liturgicalDay)
                                @php
                                    $worshipSongsGroups = $this->worshipSongsGroups[$i];
                                    $current = $liturgicalDay->date->equalTo($this->today);
                                    // $lastInWeek: deaktivováno, protože stránkování je po týdnech, takže oddělovat týdny čárkou nemá smysl
                                    $lastInWeek = false && !$this->filterSundays && isset($this->liturgicalDays[$i + 1]) && $this->liturgicalDays[$i + 1]->date->isoWeek() !== $liturgicalDay->date->isoWeek();
                                    $sunday = $liturgicalDay->isSunday();
                                @endphp
                                <tr @class(['d-block', 'd-md-table-row', 'table-active' => false && $sunday && !$current && !$this->filterSundays, 'border-dark' => $lastInWeek, 'current' => $current]) wire:key="{{ $i }}">
                                    <td @class(['d-block', 'd-md-table-cell', 'pb-md-2', 'pb-1' => $worshipSongsGroups->isNotEmpty()])>
                                        <span @class(['date', 'small', 'fw-bold' => $sunday])>
                                            {{ $liturgicalDay->date->minDayName }} {{ Helpers::formatDate($liturgicalDay->date) }}
                                            @if (!$showLiturgicalCelebrations)
                                                <br class="d-none d-md-inline" />
                                                <span class="d-md-none">&nbsp;</span>
                                                <span class="d-inline-flex column-gap-2">
                                                    @foreach ($liturgicalDay->liturgicalCelebrations as $iCelebration => $celebration)
                                                        <span data-bs-toggle="tooltip" data-bs-title="{{ $celebration->name }}" wire:key="{{ $iCelebration }}">
                                                            <a class="text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#liturgicalCelebrationModal" wire:click="setLiturgicalCelebration({{ $celebration->id }})">
                                                                <i class="bi bi-{{ $celebration->getIcon() }}" style="color: {{ $celebration->getIconColor() }}"></i>
                                                            </a>
                                                        </span>
                                                    @endforeach
                                                </span>
                                            @endif
                                        </span>
                                        <div class="d-md-none float-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary p-1 py-0 p-md-2 py-md-1"
                                                wire:click="add({{ $liturgicalDay->id }})"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="{{ __('Zapsat píseň pro tento den') }}"
                                                @disabled($isEdit)
                                            >
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                        @if ($showLiturgicalCelebrations)
                                            <div class="items-list">
                                                @foreach ($liturgicalDay->liturgicalCelebrations as $celebration)
                                                    <div class="liturgical-celebration-container d-flex column-gap-2">
                                                        <a class="text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#liturgicalCelebrationModal" wire:click="setLiturgicalCelebration({{ $celebration->id }})">
                                                            <i class="bi bi-{{ $celebration->getIcon() }}" style="color: {{ $celebration->getIconColor() }}"></i>
                                                        </a>
                                                        <div class="lh-base">
                                                            <x-organomania.soft-underline-link
                                                                href="#"
                                                                class="liturgical-celebration"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#liturgicalCelebrationModal"
                                                                wire:click="setLiturgicalCelebration({{ $celebration->id }})"
                                                                title="{{ $celebration->name }}"
                                                            >
                                                                {{ str($celebration->name)->limit(60) }}
                                                            </x-organomania.soft-underline-link>
                                                            @if ($celebration->shouldDisplayRank())
                                                                <small class="text-nowrap text-body-secondary">({{ $celebration->rank }})</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                            @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td @class(['songs', 'pt-0', 'pt-md-2', 'd-block', 'd-md-table-cell', 'd-none' => $worshipSongsGroups->isEmpty()])>
                                        @if ($worshipSongsGroups->isNotEmpty())
                                            @php $showEmptyTime = $worshipSongsGroups->keys()->all() !== [''] @endphp
                                            <table class="table table-borderless mb-0 d-block d-md-table">
                                                <tbody class="w-100 d-block">
                                                    @foreach ($worshipSongsGroups as $time => $worshipSongsGroup)
                                                        <tr class="d-block d-md-table-row w-100" wire:key="{{ $time }}">
                                                            @if ($this->showTimes)
                                                                <td @class(['time', 'bg-transparent', 'd-block', 'd-md-table-cell', 'p-md-2', 'ps-md-0', 'p-0', 'pt-2' => !$loop->first])>
                                                                    @if ($time)
                                                                        {{ Helpers::formatTime($time, seconds: false) }}
                                                                    @elseif ($showEmptyTime)
                                                                        <span class="fst-italic d-md-none">[{{ __('neznámý čas') }}]</span>
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            <td class="bg-transparent d-block d-md-table-cell p-0 p-md-2">
                                                                @foreach ($worshipSongsGroup as $worshipSong)
                                                                    @php $category = $worshipSong->song->category @endphp
                                                                    <div class="d-flex align-items-center column-gap-2" wire:key="{{ $worshipSong->id }}">
                                                                        <span class="song-number badge" style="color: {{ $category->getColor() }}; background: {{ $category->getBackground() }}">
                                                                            {{ $worshipSong->song->number }}
                                                                        </span>
                                                                        <span class="song-name-container flex-shrink-1">
                                                                            <x-organomania.soft-underline-link
                                                                                @class(['song-name', 'mark' => $this->filterSongId == $worshipSong->song->id, 'mark' => $this->savedWorshipSongId === $worshipSong->id])
                                                                                href="{{ $worshipSong->song->kancionalUrl }}"
                                                                                target="_blank"
                                                                                title="{{ __('Zobrazit píseň v Kancionálu') }}"
                                                                            >
                                                                                {{ $worshipSong->song->name }}
                                                                            </x-organomania.soft-underline-link>
                                                                            @isset($worshipSong->song->accompanimentUrl)
                                                                                <x-organomania.soft-underline-link class="small" href="{{ $worshipSong->song->accompanimentUrl }}" target="_blank" title="{{ __('Stáhnout varhanní doprovod') }}">
                                                                                    (dopr.)
                                                                                </x-organomania.soft-underline-link>
                                                                            @endisset
                                                                            @isset($worshipSong->song->purposeFormatted)
                                                                                &nbsp;
                                                                                <small title="{{ __('liturgické určení písně') }}">
                                                                                    {!! $worshipSong->song->purposeFormatted !!}
                                                                                </small>
                                                                            @endisset
                                                                        </span>

                                                                        @can('delete', $worshipSong)
                                                                            <span class="ps-2 ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat') }}">
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-danger p-1 py-0"
                                                                                    type="button"
                                                                                    data-worship-song-id="{{ $worshipSong->id }}"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#confirmDeleteWorshipSongModal"
                                                                                    @disabled($isEdit)
                                                                                >
                                                                                    <i class="bi bi-trash"></i>
                                                                                </button>
                                                                            </span>
                                                                        @else
                                                                            <span class="ps-2 ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit podrobnosti') }}">
                                                                                <a
                                                                                    class="btn btn-sm p-1 py-0 text-primary"
                                                                                    href="#"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#worshipSongInfoModal"s
                                                                                    data-info="{{ $this->getWorshipSongInfo($worshipSong) }}"
                                                                                    onclick="$('#worshipSongInfo').text(this.dataset.info)"
                                                                                >
                                                                                    <i class="bi bi-question-circle"></i>
                                                                                </a>
                                                                            </span>
                                                                        @endcan
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            {{-- vloženo kvůli zarovnání se zbylými řádky --}}
                                            @if ($this->showTimes)
                                                <div class="d-inline-block time p-2 ps-0">
                                                    &nbsp;
                                                </div>
                                            @endif
                                            <div class="p-2 d-inline-block">
                                                &mdash;
                                            </div>
                                        @endif
                                    </td>
                                    <td @class(['d-block', 'd-md-none', 'border-bottom', 'p-0', 'border-0', 'border-tertiary' => !$lastInWeek, 'border-dark' => $lastInWeek])></td>
                                    <td class="add d-none d-md-table-cell pt-0 pt-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-primary p-1 py-0 p-md-2 py-md-1" wire:click="add({{ $liturgicalDay->id }})" data-bs-toggle="tooltip" data-bs-title="{{ __('Zapsat píseň pro tento den') }}" @disabled($isEdit)>
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $this->liturgicalDays->links(data: ['scrollTo' => '#results']) }}
            </div>
        @endif
    </div>
    
    <div class="text-end mt-3">
        <a @class(['btn', 'btn-sm', 'btn-secondary', 'me-1', 'disabled' => $isEdit]) href="{{ route('organs.show', $this->organ->slug) }}" wire:navigate @disabled($isEdit)>
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>
        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl() }}">
            <i class="bi-share"></i> <span class="d-none d-sm-inline">{{ __('Sdílet') }}</span>
        </a>
    </div>
    
    <x-organomania.modals.share-modal :hintAppend="__('Sdílením seznamu písní sdílíte i varhany, ke kterým se seznam vztahuje. Každý, kdo obdrží odkaz, může písně i zapisovat.')" />
    <x-organomania.modals.liturgical-celebration-modal :$liturgicalCelebration />
        
    <div class="modal fade" id="worshipSongInfoModal" tabindex="-1" data-focus="false" aria-labelledby="worshipSongInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="worshipSongInfoLabel">{{ __('Podrobnosti o zapsané písni') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
                </div>
                <div class="modal-body">
                    <span class="text-body-secondary">{{ __('Píseň zapsal/a') }}:</span>
                    <br />
                    <span id="worshipSongInfo"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                </div>
            </div>
        </div>
    </div>
        
    <x-organomania.modals.confirm-modal
        id="confirmDeleteWorshipSongModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="deleteWorshipSong()"
    >
        {{ __('Opravdu chcete zapsanou píseň smazat?') }}
    </x-organomania.modals.confirm-modal>
        
    <x-organomania.toast toastId="saved">
        {{ __('Píseň byla úspěšně uložena.') }}
    </x-organomania.toast>
        
    <x-organomania.toast toastId="savedMany">
        {{ __('Písně byly úspěšně uloženy.') }}
    </x-organomania.toast>
        
    <x-organomania.toast toastId="deleted">
        {{ __('Píseň byla úspěšně smazána.') }}
    </x-organomania.toast>
</div>


@script
<script>
    window.deleteWorshipSong = function () {
        var worshipSongId = confirmModal.getInvokeButton('confirmDeleteWorshipSongModal').dataset.worshipSongId
        $wire.delete(worshipSongId)
    }
        
    window.addSong = function (songId) {
        let songIds = $('#songId').val()
        $('#songId').val([...songIds, songId])
        $('#songId').trigger('change')
        return false
    }
</script>
@endscript
