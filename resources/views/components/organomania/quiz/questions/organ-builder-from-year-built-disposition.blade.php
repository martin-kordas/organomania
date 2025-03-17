@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {!! __('Určete <span class="text-decoration-underline">lokalitu varhan</span>, který byly postaveny roku') !!}
    <strong>{{ $organ->year_built }}</strong>
    {{ __('a mají tuto dispozici') }}:
    
    <div @class(['markdown', 'accordion-disposition', 'mt-3', 'border', 'rounded', 'p-2', 'm-auto' => $organ->getDispositionColumnsCount() > 1]) style="column-count: {{ $organ->getDispositionColumnsCount() }}">{!! $question->getDisposition() !!}</div>
</x-organomania.quiz.questions.question>
