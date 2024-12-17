@props(['festival'])

<div style="font-size: 115%">
    <strong>{{ $festival->name }}</strong>
</div>

<div>
    {{ $festival->locality }}
    @isset($festival->place)
        | {{ $festival->place }}
    @endisset
</div>
