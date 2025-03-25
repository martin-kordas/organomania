<?php

namespace App\Quiz;

use App\Enums\QuizDifficultyLevel;
use App\Quiz\Questions\{
    OrganBuilderFromLocalityManualsCountQuestion, OrganBuilderFromLocalityYearRenovatedQuestion,
    OrganFromCaseImageQuestion, OrganBuilderFromDescriptionQuestion, ManualsCountFromLocalityOrganBuilderQuestion,
    PeriodCategoryFromLocationOrganBuilderQuestion, OrganBuilderFromFestivalOrganLocalityQuestion,
    OrganBuilderFromYearBuiltDispositionQuestion, ActionFromLocalityOrganBuilderQuestion, WindchestFromLocalityOrganBuilderQuestion,
    OrganBuilderFromLocalityYearBuiltQuestion, OrganBuilderFromLocalityRebuildYearBuilt, OrganBuilderFromLocalitiesQuestion,
    MunicipalityFromOrganBuilderQuestion, OrganBuilderFromActivePeriodMunicipalityQuestion, OrganFromOutsideImageQuestion,
    OrganFromDescriptionQuestion
};
use App\Quiz\Questions\Question;
use Exception;
use Illuminate\Support\Collection;
use LogicException;

// TODO: neměla by factory řešit i získání dat z db.? (nyní data získává Question, i data pro Answer)
//  - ALE: současná koncepce umožňuje, aby si kažá Question podle svého typu načetla adekvátní Answers
class QuestionFactory
{
    
    private Collection $created;
    
    private Collection $questionTypes;
    
    private int $answersCount;
    
    const QUESTION_TYPES = [
        OrganBuilderFromLocalityManualsCountQuestion::class, OrganBuilderFromLocalityYearRenovatedQuestion::class,
        OrganFromCaseImageQuestion::class, OrganBuilderFromDescriptionQuestion::class, ManualsCountFromLocalityOrganBuilderQuestion::class,
        PeriodCategoryFromLocationOrganBuilderQuestion::class, OrganBuilderFromFestivalOrganLocalityQuestion::class,
        OrganBuilderFromYearBuiltDispositionQuestion::class, ActionFromLocalityOrganBuilderQuestion::class, WindchestFromLocalityOrganBuilderQuestion::class,
        OrganBuilderFromLocalityYearBuiltQuestion::class, OrganBuilderFromLocalityRebuildYearBuilt::class, OrganBuilderFromLocalitiesQuestion::class,
        MunicipalityFromOrganBuilderQuestion::class, OrganBuilderFromActivePeriodMunicipalityQuestion::class, OrganFromOutsideImageQuestion::class,
        OrganFromDescriptionQuestion::class,
        //OrganBuilderFromLocalitiesQuestion::class
    ];
    
    public function __construct(
        private QuizDifficultyLevel $difficultyLevel,
        private AnswerFactory $answerFactory,
    ) {
        $this->created = collect();
        $this->questionTypes = $this->getQuestionTypes();
        
        $this->answersCount = match ($this->difficultyLevel) {
            QuizDifficultyLevel::Easy => 2,
            QuizDifficultyLevel::Medium => 3,
            default => 4,
        };
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
        if (!isset($question)) throw new Exception('Nepodařilo se vygenerovat další otázku.');
        
        $this->created[] = $question;
        return $question;
    }
    
    private function getQuestionTypes(): Collection
    {
        $types = collect(static::QUESTION_TYPES)->filter(
            fn ($questionType) => $this->difficultyLevel->value >= ($questionType::$minDifficultyLevel)->value
        );
        if ($types->isEmpty()) throw new LogicException('Nebyl nalezen žádný dostupný typ otázky.');
        return $types;
    }
    
    private function generateQuestion(): Question
    {
        $type = $this->questionTypes->flatMap(
            // před náhodným výběrem typu každý typ zduplikujeme tolikrát, jakou má frekvenci
            fn (string $type) => array_fill(0, $type::FREQUENCY, $type)
        )->random();
        
        return new $type($this->difficultyLevel, $this->answerFactory, $this->answersCount);
    }
    
    private function questionExists(Question $question)
    {
        return $this->created->contains(
            fn (Question $question1)
                => $question1::class === $question::class
                    && $question1->questionedEntity->id === $question->questionedEntity->id
        );
    }
    
}
