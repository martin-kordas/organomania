<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Region;
use App\Models\Organ;

class CompetitionYear extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organs()
    {
        return $this->hasMany(Organ::class);
    }
    
    public function years()
    {
        return $this->hasMany(Organ::class);
    }
    
    public function getThumbnailImage()
    {
        if (isset($this->image_url))
            return ['image_url' => $this->image_url, 'image_credits' => $this->image_credits];
        if (isset($this->organ_image_url))
            return ['image_url' => $this->organ_image_url, 'image_credits' => $this->organ_image_credits];
        elseif ($this->organ) {
            if (isset($this->organ->outside_image_url))
                return ['image_url' => $this->organ->outside_image_url, 'image_credits' => $this->organ->outside_image_credits];
            elseif (isset($this->organ->image_url))
                return ['image_url' => $this->organ->image_url, 'image_credits' => $this->organ->image_credits];
        }
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
