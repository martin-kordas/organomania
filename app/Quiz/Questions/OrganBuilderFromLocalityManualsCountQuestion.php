<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromLocalityManualsCountQuestion extends OrganQuestion
{
    use HasOrganBuilderAnswers;
    
    public protected(set) string $template = 'organ-builder-from-locality-manuals-count';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    protected function getCorrectAnswer(): Answer
    {
        return $this->createAnswer($this->questionedEntity->organBuilder);
    }
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('organBuilder')
            ->whereDoesntHave('organRebuilds')
            ->whereNotNull('manuals_count');
    }
    
}
