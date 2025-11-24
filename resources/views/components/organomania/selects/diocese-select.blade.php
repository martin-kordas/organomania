@props(['dioceses', 'model' => 'dioceseId', 'id' => null, 'allowClear' => false, 'small' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="form-select select2 @if ($small) form-select-sm @endif"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte diecézi') }}..."
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach ($dioceses as $diocese)
        <option value="{{ $diocese->id }}">{{ $diocese->name }}</option>
    @endforeach
</select>
