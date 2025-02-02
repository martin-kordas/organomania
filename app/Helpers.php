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
    
    static function highlightEscapeText(string $haystack, string $needle)
    {
        return static::highlightText(
            htmlspecialchars($haystack, ENT_QUOTES),
            htmlspecialchars($needle, ENT_QUOTES)
        );
    }
    
    // TODO: zvýrazňuje jen celý $needle, ne jednotlivá slova v něm
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
    
    // lze řešit i Laravel funkcí trans_choice(), ale toto je asi pohodlnější
    static function declineCount($count, $text0, $text1, $text2)
    {
        return match (true) {
            $count <= 0 || $count >= 5 => $text0,
            $count === 1 => $text1,
            $count <= 4 => $text2,
        };
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
    
    static function formatRomanNumeral(int $num)
    {
        static $nf = new \NumberFormatter('@numbers=roman', \NumberFormatter::DECIMAL);
        return $nf->format($num);
    }
    
    static function formatDate(Carbon $date)
    {
        // Carbon zřejmě neumí automaticky změnit formát podle locale
        $format = app()->getLocale() === 'en' ? 'jS F Y' : 'j. F Y';
        return $date->translatedFormat($format);
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
    
    static function array2Csv(array $data)
    {
        $rowsStr = array_map(
            fn($row) => implode(',', $row),
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
        $query = http_build_query($params);
        return "$url?$query";
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
    
    static function logPageViewIntoCache(string $page)
    {
        if (!Auth::user()?->isAdmin() && !self::isCrawler()) {
            $cacheKey = "views.$page";
            if (!Cache::has($cacheKey)) Cache::forever($cacheKey, 0);
            Cache::increment($cacheKey);

            Cache::forever("viewed-at.$page", now()->format('Y-m-d H:i:s'));
        }
    }
    
}
