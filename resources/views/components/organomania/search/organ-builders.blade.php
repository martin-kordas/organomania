@props(['organBuilders'])

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