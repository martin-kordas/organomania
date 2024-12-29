<?php

use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Helpers;
use App\Models\Like;
use App\Models\Organist;
use App\Traits\HasSorting;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasSorting;
    use WithPagination;

    #[Url(keep: true)]
    public $filter;
    #[Url(keep: true)]
    public $filterFavorite;

    public int $favoriteCount;

    const SORT_OPTIONS = [
        ['column' => 'name', 'label' => 'Jméno', 'type' => 'alpha'],
        ['column' => 'year_of_birth', 'label' => 'Rok narození', 'type' => 'numeric'],
        ['column' => 'subscribers_count', 'label' => 'Počet diváků', 'type' => 'numeric', 'directions' => ['desc']],
        ['column' => 'videos_count', 'label' => 'Počet videí', 'type' => 'numeric', 'directions' => ['desc']],
        ['column' => 'last_video_date', 'label' => 'Nejnovější video', 'type' => 'numeric', 'directions' => ['desc']],
    ];

    public function rendering(View $view): void
    {
        $view->title(__('Varhaníci českého Youtube'));
    }

    public function updated($property)
    {
        if (in_array($property, ['filter', 'filterFavorite'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        if (request()->query('sortColumn') === null) $this->sortColumn = 'last_video_date';
        $this->favoriteCount = $this->getFavoriteCount();
    }

    #[Computed]
    public function organists()
    {
        $query = Organist::withCount([
            'likes',
            'likes as my_likes_count' => function (Builder $query) {
                $query->where('user_id', Auth::id());
            }
        ]);

        if ($this->filter && mb_strlen($this->filter) >= 3) {
            $query->whereAny(['first_name', 'last_name', 'occupation'], 'LIKE', "%{$this->filter}%");
        }

        if ($this->filterFavorite) {
            $query->whereHas('likes', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
        }

        switch ($this->sortColumn) {
            case 'name':
                $query->orderBy('last_name', $this->sortDirection);
                $query->orderBy('first_name', $this->sortDirection);
                break;

            case 'year_of_birth':
            case 'subscribers_count':
            case 'videos_count':
            case 'last_video_date':
                $query->orderBy(DB::raw("`{$this->sortColumn}` IS NULL"));
                $query->orderBy($this->sortColumn, $this->sortDirection);
                break;

            default:
                throw new RuntimeException;
        }
        $query->orderBy('id');

        return $query->paginate(15);
    }

    public function sort1($column, $direction)
    {
        $this->sort($column, $direction);
        $this->resetPage();
    }

    public function likeToggle($organistId)
    {
        $organist = $this->organists->firstWhere('id', $organistId);
        if (!$organist) throw new \RuntimeException;
        Gate::authorize('likeOrganists');

        if ($organist->my_likes_count <= 0) {
            $like = new Like(['user_id' => Auth::id()]);
            $organist->likes()->save($like);
            $diff = 1;
            $this->js('showToast("likedToast")');
        }
        else {
            $like = $organist->likes()->where('user_id', Auth::id())->first();
            if ($like) $like->delete();
            $diff = -1;
            $this->js('showToast("unlikedToast")');
        }
        unset($this->organists);
        $this->updateFavoriteCount($diff);
    }

    private function getFavoriteCount()
    {
        return Organist::whereHas('likes', function (Builder $query) {
            $query->where('user_id', Auth::id());
        })->count();
    }

    public function updateFavoriteCount(int $diff)
    {
        $this->favoriteCount += $diff;
    }

    private function isOrganistLiked(Organist $organist)
    {
        return $organist->my_likes_count > 0;
    }

    private function formatSubscribersCount($count)
    {
        if ($count >= 1000) {
            $countRounded = round($count / 1000, 1);
            return Helpers::formatNumber($countRounded) . ' ' . __('tis.');
        }
        return $count;
    }

}; ?>

<div class="organists container">
  
    @push('meta')
        <meta name="description" content="{{ __('Objevte kvalitní volně dostupné nahrávky koncertních varhaníků z České republiky.') }}">
    @endpush
        
    <div class="d-sm-flex gap-3">
        <div class="mb-3 align-self-end float-end float-sm-none order-2 ms-3 ms-md-0">
            <a class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 6.1em;">
                <i class="bi-sort-up"></i>
                <br />{{ __('Seřazení') }}
                <br />
                <span class="badge text-bg-primary text-wrap">{{ $this->getSortLabel() }}</span>
                <br />
            </a>

            <ul class="dropdown-menu shadow-sm sort-dropdown">
                @foreach (static::SORT_OPTIONS as $sortOption)
                    @php $directions = $sortOption['directions'] ?? ['asc', 'desc'] @endphp
                    @if (in_array('asc', $directions))
                        <li>
                            <a href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'asc')]) wire:click="sort1('{{ $sortOption['column'] }}', 'asc')">
                                {{ __($sortOption['label']) }} ({{ __('vzestupně') }})
                                <i class="float-end bi-sort-{{ $sortOption['type'] }}-up"></i>
                            </a>
                        </li>
                    @endif
                    @if (in_array('desc', $directions))
                        <li>
                            <a href="#" @class(['dropdown-item', 'active' => $this->isCurrentSort($sortOption['column'], 'desc')]) wire:click="sort1('{{ $sortOption['column'] }}', 'desc')">
                                {{ __($sortOption['label']) }} ({{ __('sestupně') }})
                                <i class="float-end bi-sort-{{ $sortOption['type'] }}-down-alt"></i>
                            </a>
                        </li>
                    @endif
                    @if (!$loop->last)
                        <li><hr class="dropdown-divider"></li>
                    @endif
                @endforeach
            </ul>
        </div>
        
        <div class="me-auto order-1">
            <h3>{{ __('Varhaníci českého Youtube') }}</h3>

            <em class="text-body-secondary">
                {{ __('Posláním stránky je upozorňovat na kvalitní volně dostupné nahrávky našich koncertních varhaníků.') }}
            </em>
            
            <div class="hstack gap-3 my-3">
                <input class="form-control" type="search" wire:model.live="filter" placeholder="{{ __('Hledat') }}&hellip;" autofocus="true" />
                @can('likeOrganists')
                    <span class="text-nowrap">
                        <input id="quickFilterFavorite" type="checkbox" class="btn-check" wire:model.live="filterFavorite" autocomplete="off">
                        <label for="quickFilterFavorite" class="btn btn-outline-danger rounded-pill" for="btn-check" data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit jen oblíbené') }}">
                            <i class="bi-heart"></i> {{ $this->favoriteCount }}
                        </label>
                    </span>
                @endcan
            </div>
        </div>
    </div>
    
    @if ($this->organists->isEmpty())
        <br />
        <div class="alert alert-secondary text-center" role="alert">
            {{ __('Nebyli nalezeni žádní varhaníci.') }}
        </div>
    @else
        <div class="list-group mt-2 mb-3 gap-3" style="clear: right">
            @foreach ($this->organists as $organist)
                <div class="organist list-group-item list-group-item-action p-0 border-1 rounded" style="cursor: pointer" onclick="openChannel(event)" data-channel-url="{{ $organist->channelUrl }}">
                    <div class="card border-0 p-0 bg-transparent">
                        <div class="card-body d-flex flex-wrap align-items-center flex-md-nowrap gap-3">
                            @isset($organist->avatar_url)
                                @php $showAvatar = $organist->localAvatarExists(); @endphp
                                <div class="avatar-container d-flex w-100 flex-md-column align-items-center">
                                    @if ($showAvatar)
                                        <img
                                            class="avatar small rounded-circle me-auto me-md-0"
                                            src="{{ $organist->getLocalAvatarUrl() }}"
                                            alt="{{ __('Avatar kanálu') }} {{ $organist->channel_username }}, {{ __('získaný z Youtube') }}"
                                        />
                                    @endif
                                    <div @class(['organist-stats', 'text-end', 'text-md-center', 'text-body-secondary', 'mt-md-2' => $showAvatar])>
                                        @isset($organist->subscribers_count)
                                            <div class="text-nowrap">
                                                {{ $this->formatSubscribersCount($organist->subscribers_count) }}
                                                <small>{{ Helpers::declineCount($organist->subscribers_count, __('diváků'), __('divák'), __('diváci')) }}</small>
                                            </div>
                                        @endisset
                                        @isset($organist->videos_count)
                                            <div class="text-nowrap">
                                                {{ $organist->videos_count }}
                                                <small>{{ Helpers::declineCount($organist->videos_count, __('videí'), __('video'), __('videa')) }}</small>
                                            </div>
                                        @endisset
                                        <div class="mt-2 text-md-center">
                                            <a
                                                @class(['btn', 'btn-sm', 'rounded-pill', 'z-1', 'btn-danger' => $this->isOrganistLiked($organist), 'btn-outline-danger' => !$this->isOrganistLiked($organist)])
                                                @can('likeOrganists')
                                                    wire:click="likeToggle({{ $organist->id }})"
                                                @else
                                                    href="{{ route('login') }}"
                                                @endcan
                                                data-bs-toggle="tooltip"
                                                data-bs-title="{{ __('Přidat do oblíbených') }}"
                                            >
                                                <i class="bi-heart align-middle"></i>
                                                @if ($organist->likes_count > 0)
                                                    {{ $organist->likes_count }}
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endisset

                            <div class="flex-grow-1">
                                <div class="border-bottom">
                                    <h5 class="card-title">
                                        <a class="text-decoration-none link-dark" href="{{ $organist->channelUrl }}" target="_blank">
                                            <i class="bi bi-youtube text-danger"></i>
                                            @if ($this->sortColumn === 'name')
                                                {{ $organist->last_name }}, {{ $organist->first_name }}
                                            @else
                                                {{ $organist->first_name }} {{ $organist->last_name }}
                                            @endif
                                            @isset($organist->year_of_birth)
                                                <span class="fw-normal text-body-tertiary">(*{{ $organist->year_of_birth }})</span>
                                            @endisset
                                        </a>
                                    </h6>
                                    @isset($organist->occupation)
                                        <h6 class="small card-subtitle pre-line mb-2 text-body-secondary lh-base">{{ $organist->occupation }}</h6>
                                    @endisset
                                </div>
                                <div class="card-text mt-2">
                                    @isset($organist->channel_character)
                                        <div class="mb-1">
                                            <em>{{ __('Zaměření kanálu') }}:</em> {{ $organist->channel_character }}
                                        </div>
                                    @endisset
                                    @isset($organist->last_video_id)
                                        <div>
                                            <em>{{ __('Nejnovější video') }}</em> <span class="text-body-secondary">({{ Helpers::formatDate($organist->last_video_date) }})</span>
                                            <br />
                                            <a class="icon-link icon-link-hover align-items-start text-decoration-none" href="{{ $organist->lastVideoUrl }}" target="_blank">
                                                <i class="bi bi-play-circle"></i>
                                                {{ $organist->last_video_name }}
                                            </a>

                                        </div>
                                    @endisset
                                </div>
                                <div class="border-top mt-2 px-0 pt-2">
                                    @isset($organist->facebook)
                                        <a class="icon-link me-1" href="{{ $organist->facebookUrl }}" target="_blank">
                                            <i class="bi bi-facebook"></i>
                                        </a>
                                    @endisset
                                    @isset($organist->web)
                                        <x-organomania.web-link :url="$organist->web" icon="globe-americas" />
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $this->organists->links() }}
        
        <div class="small text-body-secondary text-center">
            {{ __('Chybí zde varhaník?') }}
            <br />
            <a class="link-primary text-decoration-none" href="mailto:{{ config('custom.app_admin_email') }}?subject={{ __('Návrh na přidání varhaníka') }}">{{ __('Napište nám!') }}</a>
        </div>
    @endif
        
        
    <x-organomania.toast toastId="likedToast">
        {{ __('Varhaník byl přidán do oblíbených.') }}
    </x-organomania.toast>
    <x-organomania.toast toastId="unlikedToast">
        {{ __('Varhaník byl odebrán z oblíbených.') }}
    </x-organomania.toast>
</div>

@script
<script>
    window.openChannel = function (e) {
        if (!$(e.target).closest('a, button').length) {
            let url = $(e.currentTarget).data('channelUrl')
            window.open(url, '_blank')
        }
    }
</script>
@endscript