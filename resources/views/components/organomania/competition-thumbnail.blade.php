@props(['organ' => null, 'modal' => false])

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a
                    class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover"
                    href="{{ $this->getViewUrl($organ) }}"
                    @if ($modal) target="_blank" @else wire:navigate @endif
                >
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
            <div class="stars text-body-secondary">
                <span>
                    {{ $organ->frequency }}
                </span>
                @isset($organ->next_year)
                    <span class="float-end">
                        {{ __('příště') }}:
                        <span @class(['mark' => $organ->shouldHighlightNextYear()])>
                            {{ $organ->next_year }}
                        </span>
                    </span>
                @endisset
            </div>
        @endisset
    </x-slot:header>
    
    @isset($organ)
        @isset($organ->url)
            <p class="mb-0">
                <a class="icon-link text-break" href="{{ $organ->url }}" target="_blank">
                    <i class="bi bi-link-45deg"></i>
                    {{ str($organ->url)->limit(40) }}
                </a>
            </p>
        @endisset

        @isset($organ->perex)
            <p @class(['card-text', 'mt-2' => isset($organ->url) || isset($organ->organ)])>
                {{ str($organ->perex)->limit(215) }}
            </p>
        @endisset
    @endisset
    
</x-organomania.thumbnail>
