@props(['answer', 'answeredQuestion' => false])

@php
    $organ = $answer->answerContent;

    // zobrazení varhanáře je buď zapnuto otázku jako takovou (např. kvůli nižší obtížnosti),
    // anebo se varhanář zobrazuje dodatečně po zodpovězení otázky
    $showOrganBuilder = $organ->organBuilder && ($answeredQuestion || $answer->showOrganBuilder);
@endphp

<x-organomania.quiz.answers.answer>
    {{ $organ->municipality }}, {{ $organ->place }}
    @if ($showOrganBuilder)
        <span class="text-body-secondary">
            ({{ $organ->organBuilder->name }}@if ($organ->year_built), {{$organ->year_built }}@endif)
        </span>
    @endif
</x-organomania.quiz.answers.answer>
