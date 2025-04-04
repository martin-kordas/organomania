<?php

namespace App\Models;

use App\Models\Organ;
use App\Models\CustomCategory;

class OrganCustomCategory extends CustomCategory
{
    
    public function organs()
    {
        return $this->belongsToMany(Organ::class);
    }
    
    public function getItemsUrl(): string
    {
        return route('organs.index', ['filterCategories' => ["custom-{$this->id}"]]);
    }
    
}
