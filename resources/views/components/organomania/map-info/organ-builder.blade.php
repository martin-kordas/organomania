@props(['organBuilder'])

<div style="font-size: 115%">
    <strong>{{ $organBuilder->name }}</strong>
    @isset($organBuilder->active_period)
        <span style="color: grey">
            ({{ $organBuilder->active_period }})
        </span>
    @endisset
</div>

<div>
    {{ $organBuilder->municipality }}
</div>
