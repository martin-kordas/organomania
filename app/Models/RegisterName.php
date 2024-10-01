<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers;
use App\Enums\DispositionLanguage;

class RegisterName extends Model
{
    use HasFactory;
    
    public function register()
    {
        return $this->belongsTo(Register::class);
    }
    
    protected function language(): Attribute
    {
        return Helpers::makeEnumAttribute('language', DispositionLanguage::from(...));
    }
}
