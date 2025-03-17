<?php

namespace App\Quiz\Traits;

use App\Enums\OrganCategory;
use Illuminate\Support\Collection;
use LogicException;

trait HasOrganCategoryAnswers
{
    
    protected function getCorrectAnswer(): mixed
    {
        $periodCategory = $this->questionedEntity
            ->organCategories
            ->pluck('id')
            ->map(OrganCategory::from(...))
            ->filter(static::isRelevantCategory(...))
            ->first();
        
        return $this->createAnswer($periodCategory);
    }
    
    protected function generateAnswers(): Collection
    {
        $correctCategory = $this->correctAnswer->answerContent;
        $categories = OrganCategory::cases();
        
        return static::getEntities()
            ->reject(
                fn (OrganCategory $category) => $category === $correctCategory
            )
            ->shuffle()
            ->take($this->answersCount - 1)
            ->push($correctCategory)
            // pořadí dle pořadí deklarace kategorie
            //  - dle $category->value řadit nelze, protože některé kategorie byly přidány dodatečně s vysokým value
            ->sortBy(
                fn (OrganCategory $category) => array_search($category, $categories)
            )
            ->map($this->createAnswer(...));
    }
    
    protected static function isRelevantCategory(OrganCategory $category): bool
    {
        throw new LogicException('Metoda musí být přepsána v podtřídě.');
    }
    
    public static function getEntities(): Collection
    {
        return collect(OrganCategory::cases())->filter(
            static::isRelevantCategory(...)
        );
    }
    
}
