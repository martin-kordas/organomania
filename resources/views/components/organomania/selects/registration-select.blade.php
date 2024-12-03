@props(['registrations', 'model' => 'registrationId', 'id' => null, 'select2' => true, 'allowClear' => false, 'excludeRegistrationIds' => []])

@php
    use App\Helpers;
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="registration-select form-select form-select-sm @if ($select2) select2 @endif"
    wire:model.live="{{ $model }}"
    data-placeholder="{{ __('Zvolte registraci') }}"
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach ($registrations as $registration)
        @if (!in_array($registration->id, $excludeRegistrationIds))
            <option value="{{ $registration->id }}">
                {{ $registration->name }}
                ({{ Helpers::formatDate($registration->created_at) }})
            </option>
        @endif
    @endforeach
</select>
