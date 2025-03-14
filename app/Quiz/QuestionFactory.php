<?php

namespace App\Quiz;

use App\Enums\QuizDifficultyLevel;
use App\Quiz\Questions\Question;
use Exception;
use Illuminate\Support\Collection;
use LogicException;

class QuestionFactory
{
    
    private Collection $created;
    
    private Collection $questionTypes;
    
    const QUESTION_TYPES = [
        OrganBuilderFromLocalityManualsCountQuestion::class, OrganBuilderLocalityFromYearRenovated::class,
    ];
    
    public function __construct(
        private QuizDifficultyLevel $difficultyLevel
    ) {
        $this->created = collect();
        $this->questionTypes = $this->getQuestionTypes();
    }
    
    public function createQuestion(): Question
    {
        $question = null;
        
        // TODO: efektivnější by bylo při zvolení určitého typu otázky vybrat z db. entitu, která ještě nebyla použita v jiných otázkách
        //  - pak by nebylo nutné náhodně zkoušet generovat další otázky
        for ($attempt = 1; $attempt < 20; $attempt++) {
            $question1 = $this->generateQuestion();
            if (!$this->questionExists($question1)) {
                $question = $question1;
                break;
            }
        }
        if (!isset($question)) throw new Exception;
        
        $this->created[] = $question;
    }
    
    private function getQuestionTypes(): Collection
    {
        $types = collect(static::QUESTION_TYPES)->filter(
            fn ($questionType) => ($questionType::MIN_DIFFICULTY_LEVEL)->value <= $this->difficultyLevel->value
        );
        if ($types->isEmpty()) throw new LogicException;
        return $types;
    }
    
    private function generateQuestion(): Question
    {
        $type = $this->questionTypes->random();
        $questionedEntity = $type::getRandomEntity();
        return new $type($questionedEntity, $this->difficultyLevel);
    }
    
    private function questionExists(Question $question)
    {
        return $this->created->exists(
            fn (Question $question1)
                => $question1::class === $question::class
                    && $question1->questionedEntity === $question->questionedEntity
        );
    }
    
}
