<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Helpers;

trait Viewable
{
    
    public function viewed()
    {
        if (!Auth::user()?->isAdmin() && !Helpers::isCrawler()) {
            if (!method_exists($this, 'isPublic') || $this->isPublic()) {
                $this->views++;
                $this->viewed_at = now();
                $this->save();
            }
        }
    }
    
}
