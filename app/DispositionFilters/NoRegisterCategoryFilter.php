<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\Disposition;
use App\Models\DispositionRegister;
use App\Enums\RegisterCategory;

class NoRegisterCategoryFilter extends DispositionFilter
{
    
    public readonly string $name;
    
    public function __construct(
        protected Disposition $disposition
    )
    {
        $this->name = __('Vlastní nekategorizované');
    }
    
    protected function filterRegisters(): Collection
    {
        return $this->disposition->dispositionRegisters->filter(function (DispositionRegister $dispositionRegister) {
            return
                !$dispositionRegister->coupler
                && !isset($dispositionRegister->register?->registerCategory)
                && !$this->isMixedRegister($dispositionRegister);
        });
    }
    
}
