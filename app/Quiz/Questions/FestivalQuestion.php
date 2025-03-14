<?php

namespace App\Quiz\Questions;

use App\Models\Festival;

abstract class FestivalQuestion extends Question
{

    abstract protected static string $entityClass = Festival::class;
    
    public protected(set) Festival $questionedEntity;
    
}
