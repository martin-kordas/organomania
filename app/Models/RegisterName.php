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
    
    protected $guarded = [];
    
    const
        REGISTER_NAME_ID_FLETNA_HARMONICKA = 5,
        REGISTER_NAME_ID_KLARINET = 232,
        REGISTER_NAME_ID_HARMONIA_AETHEREA = 268,
        REGISTER_NAME_ID_VOX_COELESTIS = 38;
    
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
    
    public function getRelatedRegisterName(Register $register)
    {
        // pro související rejstřík použít RegisterName ve stejném jazyce jako aktuální RegisterName, pokud existuje
        $registerNames = $register->registerNames->filter(
            fn (RegisterName $registerName) => $registerName->language === $this->language
        );
        if ($registerNames->isEmpty()) $registerNames = $register->registerNames;
        
        // upřednostnit RegisterName s nejnižším ID, protože to je nejobvyklejší tvar (např. 'Copula minor' namísto 'Copl minor')
        $registerNames = $registerNames->sortBy('id');
        
        return $registerNames->first();
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
