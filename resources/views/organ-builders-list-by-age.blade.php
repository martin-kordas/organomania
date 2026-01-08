@use(App\Helpers)
@use(App\Repositories\OrganBuilderRepository)

@php
    $repository = app(OrganBuilderRepository::class);
    
    $century = request()->query('century');
    if (isset($century)) $century = (int)$century;
    
    $timelineItems = $repository->getOrganBuilderTimelineItemsByAge(century: $century);
    $averageAge = $timelineItems->pluck('age')->avg();
    $medianAge = $timelineItems->pluck('age')->median();
@endphp

<x-app-bootstrap-layout title="{{ __('Varhanáři podle věku dožití') }}">
    @push('meta')
        <meta name="description" content="{{ __('Zjistěte, kteří známí varanáři se dožili nejvyššího věku a kteří naopak zemřeli brzy.') }}">
    @endpush
    
    <div class="organ-builders-list-by-age container">
        <h3>{{ __('Varhanáři podle věku dožití') }}</h3>

        <div class="mb-2">
            {{ __('Jen narození ve století') }}:
            &nbsp;
            <select class="form-select form-select-sm w-auto d-inline-block" onchange="location.href=`./list-by-age?century=${this.value}`">
                <option value="">{{ __('Zvolte století') }}&hellip;</option>
                @foreach ([17, 18, 19, 20] as $century1)
                    <option @selected($century1 == $century) value="{{ $century1 }}">
                        {{ $century1 }} {{ __('stol.') }}
                    </option>
                @endforeach
            </select>
        </div>
  
        <div class="mb-2">
            {{ __('Celkem osob ve statistice') }}: <span class="fw-semibold">{{ $timelineItems->count() }}</span>
        </div>
        
        <div>
            {{ __('Průměrný věk dožití') }}:
            <span class="fw-semibold">{{ Helpers::formatNumber($averageAge, decimals: 2) }}</span>
            {{ __('let') }}
        </div>
        <div class="mb-3">
            {{ __('Medián věku dožití') }}:
            <span class="fw-semibold">{{ Helpers::formatNumber($medianAge, decimals: 2) }}</span>
            {{ __('let') }}
        </div>

        <x-organomania.warning-alert class="mb-2 d-print-none d-inline-block">
            {{ __('Zobrazený věk je přibližný – vypočítává se pouze z roku narození a úmrtí a nebere v potaz konkrétní den.') }}
        </x-organomania.warning-alert>

        <table class="table table-hover table-sm w-auto">
            <thead>
                <tr>
                    <th class="text-end">{{ __('Č.') }}</th>
                    <th>{{ __('Varhanář') }}</th>
                    <th>{{ __('Narozen') }}</th>
                    <th>{{ __('Zemřel') }}</th>
                    <th class="text-end">{{ __('Věk dožití') }} <i class="bi-sort-numeric-down-alt"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($timelineItems as $i => $timelineItem)
                    <tr>
                        <td class="text-end">{{ $i + 1 }}.</td>
                        <td>
                            <x-organomania.organ-builder-link :organBuilder="$timelineItem->organBuilder" :timelineItem="$timelineItem" :name="$timelineItem->name" :showDescription="false" />
                        </td>
                        <td class="text-end">*{{ $timelineItem->year_from }}</td>
                        <td class="text-end">†{{ $timelineItem->year_to }}</td>
                        <td class="text-end fw-semibold">{{ $timelineItem->age }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-bootstrap-layout>