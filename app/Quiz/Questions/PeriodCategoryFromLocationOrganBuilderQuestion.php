<?php

namespace App\Quiz\Questions;

use App\Enums\OrganCategory;
use App\Quiz\Traits\HasOrganCategoryAnswers;
use Illuminate\Database\Eloquent\Builder;

class PeriodCategoryFromLocationOrganBuilderQuestion extends OrganQuestion
{
    use HasOrganCategoryAnswers;
    
    public protected(set) string $template = 'period-category-from-location-organ-builder';
    
    public protected(set) string $selectTemplate = 'organomania.selects.category-select';
    
    protected function scope(Builder $query)
    {
        $categoryIds = $this->getEntities()->pluck('value');
        
        $query
            ->whereDoesntHave('organRebuilds')
            // varhany musí mít právě 1 kategorii období
            ->whereHas('organCategories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            }, '=', 1)
            ->whereHas('organBuilder');
    }
    
    protected static function isRelevantCategory(OrganCategory $category): bool
    {
        return
            $category->isPeriodCategory()
            && !in_array($category, [OrganCategory::FromBookMostImportantOrgans, OrganCategory::FromBookBaroqueOrganBuilding]);
    }
    
}
