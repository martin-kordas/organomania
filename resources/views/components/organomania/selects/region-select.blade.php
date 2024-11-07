@props(['regions', 'model' => 'regionId', 'id' => null, 'allowClear' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="form-select select2"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte kraj') }}..."
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach ($regions as $region)
        <option value="{{ $region->id }}">{{ $region->name }}</option>
    @endforeach
</select>
