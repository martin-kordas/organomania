@props(['question'])

@php
    $festival = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('V lokalitě') }}
    <strong>{{ $festival->organ->municipality }}, {{ $festival->organ->place }}</strong>
    {{ __('se nachází varhany, které hostí festival') }}
    <strong>{{ $festival->name }}</strong>.
    
    <br class="d-none d-sm-inline mb-2" />
    
    {!! __('Který <span class="text-decoration-underline">varhanář</span> tyto varhany postavil?') !!}
</x-organomania.quiz.questions.question>
