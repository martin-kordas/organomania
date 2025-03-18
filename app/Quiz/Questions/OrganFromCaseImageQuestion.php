<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;

class OrganFromCaseImageQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'organ-from-case-image';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 15;
    
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
    
    protected function scope(Builder $query)
    {
        // je-li vyplněno outside_image_url, znamená to, že image_url je fotka prospektu
        $query
            ->whereNotNull('image_url')
            ->whereNotNull('outside_image_url');
    }
    
}
