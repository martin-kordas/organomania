<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RegisterCategory as Enum;

class RegisterCategory extends Model
{
    use HasFactory;
    
    public function getEnum()
    {
        return Enum::from($this->id);
    }
    
}
