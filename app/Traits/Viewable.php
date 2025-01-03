<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Helpers;

trait Viewable
{
    
    public function viewed()
    {
        if (!Auth::user()?->isAdmin() && !Helpers::isCrawler()) {
            $this->views++;
            $this->viewed_at = now();
            $this->save();
        }
    }
    
}
