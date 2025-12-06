@use(App\Models\User)

@props(['organs', 'limit', 'showLastViewed' => false])

<div class="card-header fw-bold">
    <i class="bi-music-note-list"></i> 
    @if ($showLastViewed)
        {{ __('Poslední zobrazené varhany') }}
    @else
        {{ __('Varhany') }}
    @endif
</div>

<div class="list-group list-group-flush" wire:key="organsxxx">
    @foreach ($organs as $organ)
        <a class="list-group-item list-group-item-action item-focusable" href="{{ $organ->getViewUrl() }}" wire:key="organ{{ $organ->id }}" wire:navigate>
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
                    {!! $this->highlight($organ->organ_builder_name ?? $organ->timelineItem?->name ?? $organ->organBuilder?->name ?? __('neznámý varhanář')) !!}
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

@if ($organs->count() >= $limit)
    <div class="list-group list-group-flush position-relative text-center border-top-0">
        <a class="list-group-item list-group-item-action link-primary text-decoration-none stretched-link item-focusable" href="{{ route('organs.index', ['filterLocality' => $this->sanitizedSearch, 'search' => $this->sanitizedSearch, 'viewType' => 'table', 'perPage' => 30]) }}">
            <i class="bi-music-note-list"></i>
            {{ __('Zobrazit vše') }}
        </a>
    </div>
@endif