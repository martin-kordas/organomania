<?php

namespace App\Quiz;

use App\Quiz\Questions\Question;
use Illuminate\Support\Collection;
use RuntimeException;

class Quiz
{
    
    private Collection $questions;
    
    const QUESTION_COUNT = 3;
    
    public function __construct(
        private QuestionFactory $questionFactory,
    ) {
        $this->questions = collect();
    }
    
    public function addQuestion()
    {
        $question = $this->questionFactory->createQuestion();
        $this->questions[] = $question;
    }
    
    public function hasQuestion(int $i): bool
    {
        return isset($this->questions[$i]);
    }
    
    public function getQuestion(int $i): Question
    {
        return $this->questions[$i] ?? throw new RuntimeException;
    }
    
    public function getScore()
    {
        return $this->questions->reduce(function (int $carry, Question $question) {
            $score = $carry;
            if ($question->isAnsweredCorrectly()) $score++;
            return $score;
        }, 0);
    }
    
}
