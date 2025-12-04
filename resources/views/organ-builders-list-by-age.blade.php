@use(App\Helpers)
@use(App\Repositories\OrganBuilderRepository)

@php
    $repository = app(OrganBuilderRepository::class);
    $timelineItems = $repository->getOrganBuilderTimelineItemsByAge();
    $averageAge = $timelineItems->pluck('age')->avg();
@endphp

<x-app-bootstrap-layout title="{{ __('Varhanáři podle věku dožití') }}">
    @push('meta')
        <meta name="description" content="{{ __('Zjistěte, kteří známí varanáři se dožili nejvyššího věku a kteří naopak zemřeli brzy.') }}">
    @endpush
    
    <div class="organ-builders-list-by-age container">
        <h3>{{ __('Varhanáři podle věku dožití') }}</h3>

        <x-organomania.warning-alert class="mb-2 d-print-none d-inline-block">
            {{ __('Zobrazený věk je přibližný – vypočítává se pouze z roku narození a úmrtí a nebere v potaz konkrétní den.') }}
        </x-organomania.warning-alert>

        <table class="table table-hover table-sm w-auto">
            <thead>
                <tr>
                    <th>{{ __('Varhanář') }}</th>
                    <th>{{ __('Narozen') }}</th>
                    <th>{{ __('Zemřel') }}</th>
                    <th class="text-end">{{ __('Věk dožití') }} <i class="bi-sort-numeric-down-alt"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timelineItems as $timelineItem)
                    <tr>
                        <td>
                            <x-organomania.organ-builder-link :organBuilder="$timelineItem->organBuilder" :name="$timelineItem->name" :showDescription="false" />
                        </td>
                        <td class="text-end">{{ $timelineItem->year_from }}</td>
                        <td class="text-end">{{ $timelineItem->year_to }}</td>
                        <td class="text-end fw-semibold">{{ $timelineItem->age }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div>
            {{ __('Průměrná délka dožití') }}:
            <span class="fw-semibold">{{ Helpers::formatNumber($averageAge, decimals: 2) }}</span>
            {{ __('let') }}
        </div>
        
    </div>
</x-app-bootstrap-layout>