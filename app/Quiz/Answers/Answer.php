<?php

namespace App\Quiz\Answers;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class Answer
{
    
    public protected(set) string $template = 'plain' {
        get => "organomania.quiz.answers.{$this->template}";
    }
    
    public protected(set) string $entityNameLocativ;
    
    public protected(set) mixed $comparisonValue;
    
    public function __construct(
        public protected(set) mixed $answerContent
    )
    {
        $this->comparisonValue = $this->getComparisonValue();
    }
    
    public function isEqual(self $answer)
    {
        return $this->comparisonValue === $answer->comparisonValue;
    }
    
    public function getLink(): ?string
    {
        return null;
    }
    
    protected function getComparisonValue()
    {
        if ($this->answerContent instanceof Model) {
            return $this->answerContent->id;
        }
        if ($this->answerContent instanceof BackedEnum) {
            return $this->answerContent->value;
        }
        if (is_scalar($this->answerContent)) {
            return $this->answerContent;
        }
        
        throw new LogicException('Nutné implementovat v podtřídě.');
    }
    
}
