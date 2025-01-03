<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Cviebrock\EloquentSluggable\Sluggable;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use App\Observers\OrganBuilderObserver;
use App\Models\OrganBuilderCustomCategory;
use App\Models\Region;
use App\Models\OrganBuilderCategory;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Models\Like;
use App\Enums\OrganBuilderCategory as OrganBuilderCategoryEnum;
use App\Traits\OwnedEntity;
use App\Traits\Viewable;

#[ObservedBy([OrganBuilderObserver::class])]
class OrganBuilder extends Model
{
    use HasFactory, SoftDeletes, Searchable, Sluggable;
    use OwnedEntity, Viewable;
    
    const
        ORGAN_BUILDER_ID_RIEGER_KLOSS = 2,
        ORGAN_BUILDER_ID_ORGANA = 52;
    
    protected $guarded = [];
    
    protected $attributes = [
        'is_workshop' => false,
    ];
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    public function casts()
    {
        return [
            'is_workshop' => 'boolean',
        ];
    }
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function timelineItems()
    {
        return $this->hasMany(OrganBuilderTimelineItem::class);
    }
    
    public function organs()
    {
        return $this->hasMany(Organ::class)->orderBy('year_built');
    }
    
    public function organRebuilds()
    {
        return $this->hasMany(OrganRebuild::class)->orderBy('year_built');
    }
    
    public function renovatedOrgans()
    {
        return $this->hasMany(Organ::class, 'renovation_organ_builder_id')->orderBy('year_renovated');
    }
    
    public function organBuilderCustomCategories()
    {
        return $this->belongsToMany(OrganBuilderCustomCategory::class)->withTimestamps()->orderBy('name');
    }
    
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    public function organBuilderCategories()
    {
        return $this->belongsToMany(OrganBuilderCategory::class)->withTimestamps()->orderBy('id');
    }
    
    public function getGeneralCategories()
    {
        return $this->organBuilderCategories->filter(
            fn(OrganBuilderCategory $category) => !$category->getEnum()->isPeriodCategory()
        );
    }
    
    public function shouldHideImportance()
    {
        return
            config('custom.hide_current_organ_builders_importance')
            && $this->isPublic()
            && $this->id !== static::ORGAN_BUILDER_ID_RIEGER_KLOSS
            && $this->id !== static::ORGAN_BUILDER_ID_ORGANA
            && $this->organBuilderCategories->contains(
                fn($category) => $category->id === OrganBuilderCategoryEnum::BuiltFrom1990->value
            );
    }
    
    public function scopeOrderByName(Builder $query, string $sortDirection = 'asc'): void
    {
        $raw = DB::raw('
            IF(
                organ_builders.is_workshop,
                organ_builders.workshop_name,
                CONCAT(organ_builders.last_name, organ_builders.first_name)
            )'
        );
        $query->orderBy($raw, $sortDirection);
    }
    
    public function scopeInland(Builder $query): void
    {
        $query->whereNotNull('region_id');
    }
    
    public function name(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if ($attributes['is_workshop']) return $attributes['workshop_name'];
                else {
                    $lastName = mb_strtoupper($attributes['last_name']);
                    return "$lastName, {$attributes['first_name']}";
                }
            }
        );
    }
    
    public function shortName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if ($attributes['is_workshop']) return $attributes['workshop_name'];
                else return $attributes['last_name'];
            }
        );
    }
    
    public function getThumbnailImage()
    {
        if (isset($this->image_url))
            return ['image_url' => $this->image_url, 'image_credits' => $this->image_credits];
    }
    
    #[SearchUsingFullText(['description', 'perex'])]
    public function toSearchableArray(): array
    {
        return $this->only([
            'workshop_name', 'first_name', 'last_name',
            'municipality', 'description', 'perex',
        ]);
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['private_prefix', 'workshop_name', 'first_name', 'last_name']
            ]
        ];
    }
    
    public function getMapInfo()
    {
        return view('components.organomania.map-info.organ-builder', [
            'organBuilder' => $this,
        ])->render();
    }
    
}
