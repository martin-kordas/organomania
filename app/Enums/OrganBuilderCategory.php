<?php

namespace App\Enums;

use App\Traits\EnumeratesCategories;
use Illuminate\Database\Eloquent\Builder;
use App\Interfaces\Category;
use App\Models\OrganBuilder;

enum OrganBuilderCategory: int implements Category
{
    use EnumeratesCategories;
    
    case BuiltTo1799 = 1;
    case BuiltFrom1800To1859 = 2;
    case BuiltFrom1860To1944 = 10;
    case BuiltFrom1945To1989 = 3;
    case BuiltFrom1990 = 4;
    
    case Baroque = 5;
    case Romantic = 6;
    case NeobaroqueUniversal = 7;
    
    case FactoryProduction = 8;
    case Restoration = 9;
    
    const DATA = [
        self::BuiltTo1799->value            => ['name' => 'do 1799'],
        self::BuiltFrom1800To1859->value    => ['name' => '1800–1859'],
        self::BuiltFrom1860To1944->value    => ['name' => '1860–1944'],
        self::BuiltFrom1945To1989->value    => ['name' => '1945–1989'],
        self::BuiltFrom1990->value          => ['name' => '1990–nyní'],
        
        self::Baroque->value => [
            'name' => 'Barokní',
            'description' => 'Barokní a pozdně barokní varhany (cca do 1. pol. 19. stol.)',
        ],
        self::Romantic->value => [
            'name' => 'Romantické',
            'description' => 'Romantické varhany (cca od 2. pol. 19. stol.)',
        ],
        self::NeobaroqueUniversal->value => [
            'name' => 'Neobarokní a univerzální',
            'description' => 'Novodobé nástroje čerpající z tradic barokního varhanářství a stylově nevyhraněné nástroje',
        ],
        self::FactoryProduction->value => [
            'name' => 'Tovární výroba',
            'description' => 'Varhany vyráběné sériovou výrobou v továrnách',
        ],
        self::Restoration->value => [
            'name' => 'Restaurování',
            'description' => 'Restaurování vzácných historických nástrojů',
        ],
    ];
    
    public function getValue(): int
    {
        return $this->value;
    }
    
    public function getColor(): string
    {
        return $this->isPeriodCategory() ? 'light' : 'primary';
    }
    
    public function isPeriodCategory(): bool
    {
        return in_array($this, [
            static::BuiltTo1799, static::BuiltFrom1800To1859, static::BuiltFrom1860To1944, static::BuiltFrom1945To1989, static::BuiltFrom1990
        ]);
    }
    
    public function getItemsUrl(): string
    {
        return route('organ-builders.index', ['filterCategories' => [$this->value]]);
    }
    
    public function getOrganBuildersCount()
    {
        return OrganBuilder::whereHas('organBuilderCategories', function (Builder $query) {
            $query->where('id', $this->value);
        })->inland()->count();
    }
    
    public static function getCategoryGroups()
    {
        $groups = ['generalCategories' => [], 'periodCategories' => []];
        foreach (static::cases() as $category) {
            if ($category->isPeriodCategory()) $groups['periodCategories'][] = $category;
            else $groups['generalCategories'][] = $category;
        }
        return $groups;
    }
    
    public static function getGroupName($group)
    {
        return match ($group) {
            'periodCategories' => 'Kategorie podle období',
            'generalCategories' => 'Kategorie podle typu',
            default => throw new \RuntimeException
        };
    }
    
}