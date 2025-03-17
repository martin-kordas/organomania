@props(['answer'])

<x-organomania.quiz.answers.answer>
    {{ $answer->answerContent->getName() }}
</x-organomania.quiz.answers.answer>
