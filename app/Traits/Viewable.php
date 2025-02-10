<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Helpers;
use App\Models\User;

trait Viewable
{
    
    public function viewed()
    {
        $user = Auth::user();
        if (!$user?->isAdmin() && Auth::user()?->id !== User::USER_ID_MARTIN_KORDAS && !Helpers::isCrawler()) {
            if (!method_exists($this, 'isPublic') || $this->isPublic()) {
                $this->views++;
                $this->viewed_at = now();
                $this->save();
            }
        }
    }
    
}
