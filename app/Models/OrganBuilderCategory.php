<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrganBuilder;
use App\Models\Category;

class OrganBuilderCategory extends Category
{
    use HasFactory;

    protected function getEnumClass()
    {
        return \App\Enums\OrganBuilderCategory::class;
    }
    
    public function organBuilders()
    {
        $relation = $this->belongsToMany(OrganBuilder::class);
        $relation->getQuery()->inland();
        return $relation;
    }
    
    public function getItemsUrl(): string
    {
        return route('organ-builders.index', ['filterCategories' => [$this->id]]);
    }
    
}
