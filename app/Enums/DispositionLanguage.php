<?php

namespace App\Enums;

enum DispositionLanguage: string
{

    case Czech = 'cs';
    case German = 'de';
    case French = 'fr';
    case English = 'en';

    public function getName()
    {
        return match ($this) {
            self::Czech => __('Čeština'),
            self::German => __('Němčina'),
            self::French => __('Francouzština'),
            self::English => __('Angličtina'),
        };
    }

    public function getFlagEmoji()
    {
        return match ($this) {
            self::Czech => '&#127464;&#127487;',
            self::German => '&#127465;&#127466;',
            self::French => '&#127467;&#127479;',
            self::English => '&#127468;&#127463;',
        };
    }

    public static function getDefault()
    {
        return app()->getLocale() === 'cs' ? self::Czech : self::German;
    }

}
