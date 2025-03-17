@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('V lokalitě') }}
    <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>
    {{ __('se nachází varhany, které mají') }}
    <strong>{{ $organ->getDeclinedManualsCount() }}</strong>.
    
    <br class="d-none d-sm-inline mb-2" />
    
    {!! __('Který <span class="text-decoration-underline">varhanář</span> tento nástroj postavil?') !!}
</x-organomania.quiz.questions.question>
