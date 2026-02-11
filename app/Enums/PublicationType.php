<?php

namespace App\Enums;

enum PublicationType: int
{

    case Book = 1;
    case Article = 2;
    case Thesis = 3;

    public function getName(): string
    {
        return match ($this) {
            self::Book => __('Kniha'),
            self::Article => __('Článek'),
            self::Thesis => __('Závěrečná práce'),
            default => throw new \LogicException
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Book => 'bi-book',
            self::Article => 'bi-file-text',
            self::Thesis => 'bi-mortarboard',
            default => throw new \LogicException
        };
    }

}
