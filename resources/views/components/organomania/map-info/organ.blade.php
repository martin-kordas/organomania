@props(['organ', 'distance' => null])

<div>
    <strong style="font-size: 115%">{{ $organ->municipality }}</strong> &nbsp;|&nbsp; {{ $organ->place }}
</div>

<div>
    {{ $organ->organ_builder_name ?? $organ->organBuilder->name ?? __('neznámý varhanář') }}
    @isset($organ->year_built)
        <span style='color: grey'>({{ $organ->year_built }})</span>
    @endisset
    <span style="float: right; margin-left: 1em; color: grey">{{ $organ->getSizeInfo() }}</span>
</div>

@isset($distance)
<hr>
<div>
    {{ __('Vzdálenost') }}: {{ round($distance / 1000, 1) }}&nbsp;km
</div>
@endisset
