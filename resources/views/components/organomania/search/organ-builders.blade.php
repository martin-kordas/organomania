@props(['organBuilders', 'limit'])

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
            @if ($organBuilder->baroque)
                <span class="badge text-bg-light text-wrap">{{ __('Barokní varhanářství na Moravě') }}</span>
            @endif
            @if ($organBuilder->active_period)
                <span class="text-secondary">({{ $organBuilder->active_period }})</span>
                <br />
                <small class="hstack text-secondary">
                    <span>{!! $this->highlight($organBuilder->municipality) !!}</span>
                    @if (!$organBuilder->shouldHideImportance())
                        <x-organomania.stars class="ms-auto" :count="round($organBuilder->importance / 2)" />
                    @endif
                </small>
            @endif
        </a>
    @endforeach
</div>

@if ($organBuilders->count() >= $limit)
    <div class="list-group list-group-flush position-relative text-center border-top-0">
        <div class="list-group-item list-group-item-action">
            <a type="submit" class="link-primary text-decoration-none stretched-link" href="{{ route('organ-builders.index', ['filterSearch' => $this->sanitizedSearch, 'search' => 1, 'viewType' => 'table', 'perPage' => 30]) }}">
                <i class="bi-person-circle"></i>
                {{ __('Zobrazit vše') }}
            </a>
        </div>
    </div>
@endif
