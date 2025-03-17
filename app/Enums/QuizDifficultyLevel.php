<?php

namespace App\Enums;

enum QuizDifficultyLevel: int
{
    
    case Easy = 1;
    case Medium = 2;
    case Advanced = 3;
    
    const DATA = [
        self::Easy->value => [
            'name' => 'Jednoduchá',
        ],
        self::Medium->value => [
            'name' => 'Střední',
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