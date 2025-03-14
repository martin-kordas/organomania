<?php

namespace App\Quiz\Questions;

use App\Models\Organ;

abstract class OrganQuestion extends Question
{
    
    abstract protected static string $entityClass = Organ::class;
    
    public protected(set) Organ $questionedEntity;
    
}
