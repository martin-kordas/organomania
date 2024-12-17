@props(['competition'])

<div style="font-size: 115%">
    <strong>{{ $competition->name }}</strong>
</div>

<div>
    {{ $competition->locality }}
    @isset($competition->place)
        | {{ $competition->place }}
    @endisset
</div>
