<?php

namespace App\Quiz\Questions;

use Illuminate\Database\Eloquent\Builder;

class OrganFromCaseImageQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'organ-from-case-image';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 15;
    
    protected function scope(Builder $query)
    {
        // je-li vyplněno outside_image_url, znamená to, že image_url je fotka prospektu
        $query
            ->whereNotNull('image_url')
            ->whereNotNull('outside_image_url');
    }
    
}
