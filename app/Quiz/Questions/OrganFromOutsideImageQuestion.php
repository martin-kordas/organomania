<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;

class OrganFromOutsideImageQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'organ-from-outside-image';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 12;
    
    protected function scope(Builder $query)
    {
        // je-li fotka prospektu nedostupná, fotku exteriéru obsahuje image_url
        $query->whereNotNull('image_url');
    }
    
    public function showOrganBuilders()
    {
        return $this->difficultyLevel->value <= QuizDifficultyLevel::Easy->value;
    }
    
    protected function createAnswer(mixed $answerContent): Answer
    {
        return $this->answerFactory->createAnswer(
            $answerContent, $this->answerType,
            $this->showOrganBuilders()
        );
    }
    
}
