<?php

namespace App\Models;

use App\Models\Category;

class OrganCategory extends Category
{
    
    protected function getEnumClass()
    {
        return \App\Enums\OrganCategory::class;
    }
    
}
