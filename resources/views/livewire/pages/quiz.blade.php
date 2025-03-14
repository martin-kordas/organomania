<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use App\Enums\QuizDifficultyLevel;
use App\Quiz\Quiz;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    #[Session]
    public string $name = '';
    public int $difficultyLevel = QuizDifficultyLevel::Easy->value;
    public int $step = 0;

    public int $answerIndex;
    public int $answerId;

    private Quiz $quiz;

    const SESSION_KEY_QUIZ = 'quiz.quiz';

    public function boot()
    {
        if (!$this->isStart()) $this->quiz = session(static::SESSION_KEY_QUIZ);
    }

    public function mount()
    {
        $this->name = Auth::user()?->name;
    }

    #[Computed]
    public function title()
    {
        return __('Varhanní kvíz');
    }

    #[Computed]
    public function isAnswered()
    {
        
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
    }

    public function isStart()
    {
        return $this->step <= 0;
    }

    public function isFinish($step = null)
    {
        $step ??= $this->step;
        return $step > Quiz::QUESTION_COUNT;
    }

    public function start()
    {
        if (!$this->isStart()) throw new RuntimeException;

        $difficultyLevel = QuizDifficultyLevel::from($this->difficultyLevel);
        $questionFactory = new QuestionFactory($difficultyLevel);
        $this->quiz = new Quiz($questionFactory);
        $this->step++;
    }

    public function answer()
    {
        $this->validate([
            'answerIndex' => 'required_without:answerId',
            'answerId' => 'required_without:answerIndex',
        ]);

        
    }

    public function next()
    {
        // TODO: kontrola, je-li answered
        if (!$this->isFinish($this->step + 1)) {
            $this->quiz->addQuestion();
        }
        $this->step++;
    }

}; ?>

<div class="quiz container">
  
    @push('meta')
        <meta name="description" content="{{ 'xx' /* TODO */ }}">
    @endpush
    
    <h3>{{ $this->title }}</h3>
    
    <form>
        @if ($this->isStart())
            <label for="difficultyLevel" class="form-label">{{ __('Obtížnost') }}</label>
            <x-organomania.selects.quiz-difficulty-level-select />
            @error('difficultyLevel')
                <div id="difficultyLevelFeedback" class="invalid-feedback">{{ $message }}</div>
            @enderror

            <label for="name" class="form-label">{{ __('Jméno uživatele') }}</label>
            <input id="name" class="form-control @error('name') is-invalid @enderror" autocomplete="on" wire:model="name" placeholder="{{ __('anonymní') }}" />
            @error('name')
                <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
                {{ __('Jméno, pod kterým se výsledek kvízu objeví v žebříčku.') }}
                {{ __('Chcete-li výsledek zveřejnit anonymně, ponechejte nevyplněno.') }}
            </div>
                
            <button class="btn btn-primary" type="button" wire:click="start">
                <i class="bi bi-play"></i> {{ __('Spustit kvíz') }}
            </button>
            <a href="{{ route('quiz.charts') }}" class="btn btn-outline-secondary" type="button">
                <i class="bi bi-bar-chart"></i> {{ __('Žebříček') }}
            </a>
        @elseif ($this->isFinish())
                
        @else
            @if ($this->isAnswered)
                <button class="btn btn-primary" type="button" wire:click="next">
                    <i class="bi bi-arrow-right"></i> {{ __('Další otázka') }}
                </button>
            @else
                <button class="btn btn-primary" type="button" wire:click="answer">
                    {{ __('Uložit odpověď') }}
                </button>
            @endif
        @endif
    </form>
        
</div>
