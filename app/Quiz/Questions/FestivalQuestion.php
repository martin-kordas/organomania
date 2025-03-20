<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Models\Festival;
use Illuminate\Database\Eloquent\Builder;

abstract class FestivalQuestion extends Question
{

    protected static string $entityClass = Festival::class;
    
    public protected(set) string $entityNameLocativ = 'tomto festivalu';
    
    public protected(set) string $selectTemplate = 'organomania.selects.festival-select';   // zatím neexistuje
    
    public static function getEntitiesQuery(): Builder
    {
        return Festival::orderBy('name');
    }
    
    public function getQuestionedEntityLink(): string
    {
        return route('festivals.show', $this->questionedEntity->slug);
    }
    
    protected function getMinImportance()
    {
        return match ($this->difficultyLevel) {
            QuizDifficultyLevel::Easy => 7,
            QuizDifficultyLevel::Medium => 5,
            default => 1,
        };
    }
    
}
