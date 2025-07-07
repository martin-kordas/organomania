@props(['model' => 'month', 'id' => null, 'allowClear' => false, 'small' => false])

@use(App\Helpers)

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="form-select select2 @if ($small) form-select-sm @endif"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte měsíc') }}..."
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach (Helpers::getMonths() as $monthNumber => $monthName)
        <option value="{{ $monthNumber }}">{{ $monthName }}</option>
    @endforeach
</select>
