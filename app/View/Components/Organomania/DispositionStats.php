<?php

namespace App\View\Components\Organomania;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Disposition;
use App\Enums\RegisterCategory;
use App\DispositionFilters\RegisterCategoryFilter;
use App\DispositionFilters\NoRegisterCategoryFilter;
use App\DispositionFilters\PitchFilter;

class DispositionStats extends Component
{
    
    public $statGroups;
    
    public function __construct(
        private Disposition $disposition
    ) {
        $this->statGroups = $this->getStatGroups();
    }

    private function getStatGroups()
    {
        $groups = [];

        $groups[0] = [];
        $groups[0][__('Znějících rejstříků')]
            = $this->disposition->real_disposition_registers_count
            ?? $this->disposition->realDispositionRegisters->count();

        if ($minRegister = $this->disposition->getMinPitchRegister()) {
            $groups[0][__('Nejhlubší poloha')] = $minRegister->pitch->getLabel($this->disposition->language);
        }
        if ($maxRegister = $this->disposition->getMaxPitchRegister()) {
            $groups[0][__('Nejvyšší poloha')] = $maxRegister->pitch->getLabel($this->disposition->language);
        }
        foreach ([PitchFilter::TYPE_DEEP, PitchFilter::TYPE_ALIQUOT] as $type) {
            $filter = new PitchFilter($this->disposition, $type);
            $groups[0][$filter->name] = $filter->getFormattedCount();
        }

        $groups[1] = [];
        foreach (RegisterCategory::getMainCategories() as $category) {
            $filter = new RegisterCategoryFilter($this->disposition, $category);
            $groups[1][$filter->name] = $filter->getFormattedCount();
        }
        $filter = new NoRegisterCategoryFilter($this->disposition);
        $count = $filter->getRegisterCount();
        if ($count > 0) $groups[1][$filter->name] = $count;
        
        return $groups;
    }
    
    public function render(): View|Closure|string
    {
        return view('components.organomania.disposition-stats');
    }
}
