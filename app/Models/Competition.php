<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Region;
use App\Models\Organ;
use App\Traits\Viewable;

class Competition extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    use Viewable;
    
    public function casts()
    {
        return [
            'inactive' => 'bool',
        ];
    }
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organs()
    {
        return $this->belongsToMany(Organ::class)->withTimestamps()->orderBy('municipality')->orderBy('place');
    }
    
    public function competitionYears()
    {
        return $this->hasMany(CompetitionYear::class)->orderBy('year', 'desc');
    }
    
    public function getThumbnailImage()
    {
        if (isset($this->image_url))
            return ['image_url' => $this->image_url, 'image_credits' => $this->image_credits];
        foreach ($this->organs as $organ) {
            if ($organ->outside_image_url)
                return ['image_url' => $organ->outside_image_url, 'image_credits' => $organ->outside_image_credits];
            if ($organ->image_url)
                return ['image_url' => $organ->image_url, 'image_credits' => $organ->image_credits];
        }
    }
    
    protected function firstUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $competition) {
                if (isset($competition['url'])) {
                    $row = str($competition['url'])->explode("\n")->first();
                    $url = str($row)->explode('°')->first();
                    return trim($url);
                }
            }
        );
    }
    
    public function shouldHighlightNextYear()
    {
        return isset($this->next_year) && $this->next_year >= (int)date('Y');
    }
    
    public static function getHighlightedCount()
    {
        return static::query()->where('next_year', '>=', date('Y'))->count();
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name']
            ]
        ];
    }
    
    public function getMapInfo()
    {
        return view('components.organomania.map-info.competition', [
            'competition' => $this,
        ])->render();
    }
    
}
