<?php

namespace App;

use Transliterator;
use NumberFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Carbon\Carbon;
use Carbon\Translator as CarbonTranslator;

class Helpers
{
    
    private static function arrayKeysChangeCase(array $array, callable $changeCaseCb)
    {
        return Arr::mapWithKeys(
            $array,
            fn($item, $key) => [$changeCaseCb($key) => $item]
        );
    }
    
    static function arrayKeysCamel(array $array): array
    {
        return static::arrayKeysChangeCase($array, Str::camel(...));
    }
    
    static function arrayKeysSnake(array $array): array
    {
        return static::arrayKeysChangeCase($array, Str::snake(...));
    }
    
    static function stripAccents(string $string): string
    {
        // https://stackoverflow.com/a/11743977/14967413
        $transliterator = Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: NFC;', Transliterator::FORWARD);
        return $transliterator->transliterate($string);
    }
    
    static function highlightEscapeText(string $haystack, string $needle, bool $words = false)
    {
        $fn = $words ? static::highlightTextWords(...) : static::highlightText(...);
        return $fn(
            htmlspecialchars($haystack, ENT_QUOTES),
            htmlspecialchars($needle, ENT_QUOTES)
        );
    }
    
    /**
     * zvýrazní souvislý text $needle v $haystack
     */
    static function highlightText(string $haystack, string $needle)
    {
        $haystack1 = static::stripAccents($haystack);
        $needle1 = static::stripAccents($needle);
        $pos = mb_stripos($haystack1, $needle1);
        if ($pos === false) return $haystack;
        
        $needleLength = mb_strlen($needle);
        $parts = [
            mb_substr($haystack, 0, $pos),
            '<mark class="px-0">',
            mb_substr($haystack, $pos, $needleLength),
            '</mark>',
            mb_substr($haystack, $pos + $needleLength),
        ];
        return implode($parts);
    }
    
    /**
     * rozdělí text $needle na jednotlivá slova a zvýrazní je v $haystack 
     */
    static function highlightTextWords(string $haystack, string $needle)
    {
        $haystack1 = static::stripAccents($haystack);
        $needle1 = static::stripAccents($needle);

        // $needle rozdělíme na slova
        $words = str($needle1)->explode(' ')
            ->filter(fn ($word) => mb_strlen($word) >= 3)
            ->map(trim(...));
        $alternation = $words->map(
            fn ($word) => preg_quote($word, '/')
        )->implode('|');
        $regex = "/($alternation)/iu";
        
        // matching provedeme vůči $haystack1 (bez diakritiky)
        // nalezená slova pak na základě offsetů zvýrazňujeme v původním $haystack
        //  - postupujeme odzadu, jinak by offsety nesouhlasily
        $matches = [];
        if (preg_match_all($regex, $haystack1, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as [$word, $offset]) {
                $highlightStart = $offset;
                $highlightEnd = $offset + mb_strlen($word);
                $haystack = static::mbSubstrReplace($haystack, '</mark>', $highlightEnd, 0);
                $haystack = static::mbSubstrReplace($haystack, '<mark class="px-0">', $highlightStart, 0);
            }
        }
        return $haystack;
    }
    
    // https://stackoverflow.com/a/35638691/14967413
    static function mbSubstrReplace($original, $replacement, $position, $length)
    {
        $startString = mb_substr($original, 0, $position);
        $endString = mb_substr($original, $position + $length, mb_strlen($original));

        $out = $startString . $replacement . $endString;

        return $out;
    }
    
    // lze řešit i Laravel funkcí trans_choice(), ale toto je asi pohodlnější
    static function declineCount($count, $text0, $text1, $text2)
    {
        return match (true) {
            $count <= 0 || $count >= 5 => $text0,
            $count === 1 => $text1,
            $count <= 4 => $text2,
        };
    }

    static function makeInitials(string $string): string
    {
        return str($string)
            ->explode(' ')
            ->map(function($word) { 
                // slovo musí začínat velkým písmenem
                return preg_match('/^\p{Lu}/u', $word)
                    ? str($word)->substr(0, 1) . '.'
                    : $word; 
            })
            ->join(' ');
    }
    
    static function formatUrl($url)
    {
        foreach (['http://', 'https://'] as $scheme) {
            if (str_starts_with($url, $scheme)) {
                return mb_substr($url, mb_strlen($scheme));
            }
        }
        return $url;
    }
    
    static function swap(&$var1, &$var2)
    {
        [$var1, $var2] = [$var2, $var1];
    }
    
    static function makeEnumAttribute(string $idField, callable $fromMethod)
    {
        return Attribute::make(
            get: fn (mixed $_value, array $attributes) => isset($attributes[$idField]) ? $fromMethod($attributes[$idField]) : null,
            set: fn (?\BackedEnum $enum) => isset($enum) ? [$idField => $enum->value] : [],
        );
    }
    
    static function parseRomanNumeral(string $numeral)
    {
        return match ($numeral) {
            'I' => 1,
            'II' => 2,
            'III' => 3,
            'IV' => 4,
            'V' => 5,
            'VI' => 6,
            default => throw new \RuntimeException
        };
    }
    
    static function formatRomanNumeral(int $num)
    {
        $locale = app()->getLocale();
        static $nf = new \NumberFormatter("$locale@numbers=roman", \NumberFormatter::DECIMAL);
        return $nf->format($num);
    }
    
