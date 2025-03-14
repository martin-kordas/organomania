<?php

namespace App\Quiz\Questions;

use App\Models\OrganBuilder;

abstract class OrganBuilderQuestion extends Question
{
    
    abstract protected static string $entityClass = OrganBuilder::class;
    
    public protected(set) OrganBuilder $questionedEntity;
    
}
