@props(['question'])

@php
    $organ = $question->questionedEntity;
@endphp

<x-organomania.quiz.questions.question>
    @if ($question->showOrganBuilder())
        {{ __('V lokalitě') }}
        <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>
        {{ __('se nachází varhany, které') }}
        {{ __($organ->organBuilder->is_workshop ? 'postavila varhanářská dílna' : 'postavil varhanář') }}
        <strong>{{ $organ->organBuilder->standardName }}</strong>.
        
        <br class="d-none d-sm-inline" />
        
        {!! __('Jakou mají varhany <span class="text-decoration-underline">vzdušnici</span>?') !!}
    @else
        {!! __('Jakou <span class="text-decoration-underline">vzdušnici</span> mají varhany v lokalitě') !!}
        <strong>{{ $organ->municipality }}, {{ $organ->place }}</strong>?
    @endif
</x-organomania.quiz.questions.question>
