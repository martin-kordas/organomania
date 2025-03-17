<?php

namespace App\Quiz\Questions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ManualsCountFromLocalityOrganBuilderQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'manuals-count-from-locality-organ-builder';
    
    public protected(set) string $selectTemplate = 'organomania.selects.plain-select';
    
    protected function getCorrectAnswer(): mixed
    {
        return $this->createAnswer($this->questionedEntity->manuals_count);
    }
    
    protected function scope(Builder $query)
    {
        $query
            ->whereDoesntHave('organRebuilds')
            ->whereNotNull('manuals_count')
            ->whereHas('organBuilder');
    }
    
    protected function generateAnswers(): Collection
    {
        $correctManualsCount = $this->correctAnswer->answerContent;
        
        return static::getEntities()
            ->diff([$correctManualsCount])
            ->shuffle()
            ->take($this->answersCount - 1)
            ->push($correctManualsCount)
            ->sort()
            ->map($this->createAnswer(...));
    }
    
    public static function getEntities(): Collection
    {
        return collect()->range(1, 5)->keyBy(
            fn ($count) => $count
        );
    }
    
}
