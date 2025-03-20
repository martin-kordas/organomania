<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromFestivalOrganLocalityQuestion extends FestivalQuestion
{
    use HasOrganBuilderAnswers;
    
    public protected(set) string $template = 'organ-builder-from-festival-organ-locality';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    const int FREQUENCY = 4;
    
    protected function getCorrectAnswer(): Answer
    {
        return $this->createAnswer($this->questionedEntity->organ->organBuilder);
    }
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('organ.organBuilder')
            ->whereDoesntHave('organ.organBuilder.organRebuilds');
    }
    
}
