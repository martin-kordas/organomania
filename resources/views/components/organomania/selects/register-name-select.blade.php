@props(['registerNames', 'language', 'customRegisterName' => null,  'model' => 'registerNameId', 'id' => null, 'select2' => true, 'allowClear' => false])

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="register-name-select form-select form-select-sm flex-grow-1 @if ($select2) select2-register-names @endif @error($model) is-invalid @enderror"
    aria-label="{{ __('Výběr rejstříků') }}"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Název rejstříku') }}&hellip;"
    @if ($allowClear) data-allow-clear="true" @endif
    aria-describedby="{{ "{$id}Feedback" }}"
    data-tags="true"
    onchange="registerNameSelectOnchange(event)"
    required="required"
>
    <option></option>
    @foreach ($registerNames as $registerName)
        <option
            value="{{ $registerName->id }}"
            data-default-pitch-id="{{ $registerName->register->pitch_id }}"
            data-default-pitch-label="{{ $registerName->register->pitch->getLabel($language) }}"
            data-pitches-list="{{ $registerName->register->getPitchesLabels($language)->implode(', ') }}"
            data-category-name="{{ $registerName->register->registerCategory->getName() }}"
            data-categories-list="{{ $registerName->register->getCategoriesNames()->implode(', ') }}"
            data-description="{{ $registerName->register->description }}"
            data-language="{{ $registerName->language->value }}"
            data-non-custom-register="true"
        >
            {{ $registerName->name }}
        </option>
    @endforeach
    @isset($customRegisterName)
        <option
            value="{{ $customRegisterName }}"
        >
            {{ $customRegisterName }}
        </option>
    @endisset
</select>

@error($model)
    <div id="{{ "{$id}Feedback" }}" class="invalid-feedback">{{ $message }}</div>
@enderror

@script
<script>
    window.registerNameSelectOnchange = function (e) {
        var pitchId = $(e.target).find('option:selected').data('defaultPitchId')
        if (pitchId) {
            var pitchSelect = $('select.pitch-select')
            pitchSelect.val(pitchId).change()
        }
    }
</script>
@endscript
