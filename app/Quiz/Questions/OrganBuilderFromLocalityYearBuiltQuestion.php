<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromLocalityYearBuiltQuestion extends OrganQuestion
{
    use HasOrganBuilderAnswers;
    
    public protected(set) string $template = 'organ-builder-from-locality-year-built';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    protected bool $applyScopeForAnswers = false;
    
    protected function getCorrectAnswer(): Answer
    {
        return $this->createAnswer($this->questionedEntity->organBuilder);
    }
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('organBuilder')
            ->whereNotNull('year_built');
    }
    
}
