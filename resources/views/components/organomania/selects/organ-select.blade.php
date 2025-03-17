@props(['organs', 'model' => 'organId', 'id' => null, 'select2' => true, 'allowClear' => false, 'disabled' => false, 'showOrganBuilder' => true, 'small' => true])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="organ-select form-select @if ($small) form-select-sm @endif @if ($select2) select2 @endif @error($model) is-invalid @enderror"
    aria-label="{{ __('Výběr varhan') }}"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte varhany') }}&hellip;"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option></option>
    @foreach ($organs as $organ)
        <option value="{{ $organ->id }}">
            {{ $organ->municipality }}, {{ $organ->place }}
            @if ($showOrganBuilder && $organ->organBuilder)
                ({{ $organ->organBuilder->name }}@if ($organ->year_built), {{$organ->year_built }}@endif)
            @endif
        </option>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
