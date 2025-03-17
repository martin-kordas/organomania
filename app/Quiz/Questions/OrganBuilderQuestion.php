<?php

namespace App\Quiz\Questions;

use App\Models\OrganBuilder;
use Illuminate\Support\Collection;

abstract class OrganBuilderQuestion extends Question
{
    
    protected static string $entityClass = OrganBuilder::class;
    
    public protected(set) string $entityNameLocativ = 'tomto varhanáři';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-builder-select';
    
    public static function getEntities(): Collection
    {
        return OrganBuilder::public()
            ->orderByName()
            ->get();
    }
    
    public function getQuestionedEntityLink(): string
    {
        return route('organ-builders.show', $this->questionedEntity->slug);
    }
    
}
