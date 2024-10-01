<?php

namespace App\View\Components\Organomania;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Stars extends Component
{
    
    public function __construct(
        public ?int $count,
        public int $countAll = 5,
        public bool $showCount = false,
    ) {
        $this->count = min($this->count, $this->countAll);
    }

    public function shouldRender(): bool
    {
        return isset($this->count);
    }
    
    public function render(): View|Closure|string
    {
        return view('components.organomania.stars');
    }
}
