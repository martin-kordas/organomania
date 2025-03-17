@props(['question'])

@php
    $organBuilder = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('Poznejte') }}
    {!! __($organBuilder->is_workshop ? '<span class="text-decoration-underline">varhanářskou dílnu</span>, které působila v období' : '<span class="text-decoration-underline">varhanáře</span>, který žil v letech') !!}
    <strong>{{ $organBuilder->active_period }}</strong>
    {{ __($organBuilder->is_workshop ? 'v lokalitě' : 'a působil v lokalitě') }}
    <strong>{{ $organBuilder->municipality }}</strong>.
</x-organomania.quiz.questions.question>
