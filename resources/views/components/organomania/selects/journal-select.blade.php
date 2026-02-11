@props([
    'journals', 'model' => 'filterJournal',
    'id' => null, 'select2' => true, 'allowClear' => true, 'small' => false,
    'disabled' => false, 'multiple' => false, 'live' => false
])

@php
    $id ??= $model;
    $modelAttribute = $live ? "model.live" : "model";
@endphp

<select
    id="{{ $id }}"
    class="form-select @if ($select2) select2 @endif @error($model) is-invalid @enderror @if ($small) form-select-sm @endif"
    aria-label="{{ __('Filtr časopisů') }}"
    wire:{{ $modelAttribute }}="{{ $model }}"
    data-placeholder="{{ __('Zvolte periodikum') }}&hellip;"
    @if ($allowClear) data-allow-clear="true" @endif
    @disabled($disabled)
    aria-describedby="{{ "{$id}Feedback" }}"
    @if ($multiple) multiple @endif
>
    @if (!$multiple)
        <option></option>
    @endif

    @foreach ($journals as $journal)
        <option wire:key="{{ $journal->journal }}" value="{{ $journal->journal }}">
            {{ $journal->journal }}
            @if ($journal->publications_count > 0)
                ({{ $journal->publications_count }}&times;)
            @endif
        </option>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
