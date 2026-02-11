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

    public function getFlagEmoji()
    {
        return match ($this) {
            self::Czech => '&#127464;&#127487;',
            self::German => '&#127465;&#127466;',
            self::French => '&#127467;&#127479;',
        };
    }

    public static function getDefault()
    {
        return app()->getLocale() === 'cs' ? self::Czech : self::German;
    }

}
