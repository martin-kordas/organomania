<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiturgicalCelebration extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function liturgicalDay()
    {
        return $this->belongsTo(LiturgicalDay::class);
    }
    
    public function olejnikPsalm()
    {
        return $this->belongsTo(OlejnikPsalm::class, 'psalm_olejnik', 'number');
    }
    
    public function shouldDisplayRank()
    {
        return !in_array($this->rank, ['neděle', 'ferie', 'Primární liturgické dny', 'Velikonoční triduum']);
    }
    
    public function getIcon()
    {
        return $this->color === 'white' ? 'circle' : 'circle-fill';
    }
    
    public function getIconColor()
    {
        return $this->color === 'white' ? 'initial' : $this->color;
    }
    
    public function getFullName()
    {
        $name = '';
        if ($this->shouldDisplayRank()) $name .= "{$this->rank} ";
        $name .= $this->name;
        return $name;
    }
    
    public function psalmOlejnikUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['psalm_olejnik'])) {
                    return "https://www.zaltar.cz/OL{$attributes['psalm_olejnik']}.html";
                }
            }
        );
    }
    
    
}
