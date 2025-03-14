<?php

namespace App\Quiz;

use App\Enums\QuizDifficultyLevel;
use Illuminate\Support\Collection;

class Quiz
{
    
    private Collection $questions;
    
    const QUESTION_COUNT = 12;
    
    public function __construct(
        private QuestionFactory $questionFactory,
    ) {
        $this->questions[] = collect();
    }
    
    public function addQuestion()
    {
        $question = $this->questionFactory->createQuestion();
        $this->questions[] = $question;
    }
    
}
