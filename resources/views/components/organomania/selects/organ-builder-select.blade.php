@props(['organBuilders', 'model' => 'organBuilderId', 'id' => null, 'select2' => true, 'allowClear' => false, 'small' => false, 'disabled' => false, 'showActivePeriod' => true])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="form-select @if ($select2) select2 @endif @error($model) is-invalid @enderror @if ($small) form-select-sm @endif"
    aria-label="{{ __('Filtr varhanářů') }}"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte varhanáře') }}&hellip;"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option></option>
    @foreach ($organBuilders as $organBuilder)
        <option wire:key="{{ $organBuilder->id }}" value="{{ $organBuilder->id }}">
            {{ $organBuilder->name }}
            @if ($showActivePeriod && $organBuilder->active_period)
                ({{ $organBuilder->active_period }})
            @endif
        </option>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
