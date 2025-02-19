<?php

namespace App\Enums;

enum KancionalSongCategory: int
{
    
    case Prayers = 0;
    case Advent = 1;
    case Christmas = 2;
    case Lent = 3;
    case Easter = 4;
    case General = 5;
    case Psalms = 6;
    case Christ = 7;
    case Saints = 8;
    case Occasional = 9;
    
    case Ordinaries = 10;
    
    const DATA = [
        self::Prayers->value => [
            'name' => 'Modlitby a pobožosti',
            'background' => '#f2f2f2',
            'color' => 'black',
        ],
        self::Advent->value => [
            'name' => 'Adventní',
            'background' => 'violet',
            'color' => 'black',
        ],
        self::Christmas->value => [
            'name' => 'Vánoční',
            'background' => '#FFFF66',
            'color' => 'black',
        ],
        self::Lent->value => [
            'name' => 'Postní',
            'background' => 'purple',
            'color' => 'white',
        ],
        self::Easter->value => [
            'name' => 'Vánoční',
            'background' => 'red',
            'color' => 'white',
        ],
        self::General->value => [
            'name' => 'Mešní obecné',
            'background' => 'green',
            'color' => 'white',
        ],
        self::Psalms->value => [
            'name' => 'Žalmy',
            'background' => 'black',
            'color' => 'white',
        ],
        self::Christ->value => [
            'name' => 'K Pánu Ježíši',
            'background' => '#8a5c1a',
            'color' => 'white',
        ],
        self::Saints->value => [
            'name' => 'K Panně Marii a svatým',
            'background' => 'darkblue',
            'color' => 'white',
        ],
        self::Occasional->value => [
            'name' => 'Příležitostné',
            'background' => '#180401',
            'color' => 'white',
        ],
        
        self::Ordinaries->value => [
            'name' => 'Ordinaria',
            'background' => 'lightgreen',
            'color' => 'black',
        ],
    ];
    
    public function getColor(): string
    {
        return static::DATA[$this->value]['color'] ?? throw new \LogicException;
    }
    
    public function getBackground(): string
    {
        return static::DATA[$this->value]['background'] ?? throw new \LogicException;
    }
    
    public function getFullName(): string
    {
        $name = $this->getName();
        if ($this === static::Ordinaries) return "502-509: $name";
        return "{$this->value}xx: $name";
    }
    
    public function getName(): string
    {
        return __(static::DATA[$this->value]['name'] ?? throw new \LogicException);
    }
    
}