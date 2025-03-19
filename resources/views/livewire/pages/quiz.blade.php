<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use App\Enums\QuizDifficultyLevel;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\QuizResult;
use App\Quiz\AnswerFactory;
use App\Quiz\QuestionFactory;
use App\Quiz\Questions\OrganQuestion;
use App\Quiz\Questions\Question;
use App\Quiz\Quiz;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    #[Session]
    public ?string $name = null;
    #[Session]
    public int $difficultyLevel = QuizDifficultyLevel::Easy->value;
    #[Session]
    public int $step = 0;

    public ?int $answerIndex = null;
    public ?int $answerId = null;

    private ?Quiz $quiz;

    #[Locked]
    public bool $finished = false;

    const SESSION_KEY_QUIZ = 'quiz.quiz';

    public function boot()
    {
        if (!$this->isStart()) $this->quiz = session(static::SESSION_KEY_QUIZ);
    }

    public function mount()
    {
        if ($this->isQuestion() && $this->question->isAnswered()) {
            $this->setQuestionAnswer($this->question);
        }
    }

    #[Computed]
    public function title()
    {
        return __('Varhanní kvíz');
    }

    #[Computed]
    private function question()
    {
        if (!$this->isQuestion()) throw new RuntimeException;

        return $this->quiz->getQuestion($this->step - 1);
    }

    #[Computed]
    private function score()
    {
        return $this->quiz->getScore();
    }

    #[Computed]
    private function difficultyLevelEnum()
    {
        return QuizDifficultyLevel::from($this->difficultyLevel);
    }

    #[Computed]
    private function showDetails()
    {
        // nejsou-li v odpovědích entity stejného typu jako Question::questionedEntity, zobrazíme ještě dodatečný odkaz na podrobnosti o ::questionedEntity
        return
            $this->question->isAnswered()
            && !$this->question->hasAnswerSameEntityType();
    }

    #[Computed]
    private function quizResults()
    {
        return QuizResult::query()
            ->where('user_id', Auth::user()->id)
            ->where('difficulty_level', $this->difficultyLevel)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    #[Computed]
    private function bestQuizResult()
    {
        return QuizResult::query()
            ->where('user_id', Auth::user()->id)
            ->where('difficulty_level', $this->difficultyLevel)
            ->orderBy('score', 'desc')
            ->take(1)
            ->first();
    }

    #[Computed]
    private function averageScore()
    {
        return QuizResult::query()
            ->where('user_id', Auth::user()->id)
            ->where('difficulty_level', $this->difficultyLevel)
            ->avg('score');
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
        $this->dispatch('select2-sync-needed', componentName: 'pages.quiz');
    }

    private function isStart()
    {
        return $this->step <= 0;
    }

    private function isFinish($step = null)
    {
        $step ??= $this->step;
        return $step > Quiz::QUESTION_COUNT;
    }

    private function isQuestion()
    {
        return !$this->isStart() && !$this->isFinish();
    }

    public function start()
    {
        $answerFactory = new AnswerFactory();
        $questionFactory = new QuestionFactory($this->difficultyLevelEnum, $answerFactory);
        $this->quiz = new Quiz($questionFactory);
        $this->addQuestion();

        $this->step = 1;
    }

    public function new()
    {
        $this->deleteQuestionAnswers();
        $this->finished = false;
        $this->step = 0;
    }

    public function answer()
    {
        if (!$this->isQuestion()) throw new RuntimeException;

        $message = 'Zvolte prosím odpověď.';
        $this->validate([
            'answerIndex' => 'required_without:answerId',
            'answerId' => 'required_without:answerIndex',
        ], [
            'answerIndex' => $message,
            'answerId' => $message,
        ]);

        $question = $this->quiz->getQuestion($this->step - 1);
        if ($question->hasAnswers) $question->selectAnswer($this->answerIndex);
        else $question->selectedAnswerId = $this->answerId;
        $this->saveQuiz();
    }

    public function next()
    {
        if (!$this->question->isAnswered()) throw new RuntimeException;

        $nextStep = $this->step + 1;
        if ($this->isFinish($nextStep)) {
            if (!$this->finished) {
                $this->saveResults();
                $this->finished = true;
            }
        }
        else {
            if ($this->quiz->hasQuestion($nextStep - 1)) {
                $nextQuestion = $this->quiz->getQuestion($nextStep - 1);
                if ($nextQuestion->isAnswered()) $this->setQuestionAnswer($nextQuestion);
                else $this->deleteQuestionAnswers();
            }
            else {
                $this->addQuestion();
                $this->deleteQuestionAnswers();
            }
        }
        $this->step++;
        unset($this->question);
    }

    public function back()
    {
        if ($this->isStart()) throw new RuntimeException;

        $this->step--;
        unset($this->question);
        $this->setQuestionAnswer($this->question);
        $this->resetValidation();
        $this->question->selectedAnswer;
    }

    private function addQuestion()
    {
        $this->quiz->addQuestion();
        $this->saveQuiz();
    }

    private function saveQuiz()
    {
        session([static::SESSION_KEY_QUIZ => $this->quiz]);
    }

    private function saveResults()
    {
        new QuizResult([
            'name' => $this->name,
            'difficulty_level' => $this->difficultyLevelEnum,
            'score' => $this->score,
            'user_id' => Auth::user()?->id,
        ])->save();
    }

    private function deleteQuestionAnswers()
    {
        $this->answerIndex = $this->answerId = null;
    }

    private function setQuestionAnswer(Question $question)
    {
        if ($question->hasAnswers) $this->answerIndex = $question->getSelectedAnswerIndex();
        else $this->answerId = $question->selectedAnswerId;
    }

    private function getEntitiesIds(Collection $entities)
    {
        return $entities->pluck('id')->toArray();
    }

}; ?>

