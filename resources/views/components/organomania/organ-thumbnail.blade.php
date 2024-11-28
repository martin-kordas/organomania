@props(['organ' => null, 'modal' => false])

@php
    use App\Helpers;
@endphp

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a class="link-dark link-underline-opacity-10 link-underline-opacity-50-hover" href="{{ $this->getViewUrl($organ) }}">
                    <strong>{{ $organ->municipality }}</strong> | {{ $organ->place }}
                </a>
                @if ($organ->user_id)
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                        <i class="bi-lock text-warning"></i>
                    </span>
                @endif
            </h5>
            <div class="fst-italic mb-1">
                <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" :yearBuilt="$organ->year_built" />
                @foreach ($organ->organRebuilds as $rebuild)
                    @if ($rebuild->organBuilder)
                        <br />
                        <x-organomania.organ-builder-link :organBuilder="$rebuild->organBuilder" :yearBuilt="$rebuild->year_built" :isRebuild="true" />
                    @endif
                @endforeach
            </div>
            <div class="stars">
                <span class="text-body-secondary">
                    {{ $organ->getDeclinedManualsCount() }}
                    @if ($organ->stops_count)
                        / {{ $organ->getDeclinedStopsCount() }}
                    @endif
                </span>
                <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Význam') }}" :count="round($organ->importance / 2)" />
            </div>
        @endisset
    </x-slot:header>
    
</x-organomania.thumbnail>
