@props(['organ' => null, 'modal' => false])

@php
    use App\Helpers;
@endphp

<x-organomania.thumbnail :$organ :$modal>
    
    <x-slot:header>
        @isset($organ)
            <h5 class="card-title">
                <a
                    @class(['link-dark', 'link-underline-opacity-25', 'link-underline-opacity-75-hover', 'not-preserved' => !$organ->preserved_case])
                    href="{{ $this->getViewUrl($organ) }}"
                    @if ($modal) target="_blank" @else wire:navigate @endif
                ><strong>{{ $organ->municipality }}</strong> | {{ $organ->place }}</a>
                @if (!$organ->preserved_organ)
                    <span class="text-body-secondary fw-normal">
                        ({{ $organ->preserved_case ? __('dochována skříň') : __('nedochováno') }})
                    </span>
                @endif
                
                @if ($organ->user_id)
                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                        <i class="bi-lock text-warning"></i>
                    </span>
                @endif
            </h5>
            <div class="mb-1">
                @if (isset($organ->caseOrganBuilder))
                    <x-organomania.organ-builder-link :organBuilder="$organ->caseOrganBuilder" :yearBuilt="$organ->case_year_built" :isCaseBuilt="true" />
                    <br />
                @elseif (isset($organ->case_organ_builder_name))
                    <i class="bi bi-person-circle"></i>
                    @if ($organ->case_organ_builder_name === 'neznámý')
                        <span class="text-body-secondary">{{ __('neznámý') }}</span>
                    @else
                        {{ $organ->case_organ_builder_name }}
                    @endif
                    @php $details = []; if ($organ->case_year_built) $details[] = $organ->case_year_built; $details[] = __('varhanní skříň'); @endphp
                    <span class="text-secondary">({{ implode(', ', $details) }})</span>
                    <br />
                @endif
                
                <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" :yearBuilt="$organ->year_built" :showOrganWerk="isset($organ->caseOrganBuilder) || isset($organ->case_organ_builder_name)" />
                
                @foreach ($organ->organRebuilds as $rebuild)
                    @if ($rebuild->organBuilder)
                        <br />
                        <x-organomania.organ-builder-link :organBuilder="$rebuild->organBuilder" :yearBuilt="$rebuild->year_built" :isRebuild="true" />
                    @endif
                @endforeach
            </div>
            <div class="stars">
                @isset($organ->manuals_count)
                    <span class="text-body-secondary">
                        {{ $organ->manuals_count }} <small>{{ $organ->getDeclinedManuals() }}</small>
                        @if ($organ->stops_count)
                            / {{ $organ->stops_count }} <small>{{ $organ->getDeclinedStops() }}</small>
                        @endif
                    </span>
                @endisset
                @if (!$organ->shouldHideImportance())
                    <x-organomania.stars class="float-end" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="{{ __('Význam') }}" :count="round($organ->importance / 2)" />
                @endif
            </div>
        @endisset
    </x-slot:header>
    
</x-organomania.thumbnail>
