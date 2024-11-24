<?php

namespace App\Http\Controllers;

use App\Repositories\OrganRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    
    public function __invoke(OrganRepository $organRepository)
    {
        $organOfDay = Cache::remember(
            'welcome.organOfDay',
            new Carbon('tomorrow'),
            fn() => $organRepository->getOrganOfDay()
        );
        
        return view('welcome', compact('organOfDay'));
    }
    
}
