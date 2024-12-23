<?php

namespace App\Models;

use App\Models\OrganBuilder;
use App\Models\CustomCategory;

class OrganBuilderCustomCategory extends CustomCategory
{
    
    public function organBuilders()
    {
        $relation = $this->belongsToMany(OrganBuilder::class);
        $relation->getQuery()->inland();
        return $relation;
    }
    
    public function getItemsUrl(): string
    {
        return route('organ-builders.index', ['filterCategories' => ["custom-{$this->id}"]]);
    }
    
}
