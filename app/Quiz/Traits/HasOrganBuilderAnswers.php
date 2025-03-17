<?php

namespace App\Quiz\Traits;

use App\Models\OrganBuilder;
use App\Quiz\Questions\OrganBuilderQuestion;
use Illuminate\Support\Collection;

trait HasOrganBuilderAnswers
{
    
    public static function getEntities(): Collection
    {
        // otázka se ptá na varhany, ale odpovědí jsou varhanáři
        return OrganBuilderQuestion::getEntities();
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
