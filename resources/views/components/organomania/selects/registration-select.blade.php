@props(['registrations', 'model' => 'registrationId', 'id' => null, 'select2' => true, 'allowClear' => false])

@php
    use App\Helpers;
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="pitch-select form-select form-select-sm @if ($select2) select2 @endif"
    wire:model.live="{{ $model }}"
    data-placeholder="{{ __('Zvolte registraci') }}"
    @if ($allowClear) data-allow-clear="true" @endif
>
    <option></option>
    @foreach ($registrations as $registration)
        <option value="{{ $registration->id }}">
            {{ $registration->name }}
            ({{ Helpers::formatDate($registration->created_at) }})
        </option>
    @endforeach
</select>
