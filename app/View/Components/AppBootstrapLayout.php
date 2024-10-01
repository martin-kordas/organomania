<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Services\RuntimeStatsService;

class AppBootstrapLayout extends Component
{
    
    public function __construct(public RuntimeStatsService $runtimeStats)
    {
        
    }
    
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app-bootstrap');
    }
}
