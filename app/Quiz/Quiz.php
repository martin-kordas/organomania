<?php

namespace App\Quiz;

use App\Models\Festival;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Quiz\Questions\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use RuntimeException;

class Quiz
{
    
    private Collection $questions;
    
    const QUESTION_COUNT = 12;
    
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
    
    public function getEntities(string $entityClass)
    {
        $questionEntities = $this->questions->pluck('questionedEntity')->filter(
            fn (Model $entity) => $entity instanceof $entityClass
        );
        $answerEntities = $this->questions->pluck('correctAnswer.answerContent')->filter(
            fn (mixed $answerContent) => $answerContent instanceof $entityClass
        );
        
        return collect([...$questionEntities, ...$answerEntities])->unique();
    }
    
    public function getOrgans()
    {
        return $this->getEntities(Organ::class);
    }
    
    public function getOrganBuilders()
    {
        return $this->getEntities(OrganBuilder::class)->filter(
            // zahraniční varhanáři by se nezobrazili v hromadných výpisech
            fn (OrganBuilder $organBuilder) => isset($organBuilder->region_id)
        );
    }
    
    public function getFestivals()
    {
        return $this->getEntities(Festival::class);
    }
    
}
