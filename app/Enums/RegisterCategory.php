<?php

namespace App\Enums;

enum RegisterCategory: int
{
    
    case Principal = 1;
    case Flute = 2;
    case Gedackt = 3;
    case String = 4;
    case Reed = 5;
    case Mixed = 6;
    
    case Vychvevne = 10;
    case Konicke = 11;
    case Trychtyrovite = 12;
    case JazykoveNarazne = 13;
    case JazykovePrurazne = 14;
    case Prefukujici = 15;
    case Regaly = 16;
    case Alikvotni = 17;
    case Drevene = 18;
    
    const DATA = [
        self::Principal->value => [
            'name' => 'Principály',
            'description' => 'Rejstříky s plným a nosným tónem, tvoří zvukový základ varhan'
        ],
        self::Flute->value => [
            'name' => 'Flétny',
            'description' => 'Rejstříky širší konstrukce a jemného zvuku, který dobře splývá s ostatními rejstříky',
        ],
        self::Gedackt->value => [
            'name' => 'Kryty',
            'description' => 'Rejstříky s píšťalami zakrytými kloboučkem nebo zátkou - díky tomu vydávají zvuk o oktávu nižší než otevřené píšťaly'
        ],
        self::String->value => [
            'name' => 'Smyky',
            'description' => 'Rejstříky s píšťalami užší konstrukce a ostřejšího zabarvení, napodobující smyčcové nástroje'
        ],
        self::Reed->value => [
            'name' => 'Jazyky',
            'description' => 'Rejstříky výrazného drnčivého zvuku, jejichž píšťaly vyluzují zvuk pomocí kmitání kovového jazýčku'
        ],
        self::Mixed->value => [
            'name' => 'Smíšené hlasy',
            'description' => 'Rejstříky složené z více sborů píšťal - na každé klávese zní několik píšťal současně',
        ],
        
        self::Trychtyrovite->value => [
            'name' => 'Trychtýřovité',
            'description' => 'Rejstříky s píšťalami, které se do výšky rozšiřují'
        ],
        self::Konicke->value => [
            'name' => 'Kónické',
            'description' => 'Rejstříky s píšťalami, které se do výšky zužují'
        ],
        self::Prefukujici->value => [
            'name' => 'Přefukující',
            'description' => 'Rejstříky s píšťalami, které mají pro stejný tón dvojnásobnou délku než píšťaly běžných rejstříků - díky tomu disponují výraznější barvou zvuku'
        ],
        self::JazykoveNarazne->value => [
            'name' => 'Jazykové nárazné',
            'description' => 'Nejobvyklejší typ jazykových rejstříků - jejich jazýček v píšťale naráží na otvor v pouzdru'
        ],
        self::JazykovePrurazne->value => [
            'name' => 'Jazykové průrazné',
            'description' => 'Jazykové rejstříky, jejichž jazýček v píšťale kmitá otvorem v pouzdru - mají jemnější zvuk a byly oblíbené v romantismu'
        ],
        self::Regaly->value => [
            'name' => 'Regály',
            'description' => 'Krátké jazykové rejstříky rozmanité konstrukce, vydávající syrový drnčivý zvuk'
        ],
        self::Vychvevne->value => [
            'name' => 'Výchvěvné',
            'description' => 'Rejstříky, jejichž zvuk vytvářejí dojem rychle se měnící výšky tónu - toho se docílí tím, že hrají současně 2 píšťaly naladěné na nepatrně odlišný tón'
        ],
        self::Alikvotni->value => [
            'name' => 'Alikvotní',
            'description' => 'Rejstříky znějící v jiné než základní poloze - např. v kvintové, terciové atd.'
        ],
        self::Drevene->value => [
            'name' => 'Dřevěné',
            'description' => 'Rejstříky stavěné často s dřevěnými píšťalami'
        ],
    ];
    
    public function isMain()
    {
        return $this->value < 10;
    }
    
    public function getName()
    {
        $name = static::DATA[$this->value]['name'] ?? throw new \LogicException;
        return __($name);
    }
    
    public function getDescription()
    {
        $description = static::DATA[$this->value]['description'] ?? null;
        return isset($description) ? __($description) : null;
    }
    
    public static function getMainCategories()
    {
        return collect(static::cases())->filter(
            fn($category) => $category->isMain()
        )->toArray();
    }
    
    public static function getCategoryGroups()
    {
        $groups = ['mainCategories' => [], 'otherCategories' => []];
        foreach (static::cases() as $category) {
            $group = match (true) {
                $category->isMain() => 'mainCategories',
                default => 'otherCategories'
            };
            $groups[$group][] = $category;
        }
        return $groups;
    }
    
    public static function getGroupName($group)
    {
        return match ($group) {
            'mainCategories' => 'Základní kategorie',
            'otherCategories' => 'Ostatní kategorie',
            default => throw new \RuntimeException
        };
    }
    
}