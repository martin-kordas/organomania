@props(['organs'])

<div class="organs container">
    <table class="table table-hover table-sm align-middle">
        <thead>
            <tr>
                <th>{{ __('Obec') }}</th>
                <th>{{ __('Místo') }}</th>
                <th>{{ __('Kraj') }}</th>
                <th>{{ __('Varhanář') }}</th>
                <th>{{ __('Rok stavby') }}</th>
                <th>{{ __('Počet rejstříků') }}</th>
                <th>{{ __('Počet manuálů') }}</th>
                <th>{{ __('Kategorie') }}</th>
                <th>{{ __('Význam') }}</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @foreach ($organs as $organ)
                <tr>
                    <td class="fw-semibold">{{ $organ->municipality }}</td>
                    <td class="fw-semibold">{{ $organ->place }}</td>
                    <td>{{ $organ->region->name }}</td>
                    <td>{{ $organ->organBuilder->name }}</td>
                    <td class="text-end">{{ $organ->year_built }}</td>
                    <td class="text-end">{{ $organ->stops_count }}</td>
                    <td class="text-end">{{ $organ->manuals_count }}</td>
                    <td>
                        @foreach ($organ->organCategories as $category)
                            @if (!$category->getEnum()->isPeriodCategory() && !$category->getEnum()->isTechnicalCategory())
                                <x-organomania.category-badge :category="$category->getEnum()" />
                            @endif
                        @endforeach
                    </td>
                    <td class="text-nowrap">
                        <x-organomania.stars :count="round($organ->importance / 2)" :showCount="true" />
                    </td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-primary text-nowrap" href="{{ route('organs.show', ['organ' => $organ->id]) }}"><i class="bi-eye"></i> Zobrazit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>