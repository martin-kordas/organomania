<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrganBuilderResource;
use App\Models\OrganRebuild;
use App\Models\OrganCategory as OrganCategoryModel;

class OrganResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'place' => $this->place,
            'municipality' => $this->municipality,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'region' => $this->region?->name,
            'year_built' => $this->year_built,
            'stops_count' => $this->stops_count,
            'manuals_count' => $this->manuals_count,
            'image_url' => $this->when($request->user()?->isAdmin(), $this->image_url),
            'perex' => $this->perex,
            'description' => $this->description,
            
            'organ_builder' => $this->organBuilder ? new OrganBuilderResource($this->organBuilder) : null,
            'rebuilds' => $this->organRebuilds
                ->filter(
                    fn(OrganRebuild $rebuild) => isset($rebuild->organBuilder)
                )
                ->map(
                    fn(OrganRebuild $rebuild) => [
                        'organ_builder' => $rebuild->organBuilder->name,
                        'year_built' => $rebuild->year_built,
                    ]
                ),
            'categories' => $this->organCategories->map(
                fn(OrganCategoryModel $category) => __($category->getName())
            ),
            'custom_categories' => $this->when(
                $request->user()?->isAdmin(),
                $this->organCustomCategories->pluck('name')
            )
        ];
    }
}
