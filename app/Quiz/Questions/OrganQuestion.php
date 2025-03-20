<?php

namespace App\Quiz\Questions;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Builder;

abstract class OrganQuestion extends Question
{
    
    protected static string $entityClass = Organ::class;
    
    public protected(set) string $entityNameLocativ = 'těchto varhanách';
    
    public protected(set) string $selectTemplate = 'organomania.selects.organ-select';
    
    protected const array EXCLUDED_ENTITY_IDS = [
        // nezahrnovat varhany, kterou jsou ve veřejených spíše omylem/dočasně
        4496, 123, 124, 12,
        // neexistující varhany
        Organ::ORGAN_ID_PRAHA_EMAUZY,
        // problematické - 2 varhanáři
        Organ::ORGAN_ID_KOLIN_KOSTEL_SV_BARTOLOMEJE,
        // dosud nepostavené varhany
        Organ::ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_ZAPADNI_KRUCHTA,
    ];
    
    public function showOrganBuilders()
    {
        return $this->isAnswered();
    }
    
    public static function getEntitiesQuery(): Builder
    {
        return Organ::public()
            ->when(!empty(static::EXCLUDED_ENTITY_IDS), function (Builder $query) {
                $query->whereNotIn('id', static::EXCLUDED_ENTITY_IDS);
            })
            ->orderBy('municipality')
            ->orderBy('place');
    }
    
    public function getQuestionedEntityLink(): string
    {
        return route('organs.show', $this->questionedEntity->slug);
    }
    
}
