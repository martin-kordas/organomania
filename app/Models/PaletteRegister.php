<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\Pitch;

class PaletteRegister extends Model
{
    use HasFactory;
    
    protected function pitch(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $_value, array $attributes) => isset($attributes['pitch_id']) ? Pitch::from($attributes['pitch_id']) : null,
            set: fn (Pitch $pitch) => ['pitch_id' => $pitch->value],
        );
    }
    
    public function register()
    {
        return $this->belongsTo(Register::class);
    }
    
}
