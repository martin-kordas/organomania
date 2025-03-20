<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Models\Scopes\OwnedEntityScope;
use App\Quiz\AnswerFactory;
use App\Quiz\Answers\Answer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LogicException;
use RuntimeException;

abstract class Question
{
    
    public protected(set) Collection $answers;
    
    public Answer $correctAnswer;
    
    public ?Answer $selectedAnswer = null;
    
    // je-li $hasAnswers FALSE, odpovědí je ID hledané entity
    //  - správněji by se mělo nazývat "selectedAnswerContent"
    //  - (odpovědí teoreticky nemusí být jen ID entity, ale i jakýkoli jiná hodnota, např. počet manuálů)
    public ?int $selectedAnswerId = null;
    
    public protected(set) Model $questionedEntity;
    
    protected static string $entityClass;
    
    public protected(set) string $entityNameLocativ;
    
    // je-li FALSE, jde o otevřenou otázku (odpovědí je ID hledané entity)
    public protected(set) bool $hasAnswers = true;
    
    protected int $minImportance;
    
    // přepíše se, mají-li odpovědi nějaký zcela specifický typ
    protected ?string $answerType = null;
    
    public protected(set) string $template {
        get => "organomania.quiz.questions.{$this->template}";
    }
    
    // get hook nepoužit, protože z neznámého důvodu Livewire nefunguje
    //  - při pokusu o přečtení vlastnosti prohlížeč hlásí přerušení spojení
    public protected(set) string $selectTemplate;
    
    // podtřídy mohou definovat odlišné frekvence výskytu (např. pro otázky méně obvyklé nebo otázky s menším počtem možných instancí)
    const int FREQUENCY = 5;
    
    protected bool $applyScopeForAnswers = true;
    
    abstract public function getQuestionedEntityLink(): string;
    
    abstract public static function getEntitiesQuery(): Builder;
    
    public static QuizDifficultyLevel $minDifficultyLevel = QuizDifficultyLevel::Easy;
    
    protected static ?QuizDifficultyLevel $withoutAnswersFromDifficulty = QuizDifficultyLevel::Advanced;
    
    protected const array EXCLUDED_ENTITY_IDS = [];
    
    public function __construct(
        protected QuizDifficultyLevel $difficultyLevel,
        protected AnswerFactory $answerFactory,
        protected int $answersCount,
    )
    {
        $this->minImportance = $this->getMinImportance();
        
        $this->questionedEntity = static::getRandomEntity(
            minImportance: $this->minImportance,
            scope: $this->scope(...),
            excludedEntityIds: static::EXCLUDED_ENTITY_IDS,
        ) ?? throw new RuntimeException('Pro otázku nebyla nalezena žádná entita.');
        
        $this->correctAnswer = $this->getCorrectAnswer();
        
        if (static::$withoutAnswersFromDifficulty && $this->difficultyLevel->value >= static::$withoutAnswersFromDifficulty->value) {
            $this->hasAnswers = false;
        }
        
        if ($this->hasAnswers) $this->answers = $this->generateAnswers();
    }
    
    // DŮLEŽITÉ! je-li odpovědí něco jiného než ::questionedEntity, přepíše se v podtřídě
    protected function getCorrectAnswer(): mixed
    {
        return $this->createAnswer($this->questionedEntity);
    }
    
    // DŮLEŽITÉ: v případě přizpůsobeného generování odpovědí se funkce přepíše
    protected function generateAnswers(): Collection
    {
        return $this->generateEntityAnswers();
    }
    
    // ve výchozím stavu se scope aplikuje jak pro ::questionedEntity, tak pro ::answers
    // DŮLEŽITÉ: v podtřídách lze definovat vlastní podmínky
    protected function scope(Builder $query)
    {
        return;
    }
    
    public function isAnswerCorrect(Answer $answer): bool
    {
        return $this->correctAnswer->isEqual($answer);
    }
    
    public function isAnswerIdCorrect(int $id): bool
    {
        return $id == $this->correctAnswer->comparisonValue;
    }
    
    public function getSelectedAnswer()
    {
        return $this->hasAnswers ? $this->selectedAnswer : $this->selectedAnswerId;
    }
    
