<?php

namespace App\Quiz\Questions;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class OrganQuestion extends Question
{
    
    protected static string $entityClass = Organ::class;
    
    public protected(set) string $entityNameLocativ = 'těchto varhanách';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-select';
    
    protected const array EXCLUDED_ENTITY_IDS = [
        // nezahrnovat varhany, kterou jsou ve veřejených spíše omylem/dočasně
        4496, 123, 124,
        // neexistující varhany
        Organ::ORGAN_ID_PRAHA_EMAUZY,
    ];
    
    public static function getEntities(): Collection
    {
        return Organ::public()
            ->when(!empty(static::EXCLUDED_ENTITY_IDS), function (Builder $query) {
                $query->whereNotIn('id', static::EXCLUDED_ENTITY_IDS);
            })
            ->orderBy('municipality')
            ->orderBy('place')
            ->get();
    }
    
    public function getQuestionedEntityLink(): string
    {
        return route('organs.show', $this->questionedEntity->slug);
    }
    
}
