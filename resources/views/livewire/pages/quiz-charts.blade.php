<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Enums\QuizDifficultyLevel;
use App\Models\QuizResult;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    #[Computed]
    public function title()
    {
        return __('Varhanní kvíz – žebříčky');
    }

    #[Computed]
    public function difficultyLevels()
    {
        return array_reverse(QuizDifficultyLevel::cases());
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    private function getQuizResults(QuizDifficultyLevel $difficultyLevel)
    {
        // TODO: lepší by bylo načíst pro každého uživatele s jménem jen 1 výsledek (a pro anonymní negroupovat)
        return QuizResult::query()
            ->where('difficulty_level', $difficultyLevel->value)
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

}; ?>

<div class="quiz-charts container">
  
    @push('meta')
        <meta name="description" content="{{ 'xx' /* TODO */ }}">
    @endpush
    
    <h3>{{ $this->title }}</h3>
    
    <ul class="nav nav-tabs" role="tablist">
        @foreach ($this->difficultyLevels as $difficultyLevel)
            <li class="nav-item" role="presentation">
                <button
                    @class(['nav-link', 'active' => $loop->first])
                    id="difficultyLevelTab{{ $difficultyLevel->value }}"
                    data-bs-toggle="tab"
                    data-bs-target="#difficultyLevelContent{{ $difficultyLevel->value }}"
                    type="button"
                    role="tab"
                    aria-controls="difficultyLevelContent{{ $difficultyLevel->value }}"
                    aria-selected="true"
                >
                    {{ $difficultyLevel->getName() }} {{ __('obtížnost') }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content mt-2">
        @foreach ($this->difficultyLevels as $difficultyLevel)
            <div
                @class(['tab-pane', 'fade', 'show' => $loop->first, 'active' => $loop->first])
                id="difficultyLevelContent{{ $difficultyLevel->value }}"
                role="tabpanel"
                aria-labelledby="difficultyLevelContent{{ $difficultyLevel->value }}"
                tabindex="0"
            >
                @php $quizResults = $this->getQuizResults($difficultyLevel) @endphp
                @if ($quizResults->isEmpty())
                    <span class="text-body-secondary">{{ __('zatím žádné výsledky') }}</span>
                @else
                    <x-organomania.quiz.results-table :quizResults="$this->getQuizResults($difficultyLevel)" />
                @endif
            </div>
        @endforeach
    </div>
</div>
