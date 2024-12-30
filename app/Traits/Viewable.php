<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Viewable
{
    
    public function viewed()
    {
        if (!Auth::user()?->admin) {
            $this->views++;
            $this->save();
        }
    }
    
}
