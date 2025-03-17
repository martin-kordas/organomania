@props(['question'])

@php
    $organBuilder = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {!! __('Jaké je/bylo <span class="text-decoration-underline">místo působení</span>') !!}
    {{ __($organBuilder->is_workshop ? 'varhanářské dílny' : 'varhanáře') }}
    <strong>{{ $organBuilder->standardName }}</strong>?
</x-organomania.quiz.questions.question>
