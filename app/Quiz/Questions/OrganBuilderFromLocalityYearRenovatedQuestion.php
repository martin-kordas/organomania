<?php

namespace App\Quiz\Questions;

use App\Enums\OrganBuilderCategory;
use App\Enums\QuizDifficultyLevel;
use App\Models\OrganBuilder;
use App\Quiz\Traits\HasOrganBuilderAnswers;
use Illuminate\Support\Collection;
use App\Quiz\Answers\Answer;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromLocalityYearRenovatedQuestion extends OrganQuestion
{
    use HasOrganBuilderAnswers;
    
    public static QuizDifficultyLevel $minDifficultyLevel = QuizDifficultyLevel::Medium;
    
    public protected(set) string $template = 'organ-builder-from-locality-year-renovated';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    const int FREQUENCY = 2;
    
    protected function scope(Builder $query)
    {
        $query
            ->whereHas('renovationOrganBuilder')
            ->whereNotNull('year_renovated');
    }
    
    protected function generateAnswers(): Collection
    {
        return parent::generateEntityAnswers(
            entityClass: OrganBuilder::class,
            correctAnswerEntity: $this->correctAnswer->answerContent,
            excludedEntityIds: OrganBuilderQuestion::EXCLUDED_ENTITY_IDS,
            scope: function (Builder $query) {
                // varhanáře provádějící opravu vybíráme ze současných varhanářů
                $query->whereHas('organBuilderCategories', function (Builder $query) {
                    $query->where('id', OrganBuilderCategory::BuiltFrom1990);
                });
            }
        );
    }
    
    protected function getCorrectAnswer(): Answer
    {
        return $this->createAnswer($this->questionedEntity->renovationOrganBuilder);
    }
    
}
