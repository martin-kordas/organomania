<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use App\Quiz\Answers\Answer;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromLocalityYearRenovatedQuestion extends OrganQuestion
{
    use HasOrganBuilderAnswers;
    
    public static QuizDifficultyLevel $minDifficultyLevel = QuizDifficultyLevel::Medium;
    
    public protected(set) string $template = 'organ-builder-from-locality-year-renovated';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    const int FREQUENCY = 2;
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('renovationOrganBuilder')
            ->whereNotNull('year_renovated');
    }
    
    protected function getCorrectAnswer(): Answer
    {
        return $this->createAnswer($this->questionedEntity->renovationOrganBuilder);
    }
    
}
