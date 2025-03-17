<?php

namespace App\Quiz\Answers;

use App\Models\Organ;

class OrganAnswer extends Answer
{
    
    public protected(set) string $template = 'organ';
    
    public protected(set) string $entityNameLocativ = 'varhanách';
    
    public function __construct(
        public protected(set) mixed $answerContent,
        public protected(set) bool $showOrganBuilder = false,
    )
    {
        parent::__construct($answerContent);
    }
    
    public function getLink(): string
    {
        return route('organs.show', $this->answerContent->slug);
    }
    
}
