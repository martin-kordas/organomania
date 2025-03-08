<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Enums\OrganBuilderCategory;
use App\Enums\OrganCategory;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\User;
use App\Models\VarhanyNetOrgan;
use App\Models\VarhanyNetOrganBuilder;
use Closure;
use RuntimeException;

class VarhanyNetService
{
    
    const URL = 'http://www.varhany.net';
    
    private PendingRequest $client;
    
    public function __construct(private ?Closure $logger = null)
    {
        $this->client = Http::timeout(15)
            ->connectTimeout(15)
            ->retry(3, 2000);
    }
    
    private function log($message)
    {
        if (isset($this->logger)) ($this->logger)($message);
    }
    
    private function getResponseBody($response)
    {
        if (!$response->ok()) throw new RuntimeException('Server vrátil chybovou odpověď.');
        $body = $response->body();
        if ($body === '') throw new RuntimeException('Odpověď serveru je prázdná.');
        return $body;
    }
    
    public function scrapeOrgan(int $id)
    {
        $url = static::URL.'/cardheader.php';
        $response = $this->client->get($url, ['lok' => $id]);
        $body = $this->getResponseBody($response);
            
        $crawler = new Crawler($body);
        
        // jen stránka s místem, žádné informace o varhanách
        if ($crawler->filter('.lokalita_vypis_varhanaru')->count() <= 0) return null;
        
        $municipality = $crawler->filter('.bunka_prvni_obec > font > b')->innerText();
        $municipality = mb_strtoupper($municipality);
        $place = $crawler->filter('.bunka_prvni_patrocinium')->innerText();
        $history = $this->scrapeOrganHistory($crawler);
        
        // pouze poslední novostavba a následné přestavby
        $lastOrganKey = $history->keys()->last(
            fn ($key) => !$history[$key]['isRebuild'] && !$history[$key]['isRenovation']
        );
        if ($lastOrganKey === null) $lastOrganHistory = collect();
        else $lastOrganHistory = $history->slice($lastOrganKey);
        
        // $historyRowRenovation je poslední evidovaná renovace, z $lastOrganHistory renovace vymažeme
        $historyRowRenovation = $lastOrganHistory->last(
            fn ($historyRow) => $historyRow['isRenovation']
        );
        $lastOrganHistory = $lastOrganHistory->filter(
            fn ($historyRow) => !$historyRow['isRenovation']
        );
        
        $data = compact('id', 'municipality', 'place', 'history', 'lastOrganHistory', 'historyRowRenovation');
        //dump($data);
        
        VarhanyNetOrgan::create([
            'varhany_net_id' => $id,
            'scraped_at' => now(),
            'data' => serialize($data),
        ]);
        
        // $categories
        $years = collect($data['lastOrganHistory'])->pluck('yearBuilt')->filter()->toArray();
        $categories = OrganCategory::getPeriodCategories($years);
        // přejímáme technickou specifikaci uvedenou u posledního záznamu
        $historyLast = $data['lastOrganHistory']->last(
            fn ($historyRow) => $historyRow['technicalCategories'] === false || $historyRow['technicalCategories']->isNotEmpty()
        );
        if ($historyLast) {
            if ($historyLast['technicalCategories'] === false)
                $this->log("Nelze rozpoznat technickou kategorii varhan ({$historyLast['categories']}).");
            else $categories = $categories->merge($historyLast['technicalCategories']);
        }
        
        // $varhanyNetOrganBuilderId
        $varhanyNetOrganBuilderId = $varhanyNetRenovationOrganBuilderId = null;
        $history1 = $data['lastOrganHistory']->first();
        if ($history1) $varhanyNetOrganBuilderId = $history1['organBuilderId'];
        $varhanyNetRenovationOrganBuilderId = $data['historyRowRenovation']['organBuilderId'] ?? null;
        
        // $rebuilds
        $rebuilds = $data['lastOrganHistory']
            ->filter(
                fn ($historyRow) => $historyRow['isRebuild'] && (isset($historyRow['yearBuilt'], $historyRow['organBuilderId']))
            )
            ->map(
                fn ($historyRow) => [
                    'yearBuilt' => $historyRow['yearBuilt'],
                    'varhanyNetOrganBuilderId' => $historyRow['organBuilderId'],
                ]
            );
        
        return [
            'organ' => $this->createOrgan($data),
            'organCategories' => $categories,
            'varhanyNetOrganBuilderId' => $varhanyNetOrganBuilderId,
            'varhanyNetRenovationOrganBuilderId' => $varhanyNetRenovationOrganBuilderId,
            'rebuilds' => $rebuilds,
        ] ;
    }
    
