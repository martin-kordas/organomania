<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\User;

trait Viewable
{
    
    public function viewed()
    {
        // TODO: částečně duplicitní kód s Helpers::logPageViewIntoCache()
        $user = Auth::user();
        if (!$user?->isAdmin() && $user?->id !== User::USER_ID_MARTIN_KORDAS && !Helpers::isCrawler()) {
            if (!method_exists($this, 'isPublic') || $this->isPublic()) {
                $this->views++;
                $this->viewed_at = now();
                
                // odfiltrovat zkreslující přístupy z Číny
                if ($this instanceof Organ || $this instanceof OrganBuilder) {
                    $acceptLanguage = request()->server('HTTP_ACCEPT_LANGUAGE');
                    if (!str($acceptLanguage)->startsWith('zh')) {
                        $this->views_filtered++;
                        $this->viewed_at_filtered = now();
                    }
                }
                
                $this->save();
            }
        }
    }
    
}
