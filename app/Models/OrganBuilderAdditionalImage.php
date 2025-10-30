<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers;
use App\Enums\OrganCategory;

class OrganBuilderAdditionalImage extends Model
{
    use HasFactory, SoftDeletes;

    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }

    protected function caseOrganCategory(): Attribute
    {
        return Helpers::makeEnumAttribute('case_organ_category_id', OrganCategory::from(...));
    }
    
}
