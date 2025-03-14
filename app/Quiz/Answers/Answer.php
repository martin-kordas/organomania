<?php

namespace App\Quiz\Answers;

abstract class Answer
{
    
    abstract public protected(set) string $template;
    
    // TODO: entity může být i jen text! např. je-li odpovědí letopoček
    public function __construct(public protected(set) Model $entity)
    {
        
    }
    
}
