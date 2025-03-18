@props(['question'])

@php
    $organ = $question->questionedEntity;

    if (isset($organ->outside_image_url)) {
        $imageUrl = $organ->outside_image_url;
        $imageCredits = $organ->outside_image_credits;
    }
    else {
        $imageUrl = $organ->image_url;
        $imageCredits = $organ->image_credits;
    }
@endphp

<x-organomania.quiz.questions.question>
    <div class="mb-3">{!! __('Podle obrázku určete <span class="text-decoration-underline">lokalitu kostela</span> se zajímavými varhanami') !!}:</div>
    
    <img
        wire:replace
        class="rounded border"
        src="{{ $imageUrl }}"
        @isset($imageCredits) title="{{ __('Licence obrázku') }}: {{ $imageCredits }}" @endisset
        style="width: auto; max-width: min(100%, 400px)"
    />
</x-organomania.quiz.questions.question>
