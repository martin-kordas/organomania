@props(['model' => 'difficultyLevel', 'id' => null, 'select2' => false, 'allowClear' => false, 'small' => false])

@use(App\Enums\QuizDifficultyLevel)

@php
    $id ??= $model;
@endphp

<select
    id="{{ $id }}"
    class="quiz-difficulty-select form-select @if ($select2) select2 @endif @if ($small) form-select-sm @endif"
    wire:model="{{ $model }}"
    data-placeholder="{{ __('Zvolte obtížnost') }}"
    @if ($allowClear) data-allow-clear="true" @endif
>
    @foreach (QuizDifficultyLevel::cases() as $difficulty)
        <option value="{{ $difficulty->value }}">
            {{ $difficulty->getName() }}
        </option>
    @endforeach
</select>
