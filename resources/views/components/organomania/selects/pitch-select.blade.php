@props(['pitchGroups', 'language', 'model' => 'pitchId', 'id' => null, 'select2' => true, 'allowClear' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="pitch-select form-select form-select-sm @if ($select2) select2 @endif @error($model) is-invalid @enderror"
    aria-label="{{ __('Výběr stopové výšky') }}"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Poloha') }}"
    @if ($allowClear) data-allow-clear="true" @endif
    aria-describedby="{{ "{$id}Feedback" }}"
>
    <option></option>
    @foreach ($pitchGroups as $group => $pitches)
        <optgroup label="{{ $group }}">
            @foreach ($pitches as $pitch)
                <option value="{{ $pitch->value }}">
                    {{ $pitch->getLabel($language) }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror
