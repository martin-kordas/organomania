<?php

namespace App\Quiz\Questions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrganBuilderFromLocalitiesQuestion extends OrganBuilderQuestion
{
    public protected(set) string $template = 'organ-builder-from-localities';
    
    protected bool $applyScopeForAnswers = false;
    
    protected Collection $organs;
    
    protected function scope(Builder $query)
    {
        $query->whereHas('organs', null, '>=', '3');
    }
    
    public function getOrgans()
    {
        return $this->organs ??= $this->questionedEntity
            ->organs()
            ->inRandomOrder()
            ->take(3)
            ->get();
    }
    
}
