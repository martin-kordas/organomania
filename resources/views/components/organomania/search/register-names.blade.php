@props(['registerNames'])

<div class="card-header fw-bold">
    <i class="bi-record-circle"></i> {{ __('Rejstříky') }}
</div>
<div class="list-group list-group-flush">
    @foreach ($registerNames as $registerName)
        <a
            class="list-group-item list-group-item-action item-focusable d-flex column-gap-1 align-items-center"
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