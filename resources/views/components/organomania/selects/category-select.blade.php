@props(['model', 'categories', 'items', 'id' => null, 'select2' => false, 'allowClear' => false, 'placeholder' => __('Zvolte kategorii'), 'disabled' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="category-select form-select @if ($select2) select2 @endif @error($model) is-invalid @enderror"
    wire:model="{{ $model }}"
    data-placeholder="{{ $placeholder }}"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option>{{ $placeholder }}</option>
    @foreach ($categories as $category)
        <option value="{{ $category->value }}">
            {{ $category->getName() }}
        </option>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
