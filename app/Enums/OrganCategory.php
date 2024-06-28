<?php

namespace App\Enums;

use App\Interfaces\Category;
use App\Traits\EnumeratesCategories;

enum OrganCategory: int implements Category
{
    use EnumeratesCategories;
    
    case BuiltTo1799 = 1;
    case BuiltFrom1800To1944 = 2;
    case BuiltFrom1945To1989 = 3;
    case BuiltFrom1990 = 4;
    
    case Renaissance = 5;
    case Baroque = 6;
    case Romantic = 7;
    case NeobaroqueUniversal = 8;
    
    case Oldest = 9;
    case Biggest = 10;
    
    case ActionMechanical = 11;
    case ActionPneumatical = 12;
    case ActionElectrical = 13;
    case ActionBarker = 14;
    case WindchestSchleif = 15;         // zásuvková
    case WindchestKegel = 16;           // kuželková
    case WindchestMembran = 17;         // membránová
    
    const DATA = [
        self::BuiltTo1799->value            => ['name' => 'do 1799'],
        self::BuiltFrom1800To1944->value    => ['name' => '1800-1944'],
        self::BuiltFrom1945To1989->value    => ['name' => '1945-1989'],
        self::BuiltFrom1990->value          => ['name' => '1990-nyní'],
        
        self::Renaissance->value => [
            'name' => 'Renesanční',
        ],
        self::Baroque->value => [
            'name' => 'Barokní',
            'description' => 'Barokní a pozdně barokní varhany (cca do 1. pol. 19. stol.)',
        ],
        self::Romantic->value => [
            'name' => 'Romantické',
            'description' => 'Romantičtí varhany (cca od 2. pol. 19. stol.)',
        ],
        self::NeobaroqueUniversal->value => [
            'name' => 'Neobarokní a univerzální',
            'description' => 'Novodobé neobarokní a stylově nevyhraněné nástroje',
        ],
        
        self::Oldest->value => [
            'name' => 'Mimořádně starobylé',
            'description' => 'Varhany patřící v době svého vzniku k největším',
        ],
        self::Biggest->value => [
            'name' => 'Mimořádně velké',
            'description' => 'Varhany patřící mezi nejstarší na našem území',
        ],
        
        self::ActionMechanical->value => [
            'name' => 'Mechanická traktura',
            'description' => 'Klávesy s píšťalou jsou propojeny soustavou mechanických táhel',
        ],
        self::ActionPneumatical->value => [
            'name' => 'Pneumatická traktura',
            'description' => 'Klávesy s píšťalou jsou propojeny hadičkami, kterými proudí vzduch',
        ],
        self::ActionElectrical->value => [
            'name' => 'Elektrická traktura',
            'description' => 'Klávesy s píšťalou jsou propojeny elektrickým obvodem',
        ],
        self::ActionBarker->value => [
            'name' => 'Barkerova páka',
            'description' => 'Klávesy s píšťalou jsou propojeny soutavou mechanických táhel, které se však pohybují za pomocí tlaku vzduchu',
        ],
        self::WindchestSchleif->value       => ['name' => 'Zásuvková vzdušnice'],
        self::WindchestKegel->value         => ['name' => 'Kuželková vzdušnice'],
        self::WindchestMembran->value       => ['name' => 'Membránová vzdušnice'],
    ];
    
    public function getColor(): string
    {
        return match (true) {
            $this->isPeriodCategory() => 'light',
            $this->isTechnicalCategory() => 'secondary',
            default => 'primary'
        };
    }
    
    public function isPeriodCategory()
    {
        return in_array($this, [
            static::BuiltTo1799, static::BuiltFrom1800To1944, static::BuiltFrom1945To1989, static::BuiltFrom1990
        ]);
    }
    
    public function isTechnicalCategory()
    {
        return in_array($this, [
            static::ActionMechanical, static::ActionPneumatical, static::ActionElectrical, static::ActionBarker,
            static::WindchestSchleif, static::WindchestKegel, static::WindchestMembran,
        ]);
    }
    
    public static function getCategoryGroups()
    {
        $groups = ['generalCategories' => [], 'periodCategories' => [], 'technicalCategories' => []];
        foreach (static::cases() as $category) {
            $group = match (true) {
                $category->isPeriodCategory() => 'periodCategories',
                $category->isTechnicalCategory() => 'technicalCategories',
                default => 'generalCategories'
            };
            $groups[$group][] = $category;
        }
        return $groups;
    }
    
    public static function getGroupName($group)
    {
        return match ($group) {
            'periodCategories' => 'Podle období',
            'technicalCategories' => 'Podle stavby',
            'generalCategories' => 'Podle typu',
            default => throw new \RuntimeException
        };
    }
    
}