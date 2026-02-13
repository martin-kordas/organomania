<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers;
use App\Enums\Pitch;
use App\Enums\DispositionLanguage;

class DispositionRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    private const TREMULANT_NAMES = [
        DispositionLanguage::Czech->value => ['Tremolo'],
        DispositionLanguage::German->value => ['Tremulant'],
        DispositionLanguage::French->value => ['Tremblant'],
        DispositionLanguage::English->value => ['Tremolo'],   // ??
    ];

    public function registerName()
    {
        return $this->belongsTo(RegisterName::class);
    }

    public function keyboard()
    {
        return $this->belongsTo(Keyboard::class);
    }

    public function register()
    {
        return $this->hasOneThrough(Register::class, RegisterName::class, 'id', 'id', 'register_name_id', 'register_id');
    }

    protected function realName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->registerName?->name ?? $this->name
        );
    }

    protected function pitch(): Attribute
    {
        return Helpers::makeEnumAttribute('pitch_id', Pitch::from(...));
    }

    public static function formatMultiplier($multiplier)
    {
        return str($multiplier)
            ->replaceMatches('/[ ]+-[ ]+/', '–')
            ->append('×');
    }

    public static function getTremulantName(DispositionLanguage $language)
    {
        $names = static::TREMULANT_NAMES[$language->value] ?? throw new \LogicException;
        return reset($names);
    }

    public function isTremulant()
    {
        return
            $this->coupler
            && collect(static::TREMULANT_NAMES)->flatten()->contains(function ($tremulantName) {
                return str($this->name)->startsWith($tremulantName);
            });
    }

}
