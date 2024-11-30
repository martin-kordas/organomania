@props(['organ' => null, 'modal' => false])

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a class="link-dark link-underline-opacity-10 link-underline-opacity-50-hover" href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                    <strong>{{ $organ->name }}</strong>
                </a>
            </h5>
            <div class="mb-1">
            @isset($organ->locality)
                {{ $organ->locality }}
                @isset($organ->place)
                    | {{ $organ->place }}
                @endisset
            @endisset
            </div>
            <div class="stars">
                <span @class(['text-body-secondary', 'mark' => $organ->shouldHighlightFrequency()])>
                    {{ $organ->frequency }}
                </span>
                <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Význam') }}" countAll="3" :count="$organ->importance" />
            </div>
        @endisset
    </x-slot:header>
    
    @isset($organ)
        @isset($organ->url)
            <p class="mb-0">
                <a class="icon-link" href="{{ $organ->url }}" target="_blank">
                    <i class="bi bi-link-45deg"></i>
                    {{ str($organ->url)->limit(40) }}
                </a>
            </p>
        @endisset

        @isset($organ->organ)
            <p @class(['mb-0', 'mt-2' => isset($organ->organ)])>
                Varhany:
                <x-organomania.organ-organ-builder-link :organ="$organ->organ" />
            </p>
        @endisset

        @isset($organ->perex)
            <p @class(['card-text', 'mt-2' => isset($organ->url) || isset($organ->organ)])>
                {{ str($organ->perex)->limit(215) }}
            </p>
        @endisset
    @endisset
    
</x-organomania.thumbnail>
