{{-- TODO: výchozí placeholder by měl být "Zvolte položku", ale v quiz.blade.php nejde v <x-dynamic-component> placeholder přizpůsobit a podmínkovat --}}
@props(['model', 'items', 'id' => null, 'select2' => false, 'allowClear' => false, 'placeholder' => __('Zvolte odpověď'), 'disabled' => false, 'small' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="plain-select form-select @if ($select2) select2 @endif @error($model) is-invalid @enderror @if ($small) form-select-sm @endif"
    wire:model="{{ $model }}"
    data-placeholder="{{ $placeholder }}"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option>{{ !$select2 ? $placeholder : "" }}</option>
    @foreach ($items as $value => $text)
        <option value="{{ $value }}">
            {{ $text }}
        </option>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
