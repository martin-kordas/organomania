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

#[ObservedBy([OrganBuilderObserver::class])]
class OrganBuilder extends Model
{
    use HasFactory, SoftDeletes, Searchable, Sluggable;
    use OwnedEntity;
    
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
    
    public function organs()
    {
        return $this->hasMany(Organ::class)->orderBy('year_built');
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
    
}
