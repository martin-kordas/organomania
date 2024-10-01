<?php

namespace App;

use Transliterator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        return $date->translatedFormat('j. F Y');
    }
    
    static function array2Csv(array $data)
    {
        $rowsStr = array_map(
            fn($row) => implode(',', $row),
            $data
        );
        return implode("\n", $rowsStr);
    }
    
}
