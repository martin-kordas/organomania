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
        ORGAN_BUILDER_ID_RIEGER = 1,
        ORGAN_BUILDER_ID_RIEGER_KLOSS = 2,
        ORGAN_BUILDER_ID_EMANUEL_STEPAN_PETR = 5,
        ORGAN_BUILDER_ID_BRATRI_PASTIKOVE = 53,
        ORGAN_BUILDER_ID_JINDRICH_SCHIFFNER = 67,
        ORGAN_BUILDER_ID_JAN_TUCEK = 7,
        ORGAN_BUILDER_ID_JOSEF_MELZER = 47,
        ORGAN_BUILDER_ID_BRATRI_BRAUNEROVE = 27,
        ORGAN_BUILDER_ID_NEUSSEROVE = 50,
        ORGAN_BUILDER_ID_STEINMEYER = 19,
        ORGAN_BUILDER_ID_A_SCHUSTER_UND_SOHN = 22,
        ORGAN_BUILDER_ID_JEHMLICH = 14,
        ORGAN_BUILDER_ID_MARTIN_ZAUS = 78,
        ORGAN_BUILDER_ID_MAREK_VORLICEK = 89,
        ORGAN_BUILDER_ID_DALIBOR_MICHEK = 48,
        ORGAN_BUILDER_ID_DLABAL_METTLER = 31,
        ORGAN_BUILDER_ID_STARKOVE = 8,
        ORGAN_BUILDER_ID_HEINRICH_MUNDT = 49,
        ORGAN_BUILDER_ID_TOMAS_SCHWARZ = 68,
        ORGAN_BUILDER_ID_TAUCHMANNOVE = 76,
        ORGAN_BUILDER_ID_GARTNEROVE = 34,
        ORGAN_BUILDER_ID_GUTHOVE = 36,
        ORGAN_BUILDER_ID_JAN_DAVID_SIEBER = 4,
        ORGAN_BUILDER_ID_JAN_VYMOLA = 3,
        ORGAN_BUILDER_ID_MICHAEL_ENGLER = 9,
        ORGAN_BUILDER_ID_STAUDINGEROVE = 72,
        ORGAN_BUILDER_ID_FRANTISEK_SVITIL = 6,
        ORGAN_BUILDER_ID_FRANZ_HARBICH = 37,
        ORGAN_BUILDER_ID_JOSEF_PREDIGER = 55,
        ORGAN_BUILDER_ID_KANSKY_BRACHTL = 43,
        ORGAN_BUILDER_ID_VLADIMIR_SLAJCH = 73,
        ORGAN_BUILDER_ID_KRALICKA_DILNA = 46,
        ORGAN_BUILDER_ID_LEOPOLD_SPIEGEL = 71,
        ORGAN_BUILDER_ID_LEOPOLD_BURKHARDT = 28,
        ORGAN_BUILDER_ID_BEDRICH_SEMRAD = 65,
        ORGAN_BUILDER_ID_PAVEL_FRANTISEK_HORAK = 38,
        ORGAN_BUILDER_ID_HORCICKOVE = 39,
        ORGAN_BUILDER_ID_CASPARIDOVE = 29,
        ORGAN_BUILDER_ID_JOSEF_SILBERBAUER = 69,
        ORGAN_BUILDER_ID_JIRI_SPANEL = 74,
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
        return $this
            ->belongsToMany(OrganBuilderCategory::class)
            ->withTimestamps()
            // HACK: řeší dodatečné přidání štítku
            ->orderByRaw('IF(id = 10, 2.5, id)');
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
                if (isset($attributes['short_name'])) return $attributes['short_name'];
                elseif ($attributes['is_workshop']) return $attributes['workshop_name'];
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
        return
            $this->only([
                'workshop_name', 'first_name', 'last_name',
                'municipality', 'description', 'perex',
            ])
            + [
                // HACK: díky tomuto se description hledá i ne-fulltextově (výhodné, protože hledá i neúplná slova)
                'organ_builders.description' => ''
            ];
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
