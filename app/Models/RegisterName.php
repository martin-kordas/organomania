<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers;
use App\Enums\DispositionLanguage;
use Cviebrock\EloquentSluggable\Sluggable;

class RegisterName extends Model
{
    use HasFactory, Sluggable;
    
    public function register()
    {
        return $this->belongsTo(Register::class);
    }
    
    protected function language(): Attribute
    {
        return Helpers::makeEnumAttribute('language', DispositionLanguage::from(...));
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
