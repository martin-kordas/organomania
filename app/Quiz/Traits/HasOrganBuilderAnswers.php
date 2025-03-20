<?php

namespace App\Quiz\Traits;

use App\Models\OrganBuilder;
use App\Quiz\Questions\OrganBuilderQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasOrganBuilderAnswers
{
    
    public static function getEntitiesQuery(): Builder
    {
        // otázka se ptá na varhany, ale odpovědí jsou varhanáři
        return OrganBuilderQuestion::getEntitiesQuery();
    }
    
    protected function generateAnswers(): Collection
    {
        return parent::generateEntityAnswers(
            entityClass: OrganBuilder::class,
            correctAnswerEntity: $this->correctAnswer->answerContent,
            excludedEntityIds: OrganBuilderQuestion::EXCLUDED_ENTITY_IDS,
        );
    }
    
}
