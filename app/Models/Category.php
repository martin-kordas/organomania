<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class Category extends Model
{
    use HasFactory;
    
    abstract protected function getEnumClass();
    
    public function getEnum()
    {
        $class = $this->getEnumClass();
        return $class::from($this->id);
    }
    
    public function getName()
    {
        return $this->getEnum()->getName();
    }
    
}
