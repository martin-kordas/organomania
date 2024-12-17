<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Observers\OrganObserver;
use App\Models\Region;
use App\Models\OrganBuilder;
use App\Models\OrganCategory;
use App\Models\OrganCustomCategory;
use App\Models\Like;
use App\Models\OrganRebuild;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\OwnedEntity;
use App\Helpers;

#[ObservedBy([OrganObserver::class])]
class Organ extends Model
{
    use HasFactory, SoftDeletes, Searchable, Sluggable;
    use OwnedEntity;
    
    protected $guarded = [];
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }
    
    public function renovationOrganBuilder()
    {
        return $this->belongsTo(OrganBuilder::class, 'renovation_organ_builder_id');
    }
    
    public function organCategories()
    {
        return $this->belongsToMany(OrganCategory::class)->withTimestamps()->orderBy('id');
    }
    
    public function organCustomCategories()
    {
        return $this->belongsToMany(OrganCustomCategory::class)->withTimestamps()->orderBy('name');
    }
    
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    public function organRebuilds()
    {
        return $this->hasMany(OrganRebuild::class)->orderBy('year_built');
    }
    
    public function festivals()
    {
        return $this->hasMany(Festival::class);
    }
    
    public function dispositions()
    {
        return $this->hasMany(Disposition::class)->orderBy('name');
    }
    
    public function getThumbnailImage()
    {
        if (isset($this->image_url))
            return ['image_url' => $this->image_url, 'image_credits' => $this->image_credits];
    }
    
    public function getDeclinedManuals()
    {
        return __(Helpers::declineCount($this->manuals_count, 'manuálů', 'manuál', 'manuály'));
    }
    
    public function getDeclinedStops()
    {
        if ($this->stops_count < 0) throw new \LogicException;
        
        return __(Helpers::declineCount($this->stops_count, 'rejstříků', 'rejstřík', 'rejstříky'));
    }
    
    public function getDeclinedManualsCount()
    {
        $manuals = $this->getDeclinedManuals();
        return "{$this->manuals_count} $manuals";
    }
    
    public function getDeclinedStopsCount()
    {
        $stops = $this->getDeclinedStops();
        return "{$this->stops_count} $stops";
    }
    
    #[SearchUsingFullText(['description', 'perex'])]
    public function toSearchableArray(): array
    {
        return 
            $this->only(['place', 'municipality', 'description', 'perex'])
            + ['organ_builders.last_name' => '', 'organ_builders.workshop_name' => ''];
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['private_prefix', 'municipality', 'place']
            ]
        ];
    }
    
    public function getMapInfo(?float $nearLatitude = null, ?float $nearLongitude = null)
    {
        if (
            isset($nearLatitude, $nearLongitude)
            && !($nearLatitude === $this->latitude && $nearLongitude === $this->longitude)
        ) {
            $distance = Helpers::getDistance($nearLatitude, $nearLongitude, $this->latitude, $this->longitude);
        }
        else $distance = null;
        
        return view('components.organomania.map-info.organ', [
            'organ' => $this,
            'distance' => $distance
        ])->render();
    }
    
}
