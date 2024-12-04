@props(['organ' => null, 'modal' => false])

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a class="link-dark link-underline-opacity-10 link-underline-opacity-50-hover" href="{{ $this->getViewUrl($organ) }}" wire:navigate><strong>{{ $organ->name }}</strong></a>
                @if ($organ->user_id)
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                        <i class="bi-lock text-warning"></i>
                    </span>
                @endif
                @isset($organ->active_period)
                    <small class="text-body-secondary">
                        ({{ $organ->active_period }})
                    </small>
                @endisset
            </h5>
            <div class="stars">
                <span class="text-body-secondary">
                    {{ $organ->municipality }}
                </span>
                @if (!$organ->shouldHideImportance())
                    <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Význam') }}" :count="round($organ->importance / 2)" />
                @endif
            </div>
        @endisset
    </x-slot:header>
    
</x-organomania.thumbnail>
