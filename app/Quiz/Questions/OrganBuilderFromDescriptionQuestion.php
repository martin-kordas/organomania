<?php

namespace App\Quiz\Questions;

use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromDescriptionQuestion extends OrganBuilderQuestion
{
    public protected(set) string $template = 'organ-builder-from-description';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 10;
    
    protected function scope(Builder $query)
    {
        $query
            ->whereRaw('LENGTH(description) > 350')
            ->whereNotNull('name_base_words');
    }
    
    public function getDescription()
    {
        return $this->getObfuscatedDescription(
            $this->questionedEntity->description,
            $this->questionedEntity->name_base_words
        );
    }
    
}
