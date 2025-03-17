@props(['question'])

@php
    $organBuilder = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {!! __('Určete <span class="text-decoration-underline">varhanáře</span>, který postavil varhany v těchto lokalitách') !!}:
    <ul class="items-list fw-bold mt-2">
        @foreach ($question->getOrgans() as $organ)
            <li>{{ $organ->municipality }}, {{ $organ->place }}</li>
        @endforeach
    </ul>
</x-organomania.quiz.questions.question>
