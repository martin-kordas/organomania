@props(['organs', 'thumbnailOrgan', 'thumbnailComponent'])

<p class="text-secondary text-center mb-3">
    <small>
        {{ __('Klikněte na sloupec v grafu pro zobrazení podrobností.') }}
    </small>
</p>

<div class="container entity-page-chart">
    <div
        id="chart"
        wire:ignore
    ></div>
    
    <x-organomania.modals.organ-thumbnail-modal />

</div>

@script
<script>
    let chartData = {{ Js::from($this->chartData) }}
    let texts = {{ Js::from([
        'manualsCount' => __('Počet manuálů'),
        'originalManualsCount' => __('Počet manuálů (původní)'),
        'stopsCount' => __('Počet rejstříků'),
        'originalStopsCount' => __('Počet rejstříků (původní)'),
    ]) }}
    
    setTimeout(() => {
        initChart($wire, chartData, texts)
    })
</script>
@endscript
