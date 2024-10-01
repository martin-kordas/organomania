@props(['dispositions', 'model' => 'dispositionId', 'id' => null, 'excludeDispositionId' => null, 'select2' => true, 'allowClear' => false])

@php
    use App\Helpers;
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="disposition-select form-select form-select-sm @if ($select2) select2 @endif"
    wire:model.change="{{ $model }}"
    data-placeholder="{{ __('Zvolte dispozici') }}"
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach ($dispositions as $disposition)
        @if (!isset($excludeDispositionId) || $disposition->id !== $excludeDispositionId)
            <option wire:key="{{ $id }}_{{ $disposition->id }}" value="{{ $disposition->id }}">
                {{ $disposition->name }}
            </option>
        @endif
    @endforeach
</select>
