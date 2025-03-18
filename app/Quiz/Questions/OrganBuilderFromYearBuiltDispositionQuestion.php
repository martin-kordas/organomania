<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Services\DispositionTextualFormatter;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromYearBuiltDispositionQuestion extends OrganQuestion
{
    public protected(set) string $template = 'organ-builder-from-year-built-disposition';
    
    public static QuizDifficultyLevel $minDifficultyLevel = QuizDifficultyLevel::Medium;
    
    protected bool $applyScopeForAnswers = false;
    
    protected static ?QuizDifficultyLevel $withoutAnswersFromDifficulty = null;
    
    const int FREQUENCY = 7;
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('organBuilder')
            ->whereDoesntHave('organRebuilds')
            ->whereNotNull('year_built')
            ->whereNotNull('disposition');
    }
    
    public function getDisposition()
    {
        $dispositionFormatter = app(DispositionTextualFormatter::class);
        return $dispositionFormatter->format($this->questionedEntity->disposition, credits: false);
    }
    
}