    public function getSelectedAnswerIndex()
    {
        if (!$this->hasAnswers || !$this->isAnswered()) throw new LogicException;
        
        return $this->answers->search($this->selectedAnswer, strict: true);
    }
    
    public function isAnswered()
    {
        return $this->getSelectedAnswer() !== null;
    }
    
    public function isAnsweredCorrectly()
    {
        if (!$this->isAnswered()) return false;
        
        if ($this->hasAnswers) return $this->isAnswerCorrect($this->selectedAnswer);
        else return $this->isAnswerIdCorrect($this->selectedAnswerId);
    }
    
    public function hasAnswerSameEntityType()
    {
        return $this->correctAnswer->answerContent instanceof (($this->questionedEntity)::class);
    }
    
    public function selectAnswer(int $answerIndex)
    {
        $answer = $this->answers[$answerIndex] ?? throw new RuntimeException;
        $this->selectedAnswer = $answer;
    }
    
    protected function getMinImportance()
    {
        return match ($this->difficultyLevel) {
            QuizDifficultyLevel::Easy => 7,
            QuizDifficultyLevel::Medium => 4,
            default => 1,
        };
    }
    
    protected function createAnswer(mixed $answerContent): Answer
    {
        return $this->answerFactory->createAnswer($answerContent, $this->answerType);
    }
    
    protected function generateEntityAnswers(
        ?string $entityClass = null,
        ?Model $correctAnswerEntity = null,
        callable|false|null $scope = null,
        array $excludedEntityIds = null,
    ): Collection
    {
        $entityClass ??= static::$entityClass;
        
        // answers mohou mít jiný typ entity než qustionedEntity
        //  - např. questionedEntity jsou varhany, answers jejich varhanář
        //  - v takovém případě nemá smysl aplikovat výchozí scope a další parametry
        if ($entityClass === static::$entityClass) {
            $correctAnswerEntity ??= $this->questionedEntity;
            $excludedEntityIds ??= static::EXCLUDED_ENTITY_IDS;
            if ($this->applyScopeForAnswers) {
                $scope ??= $this->scope(...);
            }
        }
        if (!isset($correctAnswerEntity)) {
            throw new LogicException('Argument $correctAnswerEntity musí být zadán.');
        }
        
        $limit = $this->answersCount - 1;
        $answers = static::getRandomEntities(
            limit: $limit,
            entityClass: $entityClass,
            scope: function (Builder $query) use ($correctAnswerEntity, $scope) {
                $query->whereNot('id', $correctAnswerEntity->id);
                if ($scope) $scope($query);
            },
            minImportance: $this->minImportance,
            excludedEntityIds: $excludedEntityIds,
        );
        if ($answers->count() < $limit) throw new RuntimeException('Nepodařilo se získat entity pro požadovaný počet odpovědí.');
            
        $answers[] = $correctAnswerEntity;
        
        return $answers->shuffle()->map(
            $this->createAnswer(...)
        );
    }
    
    protected static function getRandomEntity(
        ?string $entityClass = null,
        ?int $minImportance = null,
        ?callable $scope = null,
        array $excludedEntityIds = null,
    ): ?Model
    {
        return static::getRandomEntities(
            1, $entityClass, $minImportance, $scope, $excludedEntityIds
        )->first();
    }
    
    protected static function getRandomEntities(
        int $limit = 1,
        ?string $entityClass = null,
        ?int $minImportance = null,
        ?callable $scope = null,
        array $excludedEntityIds = null,
    ): Collection
    {
        $entityClass ??= static::$entityClass;
        $model = new $entityClass;
        
        $query = $model->query();
        if (method_exists($model, 'scopePublic')) $query->public();
        if (isset($minImportance)) $query->where('importance', '>=', $minImportance);
        if (!empty($excludedEntityIds)) $query->whereNotIn('id', $excludedEntityIds);
        if (isset($scope)) $scope($query);
        
        //$query->whereIn('id', [1, 2, 3, 4]);
        
        return $query
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
    
    public static function getEntities(): Collection
    {
        return static::getEntitiesQuery()->get();
    }
    
}
