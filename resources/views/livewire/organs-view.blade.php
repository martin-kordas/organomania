<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Reactive;
use App\Models\Organ;
use App\Models\OrganLike;

new class extends Component {
    
    use WithPagination;

    #[Reactive]
    public $viewType = 'thumbnails';

    #[Reactive]
    public $sortColumn = 'importance';
    #[Reactive]
    public $sortDirection = 'desc';

    #[Reactive]
    public $filterCategories;
    #[Reactive]
    public $filterOrganBuilderId;
    #[Reactive]
    public $filterRegionId;
    #[Reactive]
    public $filterFavorite;
    #[Reactive]
    public $filterPrivate;
    
    public function mount()
    {
        // TODO: sloupce korespondují s SORT_OPTIONS v organs, ale nevím, jak je zde načíst
        if (!in_array($this->sortColumn, ['importance', 'year_built', 'manuals_count', 'stops_count', 'municipality'])) {
            throw new \RuntimeException;
        }
    }

    #[Computed]
    public function organs()
    {
        return Organ::with(['region', 'organBuilder', 'organCategories'])
            ->withCount([
                'organLikes',
                'organLikes as my_organ_likes_count' => function (Builder $query) {
                    $query->where('user_id', Auth::id());
                }
            ])
            ->when(
                $this->filterCategories,
                fn(Builder $query) => $query->whereHas('organCategories', function (Builder $query) {
                    $query->whereIn('id', $this->filterCategories);
                })
            )
            ->when(
                $this->filterOrganBuilderId,
                fn(Builder $query) => $query->where('organ_builder_id', $this->filterOrganBuilderId)
            )
            ->when(
                $this->filterRegionId,
                fn(Builder $query) => $query->where('region_id', $this->filterRegionId)
            )
            ->when(
                $this->filterFavorite,
                fn(Builder $query) => $query->whereHas('organLikes', function (Builder $query) {
                    return $query->where('user_id', Auth::id());
                })
            )
            ->when(
                $this->filterPrivate,
                fn(Builder $query) => $query->whereNotNull('user_id')
            )
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate(6);
    }

    
    #[Computed]
    public function viewComponent()
    {
        return match ($this->viewType) {
            'thumbnails' => 'organomania.organs-view-thumbnails',
            'table' => 'organomania.organs-view-table',
            default => throw new \LogicException
        };
    }

    public function placeholder()
    {
        $loading = __('Načítání...');
        return <<<HTML
        <div class="text-secondary d-flex justify-content-center mt-5" role="status">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">$loading&hellip;</span>
            </div>
        </div>
        HTML;
    }

    // protože komponenta je lazy
    public function rendered()
    {
        $this->dispatch("bootstrap-rendered");
    }

    public function likeToggle($organId)
    {
        $organ = $this->organs->firstWhere('id', $organId);
        if (!$organ) throw new \RuntimeException;

        if ($organ->my_organ_likes_count <= 0) {
            $organLike = new OrganLike(['user_id' => Auth::id()]);
            $organ->organLikes()->save($organLike);
            $diff = 1;
        }
        else {
            $organLike = $organ->organLikes()->where('user_id', Auth::id())->first();
            if ($organLike) $organLike->delete();
            $diff = -1;
        }
        unset($this->organs);
        $this->dispatch('organ-like-updated', $diff);
    }
    
}; ?>

<div class="container align-center">
    @if ($this->organs->isEmpty())
        <div class="alert alert-light text-center" role="alert">
            {{ __('Nebyly nalezeny žádné varhany.') }}
        </div>
    @else
        <x-dynamic-component :component="$this->viewComponent" :organs="$this->organs" />
        {{ $this->organs->links() }}
    @endif
</div>