    private function scrapeOrganHistory(Crawler $crawler)
    {
        $history = $crawler->filter('.lokalita_vypis_varhanaru tr[bgcolor="#ECEDC5"]')->each(function (Crawler $node, $i) {
            $jobType = $this->trim($node->filter('.modul_tri_red, .modul_tri_lok')->text(''));
            if ($jobType === '') return;
            
            $yearBuilt = $this->trim($node->filter('.modul_jedna_red, .modul_jedna_lok')->text(''));
            $organBuilder = $this->trim($node->filter('.modul_dva_red, .modul_dva_lok')->text(''));
            $categories = $this->trim($node->filter('.modul_ctyri_red, .modul_ctyri_lok')->text(''));
            
            if (in_array($organBuilder, ['neznámý', 'svépomocně'])) $organBuilderId = null;
            else {
                // TODO: je-li uvedeno více varhanářů, vezme se jen první uvedený (např. id 210)
                $organBuilderLink = $node->filter('.modul_dva_red a, .modul_dva_lok a');
                if ($organBuilderLink->count() <= 0) $organBuilderId = null;
                else {
                    $organBuilderHref = $this->trim($organBuilderLink->attr('href'));
                    $query = parse_url($organBuilderHref, PHP_URL_QUERY);
                    // TODO: někdy je varhanář bez odkazu (např. id 1886)
                    if (!$query) throw new RuntimeException('Nelze najít odkaz na varhanáře.');
                    $params = [];
                    parse_str($query, $params);
                    $organBuilderId = $params['idv'] ?? throw new RuntimeException('Nelze najít ID varhanáře.');
                }
            }
            
            return compact('yearBuilt', 'organBuilder', 'jobType', 'categories', 'organBuilderId');
        });
        
        // pouze novostavby a přestavby
        //  - TODO: neřešeny transfery varhan
        //  - TODO: +ped neřešeno (např. id 209), +poz neřešeno (např. id 671)
        //  - TODO: zsV neřešeno (např. id 710)
        //  - TODO: úprV neřešeno (např. id 710)
        return collect($history)->flatMap(function ($historyRow) {
            if (!is_array($historyRow)) return [];
            
            $matches = [];
            if (!preg_match('/^\S+/i', $historyRow['jobType'], $matches)) return [];
            $job = $matches[0];
            if (!in_array($job, ['V', 'Vs', 'přV', 'rozšV', 'P', 'restV'])) return [];
            $historyRow['isRebuild'] = in_array($job, ['přV', 'rozšV']);        // rozšV - např. id 3607
            $historyRow['isRenovation'] = in_array($job, ['restV']);            // restV - např. id 443
            
            $historyRow['manualsCount'] = $historyRow['stopsCount'] = null;
            $matches = [];
            if (preg_match('/- ([IV]+)(\/([0-9]+))?$/', $historyRow['jobType'], $matches)) {
                $historyRow['manualsCount'] = Helpers::parseRomanNumeral($matches[1]);
                $historyRow['stopsCount'] = ($matches[3] ?? '') ? (int)$matches[3] : null;
            }
            
            $matches = [];
            if (preg_match('/[0-9]{4}$/', $historyRow['yearBuilt'], $matches)) {
                $historyRow['yearBuilt'] = (int)$matches[0];
            }
            else $historyRow['yearBuilt'] = null;
            
            $historyRow['technicalCategories'] = collect();
            if ($historyRow['categories']) {
                $technicalCategories = static::getTechnicalCategoriesFromType($historyRow['categories']);
                // při nerozpoznatelné kategorii zatím jen zaznačíme FALSE, výjimku vyhodíme, až když je opravdu kategorie potřeba
                if (empty($technicalCategories) && !in_array($historyRow['categories'], ['?', '? ?'])) $historyRow['technicalCategories'] = false;
                else $historyRow['technicalCategories'] = $historyRow['technicalCategories']->merge($technicalCategories);
            }
                
            return [$historyRow];
        });
    }
    
