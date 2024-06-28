<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use App\Models\Region;
use App\Models\OrganBuilder;
use App\Models\OrganCategory;
use App\Models\OrganLike;

class Organ extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }
    
    public function organCategories()
    {
        return $this->belongsToMany(OrganCategory::class);
    }
    
    public function organLikes()
    {
        return $this->hasMany(OrganLike::class);
    }
    
    #[SearchUsingFullText(['description'])]
    public function toSearchableArray(): array
    {
        return 
            $this->only(['place', 'municipality', 'description'])
            + ['organ_builders.last_name' => '', 'organ_builders.workshop_name' => ''];
    }
    
    protected static function booted(): void
    {
        static::addGlobalScope('authorized', function (Builder $builder) {
            $builder->whereNull('organs.user_id')->orWhere('organs.user_id', Auth::id());
        });
    }
    
}