    static function formatDate(Carbon $date, bool $monthNumber = false)
    {
        if ($monthNumber) {
            $format = app()->getLocale() === 'en' ? 'd/m/Y' : 'j. n. Y';
            return $date->format($format);
        }
        else {
            // Carbon zřejmě neumí automaticky změnit formát podle locale
            $format = app()->getLocale() === 'en' ? 'jS F Y' : 'j. F Y';
            return $date->translatedFormat($format);
        }
    }
    
    static function formatTime(string $time, bool $seconds = true)
    {
        $outputFormat = $seconds ? 'H:i.s' : 'H:i';
        return Carbon::createFromFormat('H:i:s', $time)->format($outputFormat);
    }
    
    static function formatDateTime(Carbon $date, bool $monthNumber = false, bool $seconds = true)
    {
        $dateStr = static::formatDate($date, $monthNumber);
        $timeStr = static::formatTime($date->format('H:i:s'), $seconds);
        return "$dateStr $timeStr";
    }
    
    static function getMonths()
    {
        $locale = $locale = app()->getLocale();
        $translator = CarbonTranslator::get($locale);
        $monthsAll = $translator->getMessages()[$locale];
        $months = $monthsAll['months_standalone'] ?? $monthsAll['months'];
        
        $months2 = [];
        foreach ($months as $i => $month) {
            $months2[$i + 1] = $month;
        }
        return $months2;
    }
    
    static function formatNumber(float $number, int $style = NumberFormatter::DECIMAL, ?int $decimals = null)
    {
        $formatter = app(NumberFormatter::class, ['style' => $style]);
        if (isset($decimals)) {
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
        }
        return $formatter->format($number);
    }
    
    static function formatCurrency(float $amount, string $currency = 'Kč', bool $html = true)
    {
        $separator = $html ? '&nbsp;' : ' ';
        $number = static::formatNumber($amount, decimals: 2);
        return $number . $separator . $currency;
    }
    
    static function array2Csv(array $data, string $columnSeparator = ',')
    {
        $rowsStr = array_map(
            fn($row) => implode($columnSeparator, $row),
            $data
        );
        return implode("\n", $rowsStr);
    }
    
    static function isCrawler()
    {
        return (new CrawlerDetect)->isCrawler();
    }
    
    static function getCanonicalUrl($lang = null)
    {
        $url = url()->current();
        $params = [];
        if (request()->routeIs('organs.index', 'organ-builders.index', 'festivals.index', 'competitions.index')) {
            $params['perPage'] = 300;
        }
        if (isset($lang)) $params['lang'] = $lang;
        
        // HACK: lokalizované routy
        if (request()->routeIs(['organs.show', 'organs.show-cs'])) {
            if ($lang === 'cs') $url = str($url)->replace('/organs/', '/varhany/');
            elseif ($lang === 'en') $url = str($url)->replace('/varhany/', '/organs/');
        }
        elseif (request()->routeIs(['organ-builders.show', 'organ-builders.show-cs'])) {
            if ($lang === 'cs') $url = str($url)->replace('/organ-builders/', '/varhanari/');
            elseif ($lang === 'en') $url = str($url)->replace('/varhanari/', '/organ-builders/');
        }
        elseif (request()->routeIs(['about-organ', 'about-organ-cs'])) {
            if ($lang === 'cs') $url = str($url)->replace('/about-organ', '/o-varhanach');
            elseif ($lang === 'en') $url = str($url)->replace('/o-varhanach', '/about-organ');
        }
        
        $query = http_build_query($params);
        if ($query) $query = "?$query";
        return $url . $query;
    }
    
    static function getDistance(float $latitude1, float $longitude1, float $latitude2, float $longitude2): float
    {
        $coordinate1 = new Coordinate($latitude1, $longitude1);
        $coordinate2 = new Coordinate($latitude2, $longitude2);
        $calculator = new Vincenty();
        return $calculator->getDistance($coordinate1, $coordinate2);
    }
    
    static function formatUrlsInLiterature($literature)
    {
        return preg_replace(
            '#https?://[^ ]*[^. ]#',
            '<a href="$0" target="_blank">$0</a>',
            e($literature)
        );
    }
    
    static function normalizeLineBreaks(string $string)
    {
        return preg_replace('~\R~u', "\n", $string);
    }
    
    static function normalizeWhiteSpace(string $string)
    {
        return preg_replace('/\s+/u', ' ', $string);
    }

    static function nameToLowerCase(string $name)
    {
        return preg_replace_callback(
            '/\b(\p{Lu})+\b/u',
            fn ($matches) => mb_ucfirst(mb_strtolower($matches[0])),
            $name
        );
    }
    
    static function logPageViewIntoCache(string $page)
    {
        if (!Auth::user()?->isAdmin() && !self::isCrawler()) {
            $cacheKey = "views.$page";
            if (!Cache::has($cacheKey)) Cache::forever($cacheKey, 0);
            Cache::increment($cacheKey);

            Cache::forever("viewed-at.$page", now()->format('Y-m-d H:i:s'));
        }
    }
    
    static function getMapUrl(float $latitude, float $longitude)
    {
        return url()->query("https://mapy.cz/", ['q' => "$latitude,$longitude"]);
    }
    
    static function getMapUrlPlace(string $place)
    {
        return url()->query("https://mapy.cz/", ['q' => $place]);
    }
    
}
