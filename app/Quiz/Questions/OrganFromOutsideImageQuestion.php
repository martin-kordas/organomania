<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
    
    protected function generateAnswers(): Collection
    {
        return parent::generateEntityAnswers(
            scope: $this->answerScope(...),
        );
    }
    
    protected function answerScope(Builder $query)
    {
        // v odpovědích nesmí být nástroje umístěné na stejném místě
        //  - takové nástroje mají obvykle place stejné, liší se jen závorkou (např. "(boční kůr)")
        //  - alternativně lze shodu místa určit pomocí souřadnic (ale nejprve by bylo nutné zkontrolovat, zda jsou souřadnice opravdu blízko u sebe)
        //  - TODO: stejné scope by mělo být použito pro ::getEntitiesQuery(), ale metoda by se musela nejprve převést na nestatickou
        $query->whereNot(function (Builder $query) {
            $place = str($this->questionedEntity->place)->replaceMatches('/, .+$/', '');
            $query
                ->where('municipality', $this->questionedEntity->municipality)
                ->whereLike('place', "$place%");
        });
    }
    
    protected function createAnswer(mixed $answerContent): Answer
    {
        return $this->answerFactory->createAnswer(
            $answerContent, $this->answerType,
            $this->showOrganBuilders()
        );
    }
    
}
