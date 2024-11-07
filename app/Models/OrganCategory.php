<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\Organ;

class OrganCategory extends Category
{
    use HasFactory;
    
    protected function getEnumClass()
    {
        return \App\Enums\OrganCategory::class;
    }
    
    public function organs()
    {
        return $this->belongsToMany(Organ::class);
    }
    
    public function getItemsUrl(): string
    {
        return route('organs.index', ['filterCategories' => [$this->id]]);
    }
    
}
