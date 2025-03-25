<?php

namespace App\Quiz\Questions;

use App\Quiz\Answers\Answer;
use App\Enums\QuizDifficultyLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OrganFromOutsideImageQuestion extends OrganQuestion
{
    
    public protected(set) string $template = 'organ-from-outside-image';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 11;
    
    protected function scope(Builder $query)
    {
        // je-li fotka prospektu nedostupná, fotku exteriéru obsahuje image_url
        $query->whereNotNull('image_url');
    }
    
    public function showOrganBuilders()
    {
        return $this->difficultyLevel->value <= QuizDifficultyLevel::Easy->value;
    }
    
    protected function generateAnswers(): Collection
    {
        return parent::generateEntityAnswers(
            scope: $this->answerScope(...),
        );
    }
    
    public function getEntities(): Collection
    {
        // v selectu varhan zobrazit jen dotazované varhany bez dalších varhan na stejných souřadnicích
        return static::getEntitiesQuery()
            ->where(function (Builder $query) {
                $query
                    ->where('id', $this->questionedEntity->id)
                    ->orWhere($this->answerScope(...));
            })
            ->get();
    }
    
    protected function answerScope(Builder $query)
    {
        // vyřadit z odpovědí nástroje umístěné na stejném místě jako dotazovaný nástroj, aby nebylo více správných odpovědí
        $query->whereNot(function (Builder $query) {
            $query
                ->where('latitude', $this->questionedEntity->latitude)
                ->where('longitude', $this->questionedEntity->longitude);
        });
    }
    
    protected function createAnswer(mixed $answerContent): Answer
    {
        return $this->answerFactory->createAnswer(
            $answerContent, $this->answerType,
            $this->showOrganBuilders()
        );
    }
    
}
