<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Pitch as Enum;

class Pitch extends Model
{
    use HasFactory;
    
    public function getEnum()
    {
        return Enum::from($this->id);
    }
    
}
