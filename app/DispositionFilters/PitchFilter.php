<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Enums\Pitch;
use App\Models\Disposition;

class PitchFilter extends DispositionFilter
{
    
    public readonly string $name;
    public readonly ?string $description;
    
    const
        TYPE_DEEP = 1,
        TYPE_ALIQUOT = 2;
    
    public function __construct(
        protected Disposition $disposition,
        private int $type
    )
    {
        $this->name = match ($type) {
            static::TYPE_DEEP => __("Hluboké (8' a nižsí)"),
            static::TYPE_ALIQUOT => __('Alikvotní'),
        };
        
        $this->description = match ($type) {
            static::TYPE_DEEP => __("Velké množství hlubokých rejstříků je typické pro romantické varhanářství"),
            static::TYPE_ALIQUOT => __('Rejstříky znějící v jiné než základní poloze - např. v kvintové, terciové atd.'),
        };
    }
    
    protected function filterRegisters(): Collection
    {
        switch ($this->type) {
            case static::TYPE_DEEP:
                $pitches = [Pitch::Pitch_32, Pitch::Pitch_16, Pitch::Pitch_8];
                break;
            case static::TYPE_ALIQUOT:
                $pitches = collect(Pitch::cases())->filter(
                    fn(Pitch $pitch) => $pitch->isAliquot()
                )->toArray();
                break;
            default:
                throw new \LogicException;
                
        }
        
        return $this->getRegistersByPitch($pitches);
    }
    
}
