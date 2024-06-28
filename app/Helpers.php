<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
    
}
