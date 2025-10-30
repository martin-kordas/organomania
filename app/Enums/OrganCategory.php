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
    case FromBookMostImportantOrgans = 21;
    case FromBookBaroqueOrganBuilding = 22;
    
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

    case CaseRenaissance = 23;
    case CaseBaroque = 24;
    case CaseRococo = 25;
    case CaseClassicistic = 26;
    case CaseEmpire = 27;
    case CaseNeogothic = 28;
    case CaseNeoromanesque = 29;
    case CaseNeorenaissance = 30;
    case CaseNeobaroque = 31;
    case CaseArtNouveau = 32;
    case CaseModern = 33;
    
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
            'shortName' => 'Neobarokní/univerzální',
            'description' => 'Novodobé nástroje, které se vracejí ke zvukovým ideálům barokních varhan, nebo jsou stylově nevyhraněné a umožňují tak hru skladeb všech období',
        ],
        self::FromBookMostImportantOrgans->value => [
            'name' => 'Z knihy Nejvýznamnější varhany v ČR',
            'shortName' => 'Z knihy Nejvýznamnější varhany',
            'description' => 'Nástroje zařazené v knize Nejvýznamnější varhany České republiky (Štěpán Svoboda, Jiří Krátký, CPress, 2019)',
        ],
        self::FromBookBaroqueOrganBuilding->value => [
            'name' => 'Z knihy Barokní varhanářství na Moravě',
            'shortName' => 'Z knihy Barokní varhanářství na Moravě',
            'description' => 'Nástroje zařazené v knize Barokní varhanářství na Moravě - 2. díl, varhany (Jiří Sehnal)',
        ],
        
        self::Oldest->value => [
            'name' => 'Mimořádně starobylé',
            'shortName' => 'Starobylé',
            'description' => 'Varhany patřící mezi nejstarší na našem území',
        ],
        self::Biggest->value => [
            'name' => 'Mimořádně velké',
            'shortName' => 'Velké',
            'description' => 'Varhany patřící v době svého vzniku k největším',
        ],
        self::ValuableCase->value => [
            'name' => 'Mimořádně cenná skříň',
            'shortName' => 'Cenná skříň',
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
            'description' => 'Úsporná konstrukce varhan, při níž se jedna řada píšťal používá pro několik rejstříků současně'
        ],

        self::CaseRenaissance->value => [
            'name' => 'Renesanční skříně',
        ],
        self::CaseBaroque->value => [
            'name' => 'Barokní skříně',
        ],
        self::CaseRococo->value => [
            'name' => 'Rokokové skříně',
        ],
        self::CaseClassicistic->value => [  
            'name' => 'Klasicistní skříně',
        ],
        self::CaseEmpire->value => [  
            'name' => 'Empírové skříně',
        ],
        self::CaseNeogothic->value => [
            'name' => 'Neogotické skříně',
        ],
        self::CaseNeoromanesque->value => [
            'name' => 'Neorománské skříně',
        ],
        self::CaseNeorenaissance->value => [
            'name' => 'Neorenesanční skříně',
        ],
        self::CaseNeobaroque->value => [
            'name' => 'Neobarokní skříně',
        ],
        self::CaseArtNouveau->value => [
            'name' => 'Secesní skříně',
        ],
        self::CaseModern->value => [
            'name' => 'Moderní skříně',
        ],
    ];
    
    public function getValue(): int
    {
        return $this->value;
    }

    public function getOrderValue(): int
    {
        // srv. Organ::organCategories()
        return match ($this) {
            static::BuiltFrom1860To1944 => 2.5,
            default => $this->value,
        };
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
            static::BuiltTo1799, static::BuiltFrom1800To1859, static::BuiltFrom1860To1944, static::BuiltFrom1945To1989, static::BuiltFrom1990,
            // HACK: nejde o kategorii období - řadíme ji sem, protože chceme, aby se zobrazila jen v detailu (stejně jako kategorie období)
            static::FromBookMostImportantOrgans, static::FromBookBaroqueOrganBuilding,
        ]);
    }
    
    public function isTechnicalCategory()
    {
        return $this->isActionCategory() || $this->isWindchestCategory();
    }
    
    public function isActionCategory()
    {
        return in_array($this, [
            static::ActionMechanical, static::ActionPneumatical, static::ActionElectrical, static::ActionBarker,
        ]);
    }
    
    public function isWindchestCategory()
    {
        return in_array($this, [
            static::WindchestSchleif, static::WindchestKegel, static::WindchestMembran, static::WindchestUnit,
        ]);
    }
    
    public function isCaseCategory()
    {
        return in_array($this, [
            static::CaseRenaissance, static::CaseBaroque, static::CaseRococo, static::CaseClassicistic,
            static::CaseNeogothic, static::CaseNeoromanesque, static::CaseNeobaroque, static::CaseArtNouveau, static::CaseModern
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
                $category->isCaseCategory() => 'caseCategories',
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
            'caseCategories' => 'Kategorie podle stylu skříně',
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