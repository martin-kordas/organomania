@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('Varhany v lokalitě') }}
    <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>
    {{ $organ->organBuilder->is_workshop ? __('postavila varhanářská dílna') : __('postavil varhanář') }}
    <strong>{{ $organ->organBuilder->standardName }}</strong>.
    
    <br class="d-none d-sm-inline mb-2" />
    
    {!! __('Ve kterém <span class="text-decoration-underline">období</span> byly varhany postaveny?') !!}
</x-organomania.quiz.questions.question>
