@use(App\Models\Organ)

@php
$parts = [
    ['name' => 'Postament', 'top' => '59.7%', 'left' => '26%'],
    ['name' => 'Výplň', 'top' => '52.5%', 'left' => '30%'],
    ['name' => 'Boční konzola', 'top' => '46.25%', 'left' => '10%'],
    ['name' => 'Prospektová římsa', 'top' => '42.55%', 'left' => '12%'],
    ['name' => 'Křídlová řezba', 'top' => '20.6%', 'left' => '3%'],
    ['name' => 'Korunní římsa', 'top' => '11.5%', 'left' => '6.4%'],
    ['name' => 'Píšťalové pole', 'top' => '27.8%', 'left' => '23%'],
    ['name' => 'Etážové mezipole', 'top' => '20.7%', 'left' => '33%'],
    ['name' => 'Centrální věž', 'top' => '24%', 'left' => '46.5%'],
    ['name' => 'Řezba nad píšťalami', 'top' => '6.6%', 'left' => '45.8%'],
];
$organ = Organ::find(Organ::ORGAN_ID_PRAHA_KRIZOVNICI);
@endphp

<div {{ $attributes->merge(['class' => 'text-center']) }}>
    <div class="d-inline-block p-3 rounded border border-tertiary">
        <h4>{{ __('Části varhanní skříně') }}</h4>

        <div class="position-relative mx-auto" style="max-width: 330px">
            <img src="/images/praha-krizovnici-crop.jpg" title="Licence obrázku: Tomas Jezek, Člověk a Víra (2017)" class="rounded border w-100" />
            <em class="small">
                <x-organomania.organ-link :iconLink="false" :organ="$organ" :showOrganBuilder="true" :showSizeInfo="true" :showIcon="false" :newTab="true" />
            </em>

            @foreach ($parts as $i => $part)
                <span
                    class="position-absolute badge rounded-pill text-bg-light"
                    style="font-size: 60%; top: {{ $part['top'] }}; left: {{ $part['left'] }};"
                    data-bs-toggle="tooltip"
                    data-bs-title="{{ __($part['name']) }}"
                >
                    {{ $i + 1 }}
                </span>
            @endforeach
            <div class="mt-2 text-start small" style="column-count: 2">
                @foreach ($parts as $i => $part)
                    <div>
                        <span class="badge rounded-pill text-bg-secondary">
                            {{ $i + 1 }}
                        </span>
                        {{ __($part['name']) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>