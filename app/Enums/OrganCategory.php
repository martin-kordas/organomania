<?php

namespace App\Enums;

use App\Models\Organ;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Interfaces\Category;
use App\Traits\EnumeratesCategories;

enum OrganCategory: int implements Category
{
    use EnumeratesCategories;
    
    case BuiltTo1799 = 1;
    case BuiltFrom1800To1859 = 2;
    case BuiltFrom1860To1944 = 19;
    case BuiltFrom1945To1989 = 3;
    case BuiltFrom1990 = 4;
    
    case Renaissance = 5;
    case Baroque = 6;
    case Romantic = 7;
    case NeobaroqueUniversal = 8;
    
    case Oldest = 9;
    case Biggest = 10;
    case ValuableCase = 18;
    
    case ActionMechanical = 11;
    case ActionPneumatical = 12;
    case ActionElectrical = 13;
    case ActionBarker = 14;
    case WindchestSchleif = 15;         // zásuvková
    case WindchestKegel = 16;           // kuželková
    case WindchestMembran = 17;         // membránová
    case WindchestUnit = 20;            // unit
    
    const DATA = [
        self::BuiltTo1799->value            => ['name' => 'do 1799'],
        self::BuiltFrom1800To1859->value    => ['name' => '1800–1859'],
        self::BuiltFrom1860To1944->value    => ['name' => '1860–1944'],
        self::BuiltFrom1945To1989->value    => ['name' => '1945–1989'],
        self::BuiltFrom1990->value          => ['name' => '1990–nyní'],
        
        self::Renaissance->value => [
            'name' => 'Renesanční',
            'description' => 'Pro renesanční varhany je kromě renesančního tvarosloví skříně typická barevná různorodost rejstříků'
        ],
        self::Baroque->value => [
            'name' => 'Barokní',
            'description' => 'Barokní a pozdně barokní varhany (cca do 1. pol. 19. stol.) - typické je pro ně rovnoměrné rozložení rejstříků ve všech polohách a manuálech a tradiční lesklý zvuk varhan',
        ],
        self::Romantic->value => [
            'name' => 'Romantické',
            'description' => 'Romantické varhany (cca od 2. pol. 19. stol.) následují zvukový ideál symfonického orchestru a jsou charakteristické větším podílem rejstříků v nižších polohách a smyků',
        ],
        self::NeobaroqueUniversal->value => [
            'name' => 'Neobarokní a univerzální',
            'description' => 'Novodobé nástroje, které se vracejí ke zvukovým ideálům barokních varhan, nebo jsou stylově nevyhraněné a umožňují tak hru skladeb všech období',
        ],
        
        self::Oldest->value => [
            'name' => 'Mimořádně starobylé',
            'description' => 'Varhany patřící mezi nejstarší na našem území',
        ],
        self::Biggest->value => [
            'name' => 'Mimořádně velké',
            'description' => 'Varhany patřící v době svého vzniku k největším',
        ],
        self::ValuableCase->value => [
            'name' => 'Mimořádně cenná skříň',
            'description' => 'Varhany postavené do výtvarně nebo konstrukčně cenné skříně',
        ],
        
        self::ActionMechanical->value => [
            'name' => 'Mechanická traktura',
            'description' => 'Klávesy jsou s píšťalami propojeny soustavou mechanických táhel',
        ],
        self::ActionPneumatical->value => [
            'name' => 'Pneumatická traktura',
            'description' => 'Klávesy jsou s píšťalami propojeny hadičkami, kterými proudí vzduch',
        ],
        self::ActionElectrical->value => [
            'name' => 'Elektrická traktura',
            'description' => 'Klávesy jsou s píšťalami propojeny elektrickým obvodem',
        ],
        self::ActionBarker->value => [
            'name' => 'Barkerova páka',
            'description' => 'Klávesy jsou s píšťalami propojeny soutavou mechanických táhel, která se však pohybují za pomocí tlaku vzduchu',
        ],
        self::WindchestSchleif->value       => [
            'name' => 'Zásuvková vzdušnice',
            'description' => 'Klasická konstrukce varhan, kde se zapínání rejstříků provádí posunování dřevěných desek (zásuvek), které odkrývají přívod vzduchu k píšťalám'
        ],
        self::WindchestKegel->value         => [
            'name' => 'Kuželková vzdušnice',
            'description' => 'Konstrukce varhan, typická pro romantické varhanářství, kde se píšťala po stisku klávesy rozezní nadzvihnutím kuželky, která uvolní přívod vzduchu'
        ],
        self::WindchestMembran->value       => [
            'name' => 'Membránová vzdušnice',
            'description' => 'Konstrukce varhan založená na výpustném systému, kde po stisku klávesy klesne tlak vzduchu tlačícího na membránu a díky tomu se uvolní přívod vzduchu pro rozeznění píšťaly'
        ],
        self::WindchestUnit->value       => [
            'name' => 'Vzdušnice unit',
            'description' => 'Úsporná konstrukce varhan, při níž se jedna řada píšťal používá pro několik rejstříků současně.'
        ],
    ];
    
    public function getValue(): int
    {
        return $this->value;
    }
    
    public function getColor(): string
    {
        return match (true) {
            $this->isPeriodCategory() => 'light',
            $this->isTechnicalCategory() => 'secondary',
            default => 'primary'
        };
    }
    
    public function isPeriodCategory(): bool
    {
        return in_array($this, [
            static::BuiltTo1799, static::BuiltFrom1800To1859, static::BuiltFrom1860To1944, static::BuiltFrom1945To1989, static::BuiltFrom1990
        ]);
    }
    
    public function isTechnicalCategory()
    {
        return in_array($this, [
            static::ActionMechanical, static::ActionPneumatical, static::ActionElectrical, static::ActionBarker,
            static::WindchestSchleif, static::WindchestKegel, static::WindchestMembran, static::WindchestUnit,
        ]);
    }
    
    public function isExtraordinaryCategory()
    {
        return in_array($this, [
            static::Oldest, static::Biggest
        ]);
    }
    
    public function getItemsUrl(): string
    {
        return route('organs.index', ['filterCategories' => [$this->value]]);
    }
    
    public function getOrgansCount()
    {
        return Organ::whereHas('organCategories', function (Builder $query) {
            $query->where('id', $this->value);
        })->count();
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
            'periodCategories' => 'Kategorie podle období',
            'technicalCategories' => 'Kategorie podle stavby',
            'generalCategories' => 'Kategorie podle typu',
            default => throw new \RuntimeException
        };
    }
    
    public static function getPeriodCategories(int|array $years)
    {
        $years = Arr::wrap($years);
        
        $categories = collect();
        foreach ($years as $year) {
            $category = match (true) {
                $year <= 1799 => static::BuiltTo1799,
                $year <= 1859 => static::BuiltFrom1800To1859,
                $year <= 1944 => static::BuiltFrom1860To1944,
                $year <= 1989 => static::BuiltFrom1945To1989,
                default => static::BuiltFrom1990,
            };
            if (!$categories->contains($category)) $categories[] = $category;
        }
        
        return $categories;
    }
    
}