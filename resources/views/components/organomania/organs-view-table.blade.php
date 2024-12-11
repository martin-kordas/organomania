@props(['organs'])

<div class="table-responsive">
    <table class="table table-hover table-sm align-middle">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <x-organomania.sortable-table-heading :sortOption="$this->getSortOption('municipality')" :sticky="true" />
                <th>{{ __('Místo') }}</th>
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
                        <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                            {{ $organ->municipality }}
                        </a>
                    </td>
                    <td class="fw-semibold">
                        <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($organ) }}" wire:navigate>
                            {{ $organ->place }}
                        </a>
                    </td>
                    <td data-bs-toggle="tooltip" data-bs-title="{{ $organ->region->name }}">
                        <img width="70" class="region me-1" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" />
                    </td>
                    <td>
                        <x-organomania.organ-builder-link :organBuilder="$organ->organBuilder" placeholder="{{ __('neznámý') }}" :showIcon="false" />
                    </td>
                    <td class="text-end">{{ $organ->year_built }}</td>
                    <td class="text-end">{{ $organ->manuals_count }}</td>
                    <td class="text-end">{{ $organ->stops_count }}</td>
                    <td>
                        @foreach ($organ->organCategories as $category)
                            @if (!$category->getEnum()->isPeriodCategory() && !$category->getEnum()->isTechnicalCategory())
                                <x-organomania.category-badge :category="$category->getEnum()" />
                            @endif
                        @endforeach
                    </td>
                    <td class="text-nowrap">
                        <x-organomania.stars class="responsive" :count="round($organ->importance / 2)" :showCount="true" />
                    </td>
                    <td class="text-nowrap">
                        <x-organomania.entity-page-view-table-buttons :record="$organ" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>