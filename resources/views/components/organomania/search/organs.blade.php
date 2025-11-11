@use(App\Models\User)

@props(['organs', 'showLastViewed' => false])

<div class="card-header fw-bold">
    <i class="bi-music-note-list"></i> 
    @if ($showLastViewed)
        {{ __('Poslední zobrazené varhany') }}
    @else
        {{ __('Varhany') }}
    @endif
</div>
<div class="list-group list-group-flush">
    @foreach ($organs as $organ)
        <a class="list-group-item list-group-item-action" href="{{ $organ->getViewUrl() }}" wire:navigate>
            @if ($this->showLastViewed)
                <i class="bi-clock-history"></i>
            @endif
            {!! $this->highlight($organ->municipality) !!}, {!! $this->highlight($organ->place) !!}
            @if ($organ->showAsPrivate())
                <i class="bi-lock text-warning"></i>
            @endif
            @if ($organ->baroque)
                <span class="badge text-bg-light text-wrap">{{ __('Barokní varhanářství na Moravě') }}</span>
            @endif
            <br />
            <small class="hstack text-secondary">
                <span>
                    {!! $this->highlight($organ->organ_builder_name ?? $organ->organBuilder?->name ?? __('neznámý varhanář')) !!}
                    @isset($organ->year_built)
                        ({{ $organ->year_built }})
                    @endisset
                </span>
                @if (!$organ->shouldHideImportance())
                    <x-organomania.stars class="ms-auto" :count="round($organ->importance / 2)" />
                @endif
            </small>
        </a>
    @endforeach
</div>