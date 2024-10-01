<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\DispositionRegister;
use App\Models\RegisterCategory as RegisterCategoryModel;
use App\Models\Disposition;
use App\Enums\RegisterCategory;

class TuttiFilter extends DispositionFilter
{
    
    public readonly string $name;
    public readonly ?string $description;
    
    public function __construct(
        protected Disposition $disposition,
    )
    {
        $this->name = 'Tutti';
        $this->description = __('Registrace zahrnující všechny rejstříky varhan');
    }
    
    protected function filterRegisters(): Collection
    {
        return $this->disposition->dispositionRegisters->filter(function (DispositionRegister $dispositionRegister) {
            if ($dispositionRegister->isTremulant()) return false;
            if ($dispositionRegister->register && $dispositionRegister->register->registerCategories->contains(
                fn(RegisterCategoryModel $category) => $category->getEnum() === RegisterCategory::Vychvevne
            )) return false;
            
            return true;
        });
    }
    
}
