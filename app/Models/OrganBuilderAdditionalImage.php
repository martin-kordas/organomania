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

    const ID_PERUC = 375;

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

        $organBuilderName = null;
        if ($withOrganBuilder) {
            if ($this->organ_builder_name) $organBuilderName = $this->organ_builder_name;
            elseif ($this->organBuilder && $this->organBuilder->id !== OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED) $organBuilderName = $this->organBuilder->name;
            if ($organBuilderName) $title .= "\n{$organBuilderName}";
        }

        $details = [];
        if ($this->year_built) $details[] = $this->year_built;
        if (str($this->details)->contains('dochována skříň')) $details[] = __('dochována skříň');
        if (!empty($details)) {
            $title .= $organBuilderName ? ' ' : "\n";
            $title .= sprintf('(%s)', implode(', ', $details));
        }

        return $title;
    }

    public function toSearchableArray(): array
    {
        return [
            'organ_builder_additional_images.name' => '',
            'organ_builder_additional_images.organ_builder_name' => '',
            'organ_builders.last_name' => '',
            'organ_builders.workshop_name' => '',
        ];
    }

}
