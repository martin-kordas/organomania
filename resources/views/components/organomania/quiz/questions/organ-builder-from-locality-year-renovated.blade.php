@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('V lokalitě') }}
    <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>
    {{ __('proběhla v roce') }}
    <strong>{{ $organ->year_renovated }}</strong>
    {{ __('oprava/restaurování varhan') }}.
    
    <br class="d-none d-sm-inline mb-2" />
    
    {!! __('Který <span class="text-decoration-underline">varhanář</span> tuto opravu provedl?') !!}
</x-organomania.quiz.questions.question>
