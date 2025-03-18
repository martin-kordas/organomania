@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    <div class="mb-3">{!! __('Určete <span class="text-decoration-underline">lokalitu varhan</span> na obrázku') !!}:</div>
    
    <img
        wire:replace
        class="rounded border"
        src="{{ $organ->image_url }}"
        @isset($organ->image_credits) title="{{ __('Licence obrázku') }}: {{ $organ->image_credits }}" @endisset
        style="width: auto; max-width: min(100%, 400px)"
    />
</x-organomania.quiz.questions.question>
