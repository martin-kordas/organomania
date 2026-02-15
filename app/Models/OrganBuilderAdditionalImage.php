<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers;
use App\Enums\OrganCategory;
use App\Models\OrganBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;

class OrganBuilderAdditionalImage extends Model
{
    use HasFactory, SoftDeletes, Searchable;

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
        $url = route('organs.cases', ['filterOrganBuilders' => [$this->organ_builder_id ?? -1], 'additionalImageId' => $this->id]);
        return "$url#groups";
    }

    public function getMapMarkerTitle(bool $withOrganBuilder = false)
    {
        $title = $this->name;
        if ($withOrganBuilder && $this->organBuilder && $this->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) {
            $title .= "\n{$this->organBuilder->name}";
            if ($this->year_built) $title .= " ({$this->year_built})";
        }
        if (str($this->details)->contains('dochována skříň')) {
            $title .= sprintf("\n(%s)", __('dochována skříň'));
        }
        return $title;
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => '',
            'organ_builder_name' => ''
        ];
    }

}