    private function createOrgan($data)
    {
        $organ = new Organ;
        
        $organ->place = $data['place'];
        $organ->municipality = $data['municipality'];
        
        // manuals_count: poslední nalezená informace o rozsahu varhan
        // original_manuals_count: první nalezená informace o rozsahu varhan, pokud se liší od poslední nalezené informace
        $historyKeyFirst = $data['lastOrganHistory']->keys()->first(
            fn ($key) => isset($data['lastOrganHistory'][$key]['manualsCount'])
        );
        $historyKeyLast = $data['lastOrganHistory']->keys()->last(
            fn ($key) => isset($data['lastOrganHistory'][$key]['manualsCount'])
        );
        if (isset($historyKeyLast)) {
            $organ->manuals_count = $data['lastOrganHistory'][$historyKeyLast]['manualsCount'];
            $organ->stops_count = $data['lastOrganHistory'][$historyKeyLast]['stopsCount'];
        }
        if ($historyKeyFirst !== $historyKeyLast) {
            $organ->original_manuals_count = $data['lastOrganHistory'][$historyKeyFirst]['manualsCount'];
            $organ->original_stops_count = $data['lastOrganHistory'][$historyKeyFirst]['stopsCount'];
        }
        
        $history1 = $data['lastOrganHistory']->first();
        if ($history1) $organ->year_built = $history1['yearBuilt'];
        $organ->year_renovated = $data['historyRowRenovation']['yearBuilt'] ?? null;
        
        $organ->varhany_net_id = $data['id'];
        $organ->region_id = 1;
        $organ->latitude = $organ->longitude = 0;
        $organ->importance = 1;
        $organ->description = $this->getImportedMessage();
        $organ->user_id = User::USER_ID_MARTIN_KORDAS;
        
        return $organ;
    }
    
    public static function getTechnicalCategoriesFromType($type)
    {
        return match ($type) {
            'e', 'e ?', => [OrganCategory::ActionElectrical],
            'eu' => [OrganCategory::ActionElectrical, OrganCategory::WindchestUnit],
            'ek' => [OrganCategory::ActionElectrical, OrganCategory::WindchestKegel],
            'ez' => [OrganCategory::ActionElectrical, OrganCategory::WindchestSchleif],
            'ep', 'p/e', 'epvíce' => [OrganCategory::ActionElectrical, OrganCategory::ActionPneumatical],
            'epk' => [OrganCategory::ActionElectrical, OrganCategory::ActionPneumatical, OrganCategory::WindchestKegel],
            'm', 'm ?', 'm ? -p' => [OrganCategory::ActionMechanical],
            'm-Bkk', 'm-Bkk-mBk' => [OrganCategory::ActionBarker, OrganCategory::WindchestKegel],
            'm-Bkz' => [OrganCategory::ActionBarker, OrganCategory::WindchestSchleif],
            'm-Bk', 'm-Bk ? ' => [OrganCategory::ActionBarker],
            'mez', 'mz; rejstříková traktura elektrická' => [OrganCategory::ActionMechanical, OrganCategory::ActionElectrical, OrganCategory::WindchestSchleif],
            'mk', 'mk-pv' => [OrganCategory::ActionMechanical, OrganCategory::WindchestKegel],
            'mk-p' => [OrganCategory::ActionMechanical, OrganCategory::WindchestKegel, OrganCategory::ActionPneumatical],
            'mz', 'mz?', 'mz-ep', 'mz-e' => [OrganCategory::ActionMechanical, OrganCategory::WindchestSchleif],
            'p', 'p?', 'p ?', 'pp' => [OrganCategory::ActionPneumatical],
            'pv', 'pv-pv' => [OrganCategory::ActionPneumatical, OrganCategory::WindchestMembran], 
            'pk' => [OrganCategory::ActionPneumatical, OrganCategory::WindchestKegel],
            'pu' => [OrganCategory::ActionPneumatical, OrganCategory::WindchestUnit],
            'pz' => [OrganCategory::ActionPneumatical, OrganCategory::WindchestSchleif],
            'k', '?k' => [OrganCategory::WindchestKegel],
            'z' => [OrganCategory::WindchestSchleif],
            'u' => [OrganCategory::WindchestUnit],
            default => [],
        };
    }
    
    private function trim($str)
    {
        return trim(Helpers::normalizeWhiteSpace($str));
    }
    
    private function parseBirthDeath(string $text)
    {
        if ($text === '') return [null, null];
        
        $matches = [];
        if (!preg_match('/([0-9]{4}|- ), (.+)/', $text, $matches)) {
            return [null, null];
        }
        
        [, $year, $place] = $matches;
        $year = $year === '- ' ? null : (int)$year;
        $place = $place === '-' ? null : $place;
        return [$year, $place];
    }
    
