<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Region;
use App\Models\Organ;
use App\Traits\HasLinkComponent;
use App\Traits\Viewable;

class Festival extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    use HasLinkComponent;
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
        if (!isset($this->starting_month) || !isset($this->ending_month))
            return false;
        
        $month = now()->month;
        if ($this->starting_month > $this->ending_month) {  // např. od října (10) do ledna (1)
            return $this->starting_month <= $month || $this->ending_month >= $month;
        }
        else return $this->starting_month <= $month && $this->ending_month >= $month;
    }
    
    public static function getHighlightedCount()
    {
        $month = now()->month;
        return static::query()
            ->whereRaw('
                IF(
                    starting_month > ending_month,
                    starting_month <= ? OR ending_month >= ?,
                    starting_month <= ? AND ending_month >= ?
                )
            ', [$month, $month, $month, $month])
            ->count();
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
    
    public function getLinkComponent()
    {
        return 'components.organomania.festival-link';
    }
    
}
