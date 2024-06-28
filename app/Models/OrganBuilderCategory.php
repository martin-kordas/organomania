<?php

namespace App\Models;

use App\Models\Category;

class OrganBuilderCategory extends Category
{

    protected function getEnumClass()
    {
        return \App\Enums\OrganBuilderCategory::class;
    }
    
}
