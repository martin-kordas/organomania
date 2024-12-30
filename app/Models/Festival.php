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

class Festival extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    use Viewable;
    
    protected $fillable = ['organ_id'];
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organ()
    {
        return $this->belongsTo(Organ::class);
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
    
    protected function importance(): Attribute
    {
        return Attribute::make(
            get: fn ($importance) => match (true) {
                $importance >= 8 => 3,
                $importance >= 4 => 2,
                default => 1,
            }
        );
    }
    
    protected function firstUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, $festival) {
                if (isset($festival['url'])) {
                    $row = str($festival['url'])->explode("\n")->first();
                    $url = str($row)->explode('°')->first();
                    return trim($url);
                }
            }
        );
    }
    
    public function shouldHighlightFrequency()
    {
        return isset($this->starting_month) && $this->starting_month === (int)date('n');
    }
    
    public static function getHighlightedCount()
    {
        return static::query()->where('starting_month', date('n'))->count();
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
        return view('components.organomania.map-info.festival', [
            'festival' => $this,
        ])->render();
    }
    
}
