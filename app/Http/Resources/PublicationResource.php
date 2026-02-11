<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'authors' => $this->authors->pluck('full_name'),
            'place_of_publication' => $this->place_of_publication,
            'year' => $this->year,
            'language' => $this->language->getName(),
            'type' => $this->publication_type->getName(),
            'topic' => $this->publication_topic->getName(),
            'journal' => $this->journal,
            'journal_issue' => $this->journal_issue,
            'thesis_description' => $this->thesis_description,
            'citation' => $this->citation,
            'url' => $this->url,
            'library_url' => $this->library_url,
        ];
    }
}
