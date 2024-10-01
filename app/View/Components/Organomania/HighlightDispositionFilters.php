<?php

namespace App\View\Components\Organomania;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Disposition;
use App\Enums\RegisterCategory;
use App\DispositionFilters\DispositionFilter;
use App\DispositionFilters\RegisterCategoryFilter;
use App\DispositionFilters\PlenoFilter;
use App\DispositionFilters\PitchFilter;

class HighlightDispositionFilters extends Component
{
    
    public $filters;
    
    public function __construct(
        private Disposition $disposition
    ) {
        $this->statGroups = $this->getFilters();
    }

    private function getFilters()
    {
        $filters = [];
        foreach (RegisterCategory::getMainCategories() as $category) {
            $filters[] = new RegisterCategoryFilter($this->disposition, $category);
        }
        $filters[] = new PlenoFilter($this->disposition);
        $filters[] = new PitchFilter($this->disposition, PitchFilter::TYPE_DEEP);
        $filters[] = new PitchFilter($this->disposition, PitchFilter::TYPE_ALIQUOT);

        $filters = array_filter(
            $filters,
            fn(DispositionFilter $filter) => $filter->getRegisterCount() > 0
        );

        return $filters;
    }
    
    public function render(): View|Closure|string
    {
        return view('components.organomania.highlight-disposition-filters');
    }
}
