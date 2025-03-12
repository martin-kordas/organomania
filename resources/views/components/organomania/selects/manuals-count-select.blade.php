@props(['model' => 'manualsCount', 'id' => null, 'excludeDispositionId' => null, 'select2' => true, 'allowClear' => false, 'small' => false])

@use(App\Helpers)

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="manuals-count-select form-select @if ($select2) select2 @endif @if ($small) form-select-sm @endif"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte počet manuálů') }}"
    @if ($allowClear) data-allow-clear="true" @endif
    multiple
>
    @foreach (range(1, 5) as $manualsCount)
        <option wire:key="{{ $id }}_{{ $manualsCount }}" value="{{ $manualsCount }}">
            {{ Helpers::formatRomanNumeral($manualsCount) }}
        </option>
    @endforeach
</select>
