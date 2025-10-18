<?php

namespace App\Traits;

use Livewire\Attributes\Computed;
use App\Enums\RegisterCategory;
use App\DispositionFilters\AliquotFilter;
use App\DispositionFilters\RegisterCategoryFilter;
use App\DispositionFilters\PlenoFilter;
use App\DispositionFilters\PitchFilter;
use App\DispositionFilters\DispositionFilter;
use App\Models\Disposition;

trait HasHighlightDispositionFilters
{
    
    public int $highlightFilterIndex = -1;
    
    private abstract function getDispositionForHighlight(): Disposition;
    
    #[Computed]
    public function highlightDispositionFilters()
    {
        $disposition = $this->getDispositionForHighlight();
        
        $filters = [];
        foreach (RegisterCategory::getMainCategories() as $category) {
            $filters[] = new RegisterCategoryFilter($disposition, $category);
        }
        $filters[] = new PlenoFilter($disposition);
        $filters[] = new PitchFilter($disposition, PitchFilter::TYPE_DEEP);
        $filters[] = new AliquotFilter($disposition);

        $filters = array_filter(
            $filters,
            fn(DispositionFilter $filter) => $filter->getRegisterCount() > 0
        );

        return $filters;
    }
    
    #[Computed]
    public function highlightedDispositionRegisters()
    {
        if ($this->highlightFilterIndex !== -1) {
            $filter = $this->highlightDispositionFilters[$this->highlightFilterIndex] ?? throw new \RuntimeException;
            return $filter->getRegisters();
        }
        return collect();
    }
    
}