    public function scrapeOrganBuilder(int $id)
    {
        $url = static::URL.'/zivotopis.php';
        $response = $this->client->get($url, ['idv' => $id]);
        $body = $this->getResponseBody($response);
        
        $crawler = new Crawler($body);
        $name = $this->trim($crawler->filter('.varhanar_nacionale > font')->innerText());
        
        $organBuilderInfo = $crawler
            ->filter(".tabulka_zivot")
            ->first()
            ->filter(".tabulka_zivot_td")
            ->each(function (Crawler $node, $i) {
                return $this->trim($node->text(''));
            });
        $birth = $organBuilderInfo[0] ?? '';
        $death = $organBuilderInfo[1] ?? '';
        $municipality = $organBuilderInfo[6] ?? '';
        
        if ($municipality === '') $municipality = null;
        // odstraníme číslování jednotlivých lokalit
        else $municipality = preg_replace('/[0-9]+\. /u', '', $municipality);
        
        [$birthYear, $birthPlace] = $this->parseBirthDeath($birth);
        [$deathYear, $deathPlace] = $this->parseBirthDeath($death);
        
        $data = compact(
            'id',
            'name', 'birth', 'death', 'municipality',
            'birthYear', 'birthPlace', 'deathYear', 'deathPlace'
        );
        //dump($data);
        
        VarhanyNetOrganBuilder::create([
            'varhany_net_id' => $id,
            'scraped_at' => now(),
            'data' => serialize($data),
        ]);
        
        if (!$birthYear && !$deathYear) $categories = collect();
        else {
            $yearFrom = $birthYear;
            $yearTo = $deathYear;
            $yearFrom ??= $yearTo;
            $categories = OrganBuilderCategory::getPeriodCategories($yearFrom, $yearTo);
        }
        
        return [
            'organBuilder' => $this->createOrganBuilder($data),
            'organBuilderCategories' => $categories,
        ] ;
    }
    
    private function createOrganBuilder($data)
    {
        $organBuilder = new OrganBuilder;
        
        // TOTO: nedokážeme rozlišit jméno a příjmení
        $organBuilder->last_name = $data['name'];
        $organBuilder->municipality = str($data['municipality'] ?? '')->limit(100, '');
        $organBuilder->place_of_birth = $data['birthPlace'];
        $organBuilder->place_of_death = $data['deathPlace'];
        // TODO: může být neznámé
        $organBuilder->active_from_year = $data['birthYear'] ?? 9999;
        $organBuilder->varhany_net_id = $data['id'];
        $organBuilder->region_id = 1;
        $organBuilder->latitude = $organBuilder->longitude = 0;
        $organBuilder->importance = 1;
        $organBuilder->description = $this->getImportedMessage();
        $organBuilder->user_id = User::USER_ID_MARTIN_KORDAS;
        
        if (isset($data['birthYear']) || isset($data['deathYear'])) {
            $organBuilder->active_period = implode(
                '–',
                [$data['birthYear'] ?? '', $data['deathYear'] ?? '']
            );
        }
        
        return $organBuilder;
    }
    
    private function getImportedMessage()
    {
        $dateFormat = Helpers::formatDate(now());
        return "Údaje byly importovány z webu www.varhany.net (stav ke dni $dateFormat).";
    }
    
    public function scrapeDisposition(int $id)
    {
        $url = static::URL.'/disp_vypis.php';
        $response = $this->client->get($url, ['id' => $id]);
        $body = $this->getResponseBody($response);
        $crawler = new Crawler($body);
        
        $heading = $crawler->filter('font[size=3]')->first();
        $municipality = $this->trim($heading->filter('b')->first()->text());
        $place = str($heading->innerText())->replaceStart(', ', '');
        $place = $this->trim($place);
        
        $headingText = $heading->text();
        $matches = [];
        if (!preg_match('/, ([^,]+)$/', $headingText, $matches)) throw new RuntimeException('Nebyl nalezen rok dispozice.');
        $year = $this->trim($matches[1]);
        if ($year === '-') $year = null;
        
        $disposition = $crawler->filter('.dispozice_tabulka')->html();
        $disposition = str($disposition)
            ->replace('<b> <font color="#000066" size="2"> ', "**")
            ->replace('<br></font></b>', "**<br>")
            ->replace('</b>', "**")
            ->replace('<br>', "\n")
            // TODO: doplnění odřádkování před úvodní popis dispozice (např. rok atd.) - obvykle se nachází před ztučnělým názvem manuálu
            //  - např. http://www.varhany.net/disp_vypis.php?id=1002
            ->replaceMatches('/(\S)\*\*(\S)/', "\$1\n**\$2")
            ->replace('´', "'")
            ->stripTags()
            ->explode("\n")
            ->map(
                fn ($row) => $this->trim($row)
            )
            ->implode("\n");
        
        return compact('municipality', 'place', 'year', 'disposition');
    }
    
}
