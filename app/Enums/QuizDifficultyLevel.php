<?php

namespace App\Enums;

enum QuizDifficultyLevel: int
{
    
    case Easy = 1;
    case Advanced = 2;
    
    const DATA = [
        self::Easy->value => [
            'name' => 'Jednoduchá',
        ],
        self::Advanced->value => [
            'name' => 'Pokročilá',
        ],
    ];

    public function getName(): string
    {
        return __(static::DATA[$this->value]['name'] ?? throw new \LogicException);
    }
    
}