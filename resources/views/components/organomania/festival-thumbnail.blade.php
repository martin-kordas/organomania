@props(['organ' => null, 'modal' => false])

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <strong>{{ $organ->name }}</strong>
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
                <span class="text-body-secondary">
                    {{ $organ->frequency }}
                </span>
                <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Význam" :count="round($organ->importance / 2)" />
            </div>
        @endisset
    </x-slot:header>
    
    @isset($organ)
        @isset($organ->url)
            <p class="mb-0">
                <a href="{{ $organ->url }}" target="_blank">
                    {{ $organ->url }}
                </a>
            </p>
        @endisset

        @isset($organ->organ)
            <p class="mb-0 @isset($organ->organ) mt-2 @endisset">
                Varhany:
                <x-organomania.organ-organ-builder-link :organ="$organ->organ" />
            </p>
        @endisset

    @endisset
    
</x-organomania.thumbnail>
