@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    {{ __('V lokalitě') }}
    <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>
    {{ __('se nachází varhany, které v roce') }}
    <strong>{{ $question->getOrganRebuild()->year_built }}</strong>
    {{ __('prošly přestavbou') }}.
    
    <br class="d-none d-sm-inline mb-2" />
    
    {!! __('Který <span class="text-decoration-underline">varhanář</span> přestavbu provedl?') !!}
</x-organomania.quiz.questions.question>
