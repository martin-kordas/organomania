<?php

namespace App\Quiz\Questions;

use App\Enums\OrganCategory;
use App\Enums\QuizDifficultyLevel;
use App\Quiz\Traits\HasOrganCategoryAnswers;
use Illuminate\Database\Eloquent\Builder;

class WindchestFromLocalityOrganBuilderQuestion extends OrganQuestion
{
    use HasOrganCategoryAnswers;
    
    public protected(set) string $template = 'windchest-from-locality-organ-builder';
    
    public protected(set) string $selectTemplate = 'organomania.selects.category-select';
    
    protected function scope(Builder $query)
    {
        $categoryIds = static::getEntities()->pluck('value');
        
        $query
            ->whereDoesntHave('organRebuilds')
            // varhany musí mít právě 1 kategorii traktury
            ->whereHas('organCategories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            }, '=', 1);
            
        if ($this->showOrganBuilder()) $query->whereHas('organBuilder');
    }
    
    protected static function isRelevantCategory(OrganCategory $category): bool
    {
        return $category->isWindchestCategory();
    }
    
    public function showOrganBuilder()
    {
        return $this->difficultyLevel->value <= QuizDifficultyLevel::Medium->value;
    }
    
}
