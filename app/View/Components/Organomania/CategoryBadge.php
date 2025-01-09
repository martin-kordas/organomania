<?php

namespace App\View\Components\Organomania;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Interfaces\Category;

class CategoryBadge extends Component
{
    public $showTooltip;
    
    public function __construct(public Category $category, public bool $newTab = false)
    {
        $description = $this->category->getDescription();
        $this->showTooltip = $description !== null && $description !== '';
    }

    public function render(): View|Closure|string
    {
        return view('components.organomania.category-badge');
    }
}
