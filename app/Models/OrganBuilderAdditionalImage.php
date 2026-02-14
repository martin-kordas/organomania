<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers;
use App\Enums\OrganCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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

    public function getViewUrl()
    {
        if (!$this->organ_builder_id) throw new \LogicException;

        $url = route('organs.cases', ['filterOrganBuilders' => [$this->organ_builder_id], 'additionalImageId' => $this->id]);
        return "$url#groups";
    }

    public function getMapMarkerTitle()
    {
        $title = $this->name;
        if (str($this->details)->contains('dochována skříň')) {
            $title .= sprintf("\n(%s)", __('dochována skříň'));
        }
        return $title;
    }

}
