<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Enums\Pitch;
use App\Enums\RegisterCategory;
use App\Models\Disposition;

class AliquotFilter extends DispositionFilter
{
    public readonly string $name;
    public readonly ?string $description;
    
    public function __construct(
        protected Disposition $disposition,
    )
    {
        $this->name = 'Alikvotní';
        $this->description = __('Rejstříky znějící v jiné než základní poloze - např. v kvintové, terciové atd.');
    }
    
    protected function filterRegisters(): Collection
    {
        $pitchFilter = new PitchFilter($this->disposition, PitchFilter::TYPE_ALIQUOT);
        
        // např. mixtury - nejsou označeny alikvotní polohou, ale patří do alikvotních, což lze rozeznat na základě kategorie
        $categoryFilter = new RegisterCategoryFilter($this->disposition, RegisterCategory::Alikvotni);
        
        return $this->filterRegistersByOrFilters([$pitchFilter, $categoryFilter]);
    }
    
}
