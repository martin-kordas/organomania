<?php

namespace App\Enums;

use App\Enums\DispositionLanguage;

enum Pitch: int
{
    
    case Pitch_32 = 1;
    case Pitch_16 = 2;
    case Pitch_8 = 3;
    case Pitch_4 = 4;
    case Pitch_2 = 5;
    case Pitch_1 = 6;
    case Pitch_1_2 = 7;
    
    case Pitch_2_And_2_3 = 501;
    case Pitch_1_And_1_3 = 502;
    case Pitch_10_And_2_3 = 503;
    case Pitch_5_And_1_3 = 504;
    case Pitch_2_3 = 505;
    
    case Pitch_1_And_3_5 = 301;
    case Pitch_3_And_1_5 = 302;
    case Pitch_16_19 = 303;
    case Pitch_4_5 = 304;
    case Pitch_8_19 = 305;
    
    case Pitch_2_And_2_7 = 701;
    case Pitch_1_And_1_7 = 702;
    case Pitch_8_15 = 703;
    
    case Pitch_1_And_7_9 = 201;
    case Pitch_8_9 = 202;
    
    case Pitch_1_And_5_11 = 401;
    case Pitch_8_11 = 402;
    
    case Pitch_8_13 = 601;
    
    public function isAliquot()
    {
        return $this->value > 100;
    }
    
    public function getInterval()
    {
        if ($this === self::Pitch_8) return 'základní';
        
        return match (intdiv($this->value, 100)) {
            2 => 'sekundová',
            3 => 'terciová',
            4 => 'kvartová',
            5 => 'kvintová',
            6 => 'sextová',
            7 => 'septimová',
            default => 'oktávová',
        };
    }
    
    public static function tryFromLabel(string $label, DispositionLanguage $language): ?static
    {
        foreach (static::cases() as $pitch) {
            if ($pitch->getLabel($language) === $label) {
                return $pitch;
            }
        }
        return null;
    }
    
    public static function getPitchGroups()
    {
        $groups = [];
        foreach (static::cases() as $pitch) {
            $group = __(str($pitch->getInterval())->ucfirst()->toString());
            $groups[$group] ??= [];
            $groups[$group][] = $pitch;
        }
        return $groups;
    }
    
    public function getLabel(DispositionLanguage $language, $html = false)
    {
        $label = match ($language) {
            DispositionLanguage::Czech,
            DispositionLanguage::German,
            DispositionLanguage::French => match ($this) {
                self::Pitch_32 => "32'",
                self::Pitch_16 => "16'",
                self::Pitch_8 => "8'",
                self::Pitch_4 => "4'",
                self::Pitch_2 => "2'",
                self::Pitch_1 => "1'",
                self::Pitch_1_2 => "1/2'",

                self::Pitch_1_And_3_5 => "1 3/5'",
                self::Pitch_3_And_1_5 => "3 1/5'",
                self::Pitch_16_19 => "16/19'",
                self::Pitch_4_5 => "4/5'",
                self::Pitch_8_19 => "8/19'",
                
                self::Pitch_2_And_2_3 => "2 2/3'",
                self::Pitch_1_And_1_3 => "1 1/3'",
                self::Pitch_10_And_2_3 => "10 2/3'",
                self::Pitch_5_And_1_3 => "5 1/3'",
                self::Pitch_2_3 => "2/3'",
                
                self::Pitch_2_And_2_7 => "2 2/7'",
                self::Pitch_1_And_1_7 => "1 1/7'",
                self::Pitch_8_15 => "8/15'",
                
                self::Pitch_1_And_7_9 => "1 7/9'",
                self::Pitch_8_9 => "8/9'",
                
                self::Pitch_1_And_5_11 => "1 5/11'",
                self::Pitch_8_11 => "8/11'",
                
                self::Pitch_8_13 => "8/13'",

                default => throw new \LogicException
            },
            default => throw new \RuntimeException
        };
        if ($html) $label = str($label)->replace(' ', '&nbsp;');
        return $label;
    }
    
    public function getAliquoteTone()
    {
        return match ($this) {
            self::Pitch_32 => "C0",
            self::Pitch_16 => "c0",
            self::Pitch_8 => "c1",
            self::Pitch_4 => "c2",
            self::Pitch_2 => "c3",
            self::Pitch_1 => "c4",
            self::Pitch_1_2 => "c5",

            self::Pitch_1_And_3_5 => "e3",
            self::Pitch_3_And_1_5 => "e2",
            self::Pitch_16_19 => "es4",
            self::Pitch_4_5 => "e4",
            self::Pitch_8_19 => "es5",

            self::Pitch_2_And_2_3 => "g2",
            self::Pitch_1_And_1_3 => "g3",
            self::Pitch_10_And_2_3 => "g0",
            self::Pitch_5_And_1_3 => "g1",
            self::Pitch_2_3 => "g4",

            self::Pitch_2_And_2_7 => "b2",
            self::Pitch_1_And_1_7 => "b3",
            self::Pitch_8_15 => "h4",

            self::Pitch_1_And_7_9 => "d3",
            self::Pitch_8_9 => "d4",

            self::Pitch_1_And_5_11 => "f3",
            self::Pitch_8_11 => "f4",

            self::Pitch_8_13 => "a4",

            default => throw new \LogicException
        };
    }
    
    public function getAliquoteToneFormatted()
    {
        $tone = $this->getAliquoteTone();
        $tone = preg_replace('/([0-9])/', '<sup>$1</sup>', $tone);
        return $tone;
    }
    
    public function getAliquoteOrder()
    {
        $order = array_search($this, [
            self::Pitch_32,
            self::Pitch_16,
            self::Pitch_10_And_2_3,
            self::Pitch_8,
            self::Pitch_5_And_1_3,
            self::Pitch_4,
            self::Pitch_3_And_1_5,
            self::Pitch_2_And_2_3,
            self::Pitch_2_And_2_7,
            self::Pitch_2,
            self::Pitch_1_And_7_9,
            self::Pitch_1_And_3_5,
            self::Pitch_1_And_5_11,
            self::Pitch_1_And_1_3,
            self::Pitch_1_And_1_7,
            self::Pitch_1,
            self::Pitch_8_9,
            self::Pitch_16_19,
            self::Pitch_4_5,
            self::Pitch_8_11,
            self::Pitch_2_3,
            self::Pitch_8_13,
            self::Pitch_8_15,
            self::Pitch_1_2,
            self::Pitch_8_19,
        ]);
        if ($order === false) throw new \LogicException;
        return $order;
    }
    
}