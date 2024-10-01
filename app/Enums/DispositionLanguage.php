<?php

namespace App\Enums;

enum DispositionLanguage: string
{
    
    case Czech = 'cs';
    case German = 'de';
    case French = 'fr';
    
    public function getName()
    {
        return match ($this) {
            self::Czech => __('Čeština'),
            self::German => __('Němčina'),
            self::French => __('Francouzština'),
        };
    }
    
    public static function getDefault()
    {
        return app()->getLocale() === 'cs' ? self::Czech : self::German;
    }
    
}