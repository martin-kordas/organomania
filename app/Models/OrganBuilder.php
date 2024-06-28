<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use App\Models\Region;
use App\Models\OrganBuilderCategory;

class OrganBuilder extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    
    protected $guarded = [];
    
    protected $attributes = [
        'is_workshop' => false,
    ];
    
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
    
    public function organBuilderCategories()
    {
        return $this->belongsToMany(OrganBuilderCategory::class);
    }
    
    public function getGeneralCategories()
    {
        return $this->organBuilderCategories->filter(
            fn(OrganBuilderCategory $category) => !$category->getEnum()->isPeriodCategory()
        );
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
    
    #[SearchUsingFullText(['description'])]
    public function toSearchableArray(): array
    {
        return $this->only([
            'workshop_name', 'first_name', 'last_name',
            'municipality', 'description',
        ]);
    }
    
    protected static function booted(): void
    {
        static::addGlobalScope('authorized', function (Builder $builder) {
            $builder->whereNull('organ_builders.user_id')->orWhere('organ_builders.user_id', Auth::id());
        });
    }
}
