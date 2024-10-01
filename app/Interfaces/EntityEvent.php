<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface EntityEvent
{
    
    public function getModel(): Model;
    
    public function getAmountDiff(): int;
    
}
