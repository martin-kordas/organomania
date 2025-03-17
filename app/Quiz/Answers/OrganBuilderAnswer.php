<?php

namespace App\Quiz\Answers;

use App\Models\OrganBuilder;

class OrganBuilderAnswer extends Answer
{
    
    public protected(set) string $template = 'organ-builder';
    
    public protected(set) string $entityNameLocativ = 'varhanáři';
    
    public function getLink(): string
    {
        return route('organ-builders.show', $this->answerContent->slug);
    }
    
}
