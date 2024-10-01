<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\Disposition;
use App\Enums\RegisterCategory;

class RegisterCategoryFilter extends DispositionFilter
{
    
    public readonly string $name;
    public readonly ?string $description;
    
    public function __construct(
        protected Disposition $disposition,
        private RegisterCategory $category
    )
    {
        $this->name = $category->getName();
        $this->description = $category->getDescription();
    }
    
    protected function filterRegisters(): Collection
    {
        $registers = $this->getRegistersByCategory([$this->category]);
        
        // každý rejstřík s nastaveným multiplierem také považujeme za smíšený
        if ($this->category === RegisterCategory::Mixed) {
            foreach ($this->disposition->dispositionRegisters as $dispositionRegister) {
                if ($this->isMixedRegister($dispositionRegister) && !$registers->containsStrict($dispositionRegister)) {
                    $registers[] = $dispositionRegister;
                }
            }
        }
        
        return $registers;
    }
    
}
