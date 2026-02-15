@use(App\Models\User)

@props(['additionalImages', 'showTitle' => true])

@if ($showTitle)
    <div class="card-header fw-bold">
        <i class="bi-music-note-list"></i>
        {{ __('Varhany') }}
    </div>
@endif

<div @class(['list-group', 'list-group-flush', 'border-top-0' => !$showTitle]) wire:key="additionalImages">
    @foreach ($additionalImages as $additionalImage)
        <a class="list-group-item list-group-item-action item-focusable text-secondary" href="{{ $additionalImage->getViewUrl() }}" wire:key="additionalImage{{ $additionalImage->id }}" wire:navigate>
            {!! $this->highlight($additionalImage->name) !!}

            @php
                $details = [];
                if ($additionalImage->year_built) $details[] = $additionalImage->year_built;
                // obvykle se ukazují zvukově dochované varhany a v additional_images mohou být jen dochované skříně, na což upozorníme
                if (str($additionalImage->details)->contains('dochována skříň')) $details[] = __('dochována skříň');
            @endphp
            <small class="hstack text-secondary">
                <span>
                    {!! $this->highlight($additionalImage->organ_builder_name ?? $additionalImage->organBuilder?->name ?? __('neznámý varhanář')) !!}
                    @if (!empty($details))
                        ({{ implode(', ', $details) }})
                    @endif
                </span>
            </small>
        </a>
    @endforeach
</div>