<div class="quiz container">
  
    @push('meta')
        <meta name="description" content="{{ __('Prověřte své znalosti o významných varhanách a varhanářích pomocí všestranného kvízu s 3 úrovněmi obtížnosti. Porovnejte své skóre s ostatními.') }}">
    @endpush
    
    <h3 class="mb-3">{{ $this->title }}</h3>
    
    <form class="mb-4 position-relative" @if ($this->isStart()) wire:submit="start" @endif>
        <div wire:loading.block wire:loading.class="opacity-75" class="position-absolute text-center bg-white w-100 h-100" style="z-index: 10;">
            <x-organomania.spinner class="align-items-center h-100" :margin="false" />
        </div>
        @if ($this->isStart())
            @if (!Auth::check())
                <x-organomania.info-alert class="d-inline-block">
                    {{ __('Pro lepší přehled o výsledcích Vašich kvízů se přihlaste.') }}
                    <div class="mt-1">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('login') }}" type="button">
                            {{ __('Přihlásit se') }}
                        </a>
                    </div>
                </x-organomania.info-alert>
            @endif
        
            <div class="row gy-3">
                <div>
                    <label for="difficultyLevel" class="form-label">{{ __('Obtížnost') }}</label>
                    <x-organomania.selects.quiz-difficulty-level-select />
                    @error('difficultyLevel')
                        <div id="difficultyLevelFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        {{ __('Čím vyšší obtížnost, tím méně známé varhany a tím větší počet nabízených odpovědí.') }}
                    </div>
                </div>
                
                <div>
                    <label for="name" class="form-label">{{ __('Jméno uživatele v žebříčku') }}</label>
                    <input id="name" class="form-control @error('name') is-invalid @enderror" autocomplete="on" wire:model="name" placeholder="{{ __('anonymní') }}" />
                    @error('name')
                        <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        @if ($userName = Auth::user()?->name)
                            {{ __('např.') }}
                            <a class="text-decoration-none" onclick="return setName({{ Js::from($userName) }})" href="#">{{ $userName }}</a>
                            &nbsp;|&nbsp;
                        @endif
                        {{ __('Chcete-li výsledek kvízu zveřejnit anonymně, nechejte jméno nevyplněno.') }}
                    </div>
                </div>
              
                <div class="mt-4">
                    <button class="btn btn-primary" type="button" wire:click="start">
                        <i class="bi bi-play"></i> {{ __('Spustit kvíz') }}
                    </button>
                    <a class="btn btn-outline-secondary float-end" href="{{ route('quiz.results') }}" type="button" wire:navigate>
                        <i class="bi bi-bar-chart"></i> {{ __('Žebříčky') }}
                    </a>
                </div>
            </div>
                
        @elseif ($this->isFinish())
            <div>
                <div class="fs-5 mb-3">
                    {{ __('Výsledné skóre') }}: <span class="badge text-bg-info">{{ $this->score }}</span>
                </div>
              
                <div class="mb-4">
                    <h5>{{ __('Shrnutí obsahu kvízu') }}</h5>
                    @php
                        $organs = $this->quiz->getOrgans();
                        $organBuilders = $this->quiz->getOrganBuilders();
                        $festivals = $this->quiz->getFestivals();
                    @endphp
                    @if ($organs->isNotEmpty())
                        <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('organs.index', ['filterId' => $this->getEntitiesIds($organs)]) }}" target="_blank">
                            <i class="bi bi-music-note-list"></i> {{ __('Varhany') }}
                            <span class="badge text-bg-secondary rounded-pill">{{ $organs->count() }}</span>
                        </a>
                    @endif
                    @if ($organBuilders->isNotEmpty())
                        <a class="btn btn-sm btn-outline-secondary me-1" href="{{ route('organ-builders.index', ['filterId' => $this->getEntitiesIds($organBuilders)]) }}" target="_blank">
                            <i class="bi bi-person-circle"></i> {{ __('Varhanáři') }}
                            <span class="badge text-bg-secondary rounded-pill">{{ $organBuilders->count() }}</span>
                        </a>
                    @endif
                    @if ($festivals->isNotEmpty())
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('festivals.index', ['filterId' => $this->getEntitiesIds($festivals)]) }}" target="_blank">
                            <i class="bi bi-calendar-date"></i> {{ __('Festivaly') }}
                            <span class="badge text-bg-secondary rounded-pill">{{ $festivals->count() }}</span>
                        </a>
                    @endif
                </div>

                <div class="mb-4 gap-2 d-flex">
                    <button class="btn btn-primary" type="button" wire:click="new">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Nový kvíz') }}
                    </button>
                    <button class="btn btn-outline-secondary" type="button" wire:click="back">
                        <i class="bi bi-arrow-left"></i> {{ __('Předchozí') }} <span class="d-none d-sm-inline">{{ __('otázka') }}</span>
                    </button>
                </div>

                @if (Auth::check() && $this->quizResults->isNotEmpty())
                    <h4>{{ __('Historie výsledků') }} &ndash; {{ str($this->difficultyLevelEnum->getName())->lower() }} {{ __('obtížnost') }}</h4>
                    <div class="mb-2 lh-lg">
                        @if ($this->bestQuizResult)
                            <div>
                                {{ __('Nejlepší skóre') }}: <span class="badge text-bg-info">{{ $this->bestQuizResult->score }}</span> ({{ Helpers::formatDate($this->bestQuizResult->created_at, true) }})
                            </div>
                        @endif
                        @isset ($this->averageScore)
                            <div>
                                {{ __('Průměrné skóre') }}: <span class="badge text-bg-info">{{ Helpers::formatNumber($this->averageScore, decimals: 1) }}</span>
                            </div>
                        @endisset
                    </div>
                    <x-organomania.quiz.results-table :quizResults="$this->quizResults" :showName="false" showTime highlightFirst sortByName />
                @endif
                <div class="mt-3">
                    <a href="{{ route('quiz.results', ['difficultyLevel' => $this->difficultyLevel]) }}" class="btn btn-outline-secondary" type="button" wire:navigate>
                        <i class="bi bi-bar-chart"></i> {{ __('Žebříčky') }}
                    </a>
                </div>
            </div>
                
        @else
            <div>
                <div class="d-flex gap-2 align-items-end mb-2">
                    <h4 class="mb-0 me-auto">
                        {{ __('Otázka') }} {{ $this->step }}/{{ Quiz::QUESTION_COUNT }}
                    </h4>
                    <div class="fs-5 d-flex gap-1 align-items-end lh-sm">
                        <span>{{ __('Skóre') }}:</span> <span class="badge text-bg-info">{{ $this->score }}</span>
                    </div>
                </div>

                <div class="my-3">
                    <x-dynamic-component :component="$this->question->template" :question="$this->question" />
                </div>

                <div class="mb-3">
                    {{-- a) výběr z předgenerovaných odpovědí --}}
                    @if ($this->question->hasAnswers)
                        <div class="lh-lg">
                            @foreach ($this->question->answers as $index => $answer)
                                <div class="form-check" wire:key="answer{{ $index }}">
                                    <input
                                        class="form-check-input @error('answerIndex') is-invalid @enderror"
                                        type="radio"
                                        id="answer{{ $index }}"
                                        wire:model="answerIndex"
                                        value="{{ $index }}"
                                        aria-describedby="answerIndexFeedback"
                                        @disabled($this->question->isAnswered())
                                    >
                                    <label class="form-check-label" for="answer{{ $index }}">
                                        <x-dynamic-component :component="$answer->template" :answer="$answer" :answeredQuestion="$this->question->isAnswered()" />
                                    </label>
                                    @if ($loop->last)
                                        @error('answerIndex')
                                            <div id="answerIndexFeedback" class="invalid-feedback">{{ $errors->first('answerIndex') }}</div>
                                        @enderror
                                    @endif
                                    @if ($this->question->isAnswered())
                                        @php
                                            $successAnswer = $this->question->isAnswerCorrect($answer);
                                            $errorAnswer = $this->answerIndex === $index;
                                            $answerWithInfo = $successAnswer || $errorAnswer;
                                        @endphp
                                        @if ($answerWithInfo)
                                            <span class="d-inline-block ms-2" style="width: 30px">
                                                @if ($successAnswer)
                                                    <i class="bi bi-check-lg px-1 text-bg-success rounded-pill"></i>
                                                    @php $answerWithInfo = true @endphp
                                                @elseif ($errorAnswer)
                                                    <i class="bi bi-x-lg px-1 text-bg-danger rounded-pill"></i>
                                                    @php $answerWithInfo = true @endphp
                                                @endif
                                            </span>
                                            
                                            @if ($link = $answer->getLink())
                                                <a class="btn btn-sm btn-outline-primary" href="{{ $link }}" target="_blank">
                                                    <i class="bi bi-eye"></i> {{ __('Více o') }} {{ __($answer->entityNameLocativ) }}
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    {{-- b) výběr z roletky všech entit --}}
                    @else
                        @php
                            $entities = $this->question::getEntities();
                            $correctAnswer = $this->question->correctAnswer;
                            $showOrganBuilder = $this->question->isAnswered() || $this->question instanceof OrganQuestion && $this->question->showOrganBuilders();
                        @endphp
                        {{-- atributy organs, organBuilders aj. nejde zvýjimkovat (použití @if působí chyby), proto uvádíme vždy všechny atributy --}}
                        <div wire:key="{{ $this->question->selectTemplate }}">
                            <x-dynamic-component
                                :component="$this->question->selectTemplate"
                                :question="$this->question"
                                :disabled="$this->question->isAnswered()"
                                model="answerId"
                                :small="false"
                                :organs="$entities"
                                :organBuilders="$entities"
                                :categories="$entities"
                                :items="$entities"
                                :showOrganBuilder="$showOrganBuilder"
                                :showActivePeriod="false"
                            />
                        </div>
                        <div class="mt-2">
                            @if ($this->question->isAnswered())
                                @if ($this->question->isAnsweredCorrectly())
                                    <i class="bi bi-check-lg px-1 text-bg-success rounded-pill"></i>
                                    {{ __('Správná odpověď!') }}
                                @else
                                    <i class="bi bi-x-lg px-1 text-bg-danger rounded-pill"></i>
                                    {{ __('Správná odpověď je') }}:
                                    <div class="d-inline-block fw-bold">
                                        <x-dynamic-component :component="$correctAnswer->template" :answer="$correctAnswer" :answeredQuestion="$this->question->isAnswered()" />
                                    </div>
                                @endif

                                @if ($link = $correctAnswer->getLink())
                                    <div class="mt-2">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $link }}" target="_blank">
                                            <i class="bi bi-eye"></i> {{ __('Více o') }} {{ __($correctAnswer->entityNameLocativ) }}
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
                
                @if ($this->showDetails)
                    <div class="mb-3">
                        <a class="btn btn-sm btn-outline-primary" href="{{ $this->question->getQuestionedEntityLink() }}" target="_blank">
                            <i class="bi bi-eye"></i> {{ __('Více o') }} {{ __($this->question->entityNameLocativ) }}
                        </a>
                    </div>
                @endif

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    @if ($this->question->isAnswered())
                        <button class="btn btn-primary" type="button" wire:click="next">
                            <i class="bi bi-arrow-right"></i>
                            @if ($this->isFinish($step + 1))
                                {{ __('Dokončit') }} <span class="d-none d-sm-inline">{{ __('kvíz') }}</span>
                            @else
                                {{ __('Další') }} <span class="d-none d-sm-inline">{{ __('otázka') }}</span>
                            @endif
                        </button>
                    @else
                        <button class="btn btn-primary" type="button" wire:click="answer">
                            <i class="bi bi-floppy"></i> {{ __('Uložit odpověď') }}
                        </button>
                    @endif
                    @if ($this->step > 1)
                        <button class="btn btn-outline-secondary" type="button" wire:click="back">
                            <i class="bi bi-arrow-left"></i> {{ __('Předchozí') }} <span class="d-none d-sm-inline">{{ __('otázka') }}</span>
                        </button>
                    @endif
                    <button class="btn btn-secondary ms-auto" type="button" data-bs-toggle="modal" data-bs-target="#newQuizModal">
                        <i class="bi bi-x-lg"></i> {{ __('Ukončit') }} <span class="d-none d-sm-inline">{{ __('kvíz') }}</span>
                    </button>
                </div>
            </div>
        @endif
    </form>
        
    <x-organomania.modals.confirm-modal
        id="newQuizModal"
        onclick="$wire.new()"
        title="{{ __('Ukončit kvíz') }}"
        :icon="false"
    >
        {{ __('Opravdu chcete kvíz ukončit?') }}
    </x-organomania.modals.confirm-modal>
</div>

@script
<script>
    window.setName = function (name) {
        $wire.name = name
        $('#name').focus()
        return false
    }
</script>
@endscript