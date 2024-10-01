<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\DispositionRegister;
use App\Models\Disposition;
use App\Enums\Pitch;

class CouplersFilter extends DispositionFilter
{
    
    public readonly string $name;
    
    const
        TYPE_EQUAL = 1,
        TYPE_ALL = 2;
    
    public function __construct(
        protected Disposition $disposition,
        private int $type = self::TYPE_ALL
    )
    {
        $this->name = match ($type) {
            static::TYPE_EQUAL => __("Spojky (8')"),
            static::TYPE_ALL => __('Spojky (všechny)'),
        };
    }
    
    protected function filterRegisters(): Collection
    {
        $registers = $this->disposition->dispositionRegisters->filter(function (DispositionRegister $dispositionRegister) {
            if (!$dispositionRegister->coupler) return false;
            // nemá-li stopovou výšku, může to být jiné přídavné zařízení
            if (!$dispositionRegister->pitch) return false;
            
            return true;
        });
        
        if ($this->type === static::TYPE_EQUAL) {
            $registers = $this->getRegistersByPitch([Pitch::Pitch_8], $registers, withCouplers: true);
        }
        return $registers;
    }
    
}
