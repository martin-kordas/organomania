<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrganBuilderCategory;

class OrganBuilderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_workshop' => $this->is_workshop,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'place_of_birth' => $this->place_of_birth,
            'place_of_death' => $this->place_of_death,
            'active_period' => $this->active_period,
            'active_from_year' => $this->when($request->user()?->isAdmin(), $this->active_from_year),
            'municipality' => $this->municipality,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'region' => $this->region?->name,
            'importance' => $this->importance,
            'perex' => $this->perex,
            'description' => $this->description,
            
            'categories' => $this->organBuilderCategories->map(
                fn(OrganBuilderCategory $category) => __($category->getName())
            ),
            'custom_categories' => $this->when(
                $request->user()?->isAdmin(),
                $this->organBuilderCustomCategories->pluck('name')
            )
        ];
    }
}
