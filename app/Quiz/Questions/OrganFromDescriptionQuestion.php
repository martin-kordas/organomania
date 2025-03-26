<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;

class OrganFromDescriptionQuestion extends OrganQuestion
{
    public protected(set) string $template = 'organ-from-description';
    
    protected bool $applyScopeForAnswers = false;
    
    public static QuizDifficultyLevel $minDifficultyLevel = QuizDifficultyLevel::Medium;
    
    const int FREQUENCY = 10;
    
    // vyřadíme varhany, které nelze poznat z popisu
    //  - TODO: tyto varhany se vyřazují i z odpovědí, což není nezbytné
    protected const array EXCLUDED_ENTITY_IDS = [
        ...parent::EXCLUDED_ENTITY_IDS,
        42, 65, 72, 79, 86, 107, 118,
        // 3 hvězdičky
        19, 21, 23, 25, 142, 40, 47, 48, 144, 49, 52,
        54, 58, 63, 67, 68, 90, 160, 99, 102, 112, 116, 141,
    ];
    
    protected function scope(Builder $query)
    {
        $query
            ->whereNotNull('description')
            ->where(function (Builder $query) {
                $query
                    ->where('importance', '>=', 5)
                    ->orWhereIn('id', [15, 22, 24, 28, 35, 36, 159, 66, 70, 96, 126, 162, 104, 166, 163, 59, 30, 113, 115]);
            });
    }
    
    public function getDescription()
    {
        return $this->getObfuscatedDescription(
            $this->questionedEntity->description,
            $this->questionedEntity->location_base_words
        );
    }
    
}
