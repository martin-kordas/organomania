<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\KeyboardObserver;
use App\Enums\DispositionLanguage;
use App\Helpers;

#[ObservedBy([KeyboardObserver::class])]
class Keyboard extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    
    public function dispositionRegisters()
    {
        return $this->hasMany(DispositionRegister::class)->orderBy('order');
    }
    
    public function realDispositionRegisters()
    {
        return $this->dispositionRegisters()->where('coupler', 0);
    }
    
    public static function getProposedManualNames(DispositionLanguage $language)
    {
        $names = match ($language) {
            DispositionLanguage::Czech => [
                'Hlavní stroj', 'Pozitiv', 'Žaluziový stroj',
            ],
            DispositionLanguage::German => [
                'Hauptwerk', 'Positiv', 'Oberwerk', 'Schwellwerk',
            ],
            DispositionLanguage::French => [
                'Grand Orgue', 'Positif', 'Récit',
            ]
        };
        return $names;
    }
    
    public static function getDefaultName(DispositionLanguage $language, $pedal = false)
    {
        return match ($language) {
            DispositionLanguage::Czech => $pedal ? 'Pedál' : 'manuál',
            DispositionLanguage::German => $pedal ? 'Pedal' : 'manual',
            DispositionLanguage::French => $pedal ? 'Pédale' : 'manuel',
        };
    }
    
    public function getAbbrev()
    {
        if ($this->pedal) return 'P';
        return $this->getNumber();
    }
    
    public function getNumber()
    {
        if ($this->pedal) return null;
        return Helpers::formatRomanNumeral($this->order);
    }
    
    public function getFullName()
    {
        if ($this->pedal) return $this->name;
        $number = Helpers::formatRomanNumeral($this->order);
        return "$number. {$this->name}";
    }
    
}
