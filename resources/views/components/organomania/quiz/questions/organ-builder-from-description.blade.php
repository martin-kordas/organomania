@props(['question'])

@php
    $organBuilder = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {!! __('Určete <span class="text-decoration-underline">varhanáře</span> na základě uvedeného popisu') !!}:
    <div class="markdown mt-3 border rounded p-2">{!! $question->getDescription() !!}</div>
</x-organomania.quiz.questions.question>
