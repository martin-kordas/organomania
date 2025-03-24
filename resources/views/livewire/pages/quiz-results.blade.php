<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Enums\QuizDifficultyLevel;
use App\Helpers;
use App\Models\QuizResult;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    public bool $userResults = false;

    #[Computed]
    public function title()
    {
        return __('Varhanní kvíz') . ' – ' . __('žebříčky');
    }

    #[Computed]
    public function difficultyLevels()
    {
        return array_reverse(QuizDifficultyLevel::cases());
    }

    #[Computed]
    public function defaultDifficultyLevel()
    {
        $difficultyLevelValue = request('difficultyLevel');
        if ($difficultyLevelValue) $difficultyLevel = QuizDifficultyLevel::tryFrom($difficultyLevelValue);
        return $difficultyLevel ?? QuizDifficultyLevel::Advanced;
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    private function getQuizResults(QuizDifficultyLevel $difficultyLevel)
    {
        $userId = Auth::id();

        // TODO: lepší by bylo načíst pro každého uživatele s jménem jen 1 výsledek (a pro anonymní negroupovat)
        return QuizResult::query()
            ->where('difficulty_level', $difficultyLevel->value)
            ->where('score', '>', 0)
            ->when($this->userResults && $userId, function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
    }

    private function getAverageScore(QuizDifficultyLevel $difficultyLevel)
    {
        $userId = Auth::id();

        return QuizResult::query()
            ->where('difficulty_level', $difficultyLevel->value)
            ->when($this->userResults && $userId, function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->avg('score');
    }

}; ?>

<div class="quiz-charts container">
  
    @push('meta')
        <meta name="description" content="{{ __('Zjistěte, kdo dosáhl nejlepšího skóre ve varhanním kvízu.') }}">
    @endpush
    
    <h3>{{ $this->title }}</h3>
    
    @if (Auth::check())
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="userResults" wire:model.live="userResults">
            <label class="form-check-label" for="userResults">{{ __('Jen moje výsledky') }}</label>
        </div>
    @endif
    
    <ul class="nav nav-tabs mt-3" role="tablist">
        @foreach ($this->difficultyLevels as $difficultyLevel)
            <li class="nav-item" role="presentation">
                <button
                    @class(['nav-link', 'active' => $difficultyLevel === $this->defaultDifficultyLevel])
                    id="difficultyLevelTab{{ $difficultyLevel->value }}"
                    data-bs-toggle="tab"
                    data-bs-target="#difficultyLevelContent{{ $difficultyLevel->value }}"
                    type="button"
                    role="tab"
                    aria-controls="difficultyLevelContent{{ $difficultyLevel->value }}"
                    aria-selected="true"
                    wire:ignore.self
                >
                    {{ $difficultyLevel->getName() }} {{ __('obtížnost') }}
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content mt-2">
        @foreach ($this->difficultyLevels as $difficultyLevel)
            <div
                @class(['tab-pane', 'fade', 'show' => $difficultyLevel === $this->defaultDifficultyLevel, 'active' => $difficultyLevel === $this->defaultDifficultyLevel])
                id="difficultyLevelContent{{ $difficultyLevel->value }}"
                role="tabpanel"
                aria-labelledby="difficultyLevelContent{{ $difficultyLevel->value }}"
                tabindex="0"
                wire:ignore.self
            >
                @php
                    $quizResults = $this->getQuizResults($difficultyLevel);
                    $averageScore = $this->getAverageScore($difficultyLevel);
                @endphp
                @if ($quizResults->isEmpty())
                    <div class="text-body-secondary text-center">{{ __('zatím žádné výsledky') }}</div>
                @else
                    <div class="mb-1">
                        {{ __('Průměrné skóre') }}: <span class="badge text-bg-info">{{ Helpers::formatNumber($averageScore, decimals: 1) }}</span>
                    </div>
                    <x-organomania.quiz.results-table :quizResults="$this->getQuizResults($difficultyLevel)" />
                @endif
            </div>
        @endforeach
    </div>
    
    <div class="mt-4">
        <a class="btn btn-secondary" href="{{ route('quiz') }}" wire:navigate>
            <i class="bi bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>
    </div>
</div>
