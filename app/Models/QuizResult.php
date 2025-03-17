<?php

namespace App\Models;

use App\Enums\QuizDifficultyLevel;
use App\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    
    protected $guarded = [];
    
    protected function difficultyLevel(): Attribute
    {
        return Helpers::makeEnumAttribute('difficulty_level', QuizDifficultyLevel::from(...));
    }
    
}
