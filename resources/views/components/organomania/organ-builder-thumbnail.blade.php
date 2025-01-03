@props(['organ' => null, 'modal' => false, 'showOrgansTimeline' => false])

<x-organomania.thumbnail :$organ :$modal :$showOrgansTimeline>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a
                    class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover"
                    @if ($modal) target="_blank" @else wire:navigate @endif
                    href="{{ $this->getViewUrl($organ) }}"><strong>{{ $organ->name }}</strong></a>
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
