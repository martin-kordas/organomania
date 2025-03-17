<?php

namespace App\Quiz\Questions;

use App\Models\OrganRebuild;
use App\Quiz\Answers\Answer;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromLocalityRebuildYearBuilt extends OrganQuestion
{
    use HasOrganBuilderAnswers;
    
    public protected(set) string $template = 'organ-builder-from-locality-rebuild-year-built';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    const int FREQUENCY = 3;
    
    protected OrganRebuild $organRebuild;
    
    public function getOrganRebuild()
    {
        return $this->organRebuilds ??= $this->questionedEntity->organRebuilds->random();
    }
    
    protected function getCorrectAnswer(): Answer
    {
        $organBuilder = $this->getOrganRebuild()->organBuilder;
        return $this->createAnswer($organBuilder);
    }
    
    protected function scope(Builder $query)
    {
        $query->whereHas('organRebuilds');
    }
    
}
