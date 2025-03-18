<?php

namespace App\Quiz\Questions;

use Illuminate\Database\Eloquent\Builder;

class OrganFromOutsideImageQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'organ-from-outside-image';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 12;
    
    protected function scope(Builder $query)
    {
        // je-li fotka prospektu nedostupná, fotku exteriéru obsahuje image_url
        $query->whereNotNull('image_url');
    }
    
}
