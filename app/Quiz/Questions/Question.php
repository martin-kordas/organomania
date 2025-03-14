<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Quiz\Answers\Answer;
use App\Traits\OwnedEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Question
{
    
    public ?Answer $selectedAnswer;
    
    public protected(set) Collection $answers;
    
    const MIN_DIFFICULTY_LEVEL = QuizDifficultyLevel::Easy;
    
    abstract protected static string $entityClass;
    
    abstract public protected(set) string $template;
    
    // je-li FALSE, jde o otevřenou otázku (odpovědí je ID hledané entity)
    abstract protected bool $hasAnswers = true;
    
    public function __construct(
        public protected(set) Model $questionedEntity,
        protected QuizDifficultyLevel $difficultyLevel,
    )
    { }
    
    abstract public function generateAnswers(): Collection;
    
    public function isAnswerCorrect(Answer $answer)
    {
        return $answer->entity->id === $this->questionedEntity->id;
    }
    
    public static function getRandomEntity(): Model
    {
        $model = new static::$entityClass;
        if ($model instanceof OwnedEntity) $model->public();
        return $model->inRandomOrder()->first();
    }
}
