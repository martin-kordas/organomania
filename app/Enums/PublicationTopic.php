<?php

namespace App\Enums;

enum PublicationTopic: int
{

    case Organ = 1;
    case OrganBuilder = 2;
    case Area = 3;
    case Other = 4;

    public function getName(): string
    {
        return match ($this) {
            self::Organ => __('Varhany'),
            self::OrganBuilder => __('Varhanář'),
            self::Area => __('Lokalita'),
            self::Other => __('Jiné'),
            default => throw new \LogicException
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Organ => __('Publikace zabývající se určitými varhanami'),
            self::OrganBuilder => __('Publikace zabývající se určitým varhanářem'),
            self::Area => __('Publikace zabývající se varhanami a varhanáři na určitém území'),
            self::Other => __('Publikace jiného zaměření'),
            default => throw new \LogicException
        };
    }

}
