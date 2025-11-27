@props(['organs'])

@use(App\Helpers)
@use(App\Models\OrganBuilder)

<div class="table-responsive">
    <table class="table table-hover table-sm align-middle">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('municipality')" :sticky="true" />
                <th style="min-width: 9em;">{{ __('Místo') }}</th>
                @if ($this->hasDistance)
                    <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('distance')" />
                @endif
                <th>{{ __('Kraj') }}</th>
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('organ_builder')" />
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('year_built')" />
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('manuals_count')" />
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('stops_count')" />
                <th>{{ __('Kategorie') }}</th>
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('importance')" />
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @foreach ($organs as $organ)
                <tr>
                    <td>
                        @if ($organ->user_id)
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                                <i class="bi-lock text-warning"></i>
                            </span>
                        @endif
                    </td>
                    <td class="table-light fw-semibold position-sticky start-0">
                        <a @class(['link-dark', 'link-underline-opacity-25', 'link-underline-opacity-75-hover', 'not-preserved' => !$organ->preserved_case]) href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                            {!! $this->highlight($organ->municipality, $this->search) !!}
                        </a>
                    </td>
                    <td class="fw-semibold" style="min-width: 9em;">
                        <a @class(['link-dark', 'link-underline-opacity-25', 'link-underline-opacity-75-hover', 'position-relative', 'not-preserved' => !$organ->preserved_case]) href="{{ $this->getViewUrl($organ) }}" wire:navigate>{!! $this->highlight($organ->shortPlace, $this->search) !!}</a>
                        @if (!$organ->preserved_organ)
                        <span class="text-body-secondary fw-normal">
                            ({{ $organ->preserved_case ? __('dochována skříň') : __('nedochováno') }})
                        </span>
                        @endif
                        @if ($organ->isPromoted())
                            @php $highlightPromoted = $organ->isPromoted(highlighted: true) @endphp
                            <span @class(['badge', 'text-uppercase', 'align-top', $highlightPromoted ? 'text-bg-danger' : 'text-bg-secondary']) style="font-size: 55%">
                                {{ __('Nové') }}
                            </span>
                        @endif
                    </td>
                    @if ($this->hasDistance)
                        <td class="text-end">
                            @if (!$this->isFilterNearCenter($organ))
                                {{ Helpers::formatNumber($organ->distance / 1000, decimals: 1) }}&nbsp;<span class="text-body-secondary">km</span>
                                <i class="bi bi-arrow-up d-inline-block text-info" style="transform: rotate({{ $organ->angle }}deg)"></i>
                            @endif
                        </td>
                    @endif
                    <td data-bs-toggle="tooltip" data-bs-title="{{ $organ->region?->name }}">
                        @isset($organ->region_id)
                            <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
                        @endisset
                    </td>
                    <td>
                        @isset($organ->organ_builder_name)
                            <i class="bi bi-person-circle"></i>
                            {{ $organ->organ_builder_name }}
                        @else
                            <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" placeholder="{{ __('neznámý') }}" :iconLink="false" />
                        @endisset
                        
                        @if ($organ->organRebuilds->isNotEmpty())
                            <span class="text-body-secondary">(přestavěno)</span>
                        @endif
                    </td>
                    <td class="text-end">{{ $organ->year_built }}</td>
                    <td class="text-end">
                        @isset($organ->manuals_count)
                            {{ Helpers::formatRomanNumeral($organ->manuals_count) }}
                        @endisset
                    </td>
                    <td class="text-end">{{ $organ->stops_count }}</td>
                    <td>
                        @foreach ($organ->organCategories as $category)
                            @if (!$category->getEnum()->isPeriodCategory() && !$category->getEnum()->isCaseCategory() && !$category->getEnum()->isTechnicalCategory())
                                <x-organomania.category-badge :category="$category->getEnum()" shortName />
                            @endif
                        @endforeach
                    </td>
                    <td class="text-nowrap">
                        @if (!$organ->shouldHideImportance())
                            <x-organomania.stars class="responsive" :count="round($organ->importance / 2)" :showCount="true" />
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <x-organomania.entity-page-view-table-buttons :record="$organ" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>