<?php

namespace App\Quiz\Questions;

use App\Enums\OrganBuilderCategory;
use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrganBuilderFromActivePeriodMunicipalityQuestion extends OrganBuilderQuestion
{
    public protected(set) string $template = 'organ-builder-from-active-period-municipality';
    
    protected bool $applyScopeForAnswers = false;
    
    protected static ?QuizDifficultyLevel $withoutAnswersFromDifficulty = null;
    
    protected function scope(Builder $query)
    {
        $query->whereDoesntHave('organBuilderCategories', function (Builder $query) {
            $query->where('id', OrganBuilderCategory::BuiltFrom1990);
        });
    }
    
    protected function generateAnswers(): Collection
    {
        // ve formulaci otázky je rozlišeno, zda jde o varhanáře nebo varhanářskou dílnu, proto i odpovědi musí být nastaveny stejně
        return parent::generateEntityAnswers(
            scope: function (Builder $query) {
                $query->where('is_workshop', $this->questionedEntity->is_workshop ? 1 : 0);
            }
        );
    }
    
}
