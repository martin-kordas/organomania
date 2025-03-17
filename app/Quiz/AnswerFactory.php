<?php

namespace App\Quiz;

use App\Interfaces\Category;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Quiz\Answers\Answer;
use App\Quiz\Answers\CategoryAnswer;
use App\Quiz\Answers\OrganAnswer;
use App\Quiz\Answers\OrganBuilderAnswer;

class AnswerFactory
{
    
    public function createAnswer(mixed $answerContent, ?string $type = null, ...$args): Answer
    {
        if (!isset($type)) {
            if ($answerContent instanceof Organ) $type = OrganAnswer::class;
            elseif ($answerContent instanceof OrganBuilder) $type = OrganBuilderAnswer::class;
            elseif ($answerContent instanceof Category) $type = CategoryAnswer::class;
            else $type = Answer::class;
        }
        
        return new $type($answerContent, ...$args);
    }
    
}
