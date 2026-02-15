@use(App\Models\User)

@props(['additionalImages'])

<div class="card-header fw-bold">
    <i class="bi-music-note-list"></i>
    {{ __('Varhany') }}
</div>

<div class="list-group list-group-flush" wire:key="additionalImages">
    @foreach ($additionalImages as $additionalImage)
        <a class="list-group-item list-group-item-action item-focusable" href="{{ $additionalImage->getViewUrl() }}" wire:key="additionalImage{{ $additionalImage->id }}" wire:navigate>
            {!! $this->highlight($additionalImage->name) !!}

            <small class="hstack text-secondary">
                <span>
                    {!! $this->highlight($additionalImage->organ_builder_name ?? $additionalImage->organBuilder?->name ?? __('neznámý varhanář')) !!}
                    @isset($additionalImage->year_built)
                        ({{ $additionalImage->year_built }})
                    @endisset
                </span>
            </small>
        </a>
    @endforeach
</div>
