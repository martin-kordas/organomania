<?php

namespace App\Quiz\Questions;

use App\Enums\QuizDifficultyLevel;
use App\Models\OrganBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LogicException;

class MunicipalityFromOrganBuilderQuestion extends OrganBuilderQuestion
{
    public protected(set) string $template = 'municipality-from-organ-builder';
    
    protected static ?QuizDifficultyLevel $withoutAnswersFromDifficulty = null;
    
    protected function getCorrectAnswer(): mixed
    {
        return $this->createAnswer($this->questionedEntity->municipality);
    }
    
    protected function scope(Builder $query)
    {
        // zahraniční varhanáře je lehké poznat, protože mají německý název a německé místo působení
        if ($this->difficultyLevel->value > QuizDifficultyLevel::Easy->value) {
            $query->whereNotNull('region_id');
        }
    }
    
    protected function generateAnswers(): Collection
    {
        $correctMunicipality = $this->correctAnswer->answerContent;
        
        return OrganBuilder::query()
            ->where('municipality', '!=', $correctMunicipality)
            ->groupBy('municipality')
            ->select('municipality')
            ->inRandomOrder()
            ->take($this->answersCount - 1)
            ->get()
            ->pluck('municipality')
            ->push($correctMunicipality)
            ->shuffle()
            ->map($this->createAnswer(...));
    }
    
    public static function getEntities(): Collection
    {
        throw new LogicException('Neimplementováno.');
    }
    
}
