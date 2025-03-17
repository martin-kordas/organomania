@props(['answer'])

@php
    $organ = $answer->answerContent;
@endphp

<x-organomania.quiz.answers.answer>
    {{ $organ->municipality }}, {{ $organ->place }}
    @if ($answer->showOrganBuilder && $organ->organBuilder)
        ({{ $organ->organBuilder->name }}@if ($organ->year_built), {{$organ->year_built }}@endif)
    @endif
</x-organomania.quiz.answers.answer>
