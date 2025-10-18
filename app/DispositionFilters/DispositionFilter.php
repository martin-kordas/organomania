<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\Disposition;
use App\Models\DispositionRegister;
use App\Models\RegisterCategory as RegisterCategoryModel;
use App\Enums\RegisterCategory;

abstract class DispositionFilter
{
    
    public readonly string $name;
    public readonly ?string $description;
    
    protected $registers;
    
    public function __construct(
        protected Disposition $disposition
    )
    { }
    
    protected abstract function filterRegisters(): Collection;
    
    protected function getRegistersByCategory(array $categories, ?Collection $registers = null): Collection
    {
        $registers ??= $this->disposition->dispositionRegisters;
        
        return $registers->filter(function (DispositionRegister $dispositionRegister) use ($categories) {
            $register = $dispositionRegister->register;
            if (!$register) return false;
            
            if (in_array($register->registerCategory, $categories)) return true;
            if ($register->registerCategories->contains(
                fn(RegisterCategoryModel $categoryModel) => in_array($categoryModel->getEnum(), $categories)
            )) return true;
        });
    }
    
    protected function getRegistersByPitch(array $pitches, ?Collection $registers = null, $withCouplers = false): Collection
    {
        $registers ??= $this->disposition->dispositionRegisters;
        
        return $registers->filter(
            fn(DispositionRegister $dispositionRegister)
                => ($withCouplers || !$dispositionRegister->coupler)
                    && $dispositionRegister->pitch
                    && in_array($dispositionRegister->pitch, $pitches)
        );
    }
    
    protected function isMixedRegister(DispositionRegister $dispositionRegister)
    {
        return $dispositionRegister['multiplier'] && $dispositionRegister['multiplier'] !== '1';
    }
    
    public function getRegisters(): Collection
    {
        return $this->registers ??= $this->filterRegisters();
    }
    
    public function getRegisterCount(): int
    {
        return $this->getRegisters()->count();
    }
    
    private function getRealDispositionRegistersCount()
    {
        return $this->disposition->real_disposition_registers_count ?? $this->disposition->realDispositionRegisters->count();
    }
    
    public function getRatioToAllRealRegisters(): float
    {
        return
            $this->getRegisterCount()
            / $this->getRealDispositionRegistersCount();
    }
    
    public function getFormattedCount()
    {
        $count = $this->getRegisterCount();
        $str = "$count";
        if ($this->getRealDispositionRegistersCount() > 0) {
            $percent = round($this->getRatioToAllRealRegisters() * 100);
            if ($percent > 0) $str .= " <span class='fw-normal'>($percent&nbsp;%)</span>";
        }
        return $str;
    }
    
    protected function filterRegistersByOrFilters(array $filters)
    {
        $registers = collect();
        foreach ($filters as $filter) {
            $registers = $registers->merge(
                $filter->filterRegisters()
            );
        }
        return $registers->unique('id');
    }
    
}
