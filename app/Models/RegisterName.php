<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Scout\Searchable;
use App\Helpers;
use App\Enums\DispositionLanguage;
use Cviebrock\EloquentSluggable\Sluggable;

class RegisterName extends Model
{
    use HasFactory, Searchable, Sluggable;
    
    public function register()
    {
        return $this->belongsTo(Register::class);
    }
    
    protected function language(): Attribute
    {
        return Helpers::makeEnumAttribute('language', DispositionLanguage::from(...));
    }
    
    public function toSearchableArray(): array
    {
        return $this->only(['name']);
    }
    
    // umožňuje zohlednit, že stejné názvy rejstříků v různých jazycích (které jsou ale skryté) vypadají opticky stejně
    public function getVisualIdentifier()
    {
        $language = $this->hide_language ? '' : $this->language->value;
        return "{$this->name}_$language";
    }
    
    public function isVisuallySameAs(self $registerName)
    {
        return $registerName->getVisualIdentifier() === $this->getVisualIdentifier();
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name']
            ]
        ];
    }
}
