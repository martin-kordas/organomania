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
use App\Helpers;
use App\Observers\OrganBuilderObserver;
use App\Models\OrganBuilderCustomCategory;
use App\Models\Region;
use App\Models\OrganBuilderCategory;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Models\Like;
use App\Enums\OrganBuilderCategory as OrganBuilderCategoryEnum;
use App\Traits\HasLinkComponent;
use App\Traits\OwnedEntity;
use App\Traits\Viewable;

#[ObservedBy([OrganBuilderObserver::class])]
class OrganBuilder extends Model
{
    use HasFactory, SoftDeletes, Searchable, Sluggable;
    use HasLinkComponent;
    use Viewable;
    use OwnedEntity {
        OwnedEntity::scopeWithUniqueSlugConstraints insteadof Sluggable;
    }

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
        ORGAN_BUILDER_ID_ORGANA = 52,
        ORGAN_BUILDER_ID_THEODOR_AGADONI = 874;

    // HACK: pokud varhanáře není čas vložit, přiřadíme varhany k tomuto varhanáři (varhanář se nezobrazí a zmíníme ho alespoň v popisu varan)
    const ORGAN_BUILDER_ID_NOT_INSERTED = 501;

    const ORGAN_BUILDER_CENTERS = [
      'Praha', 'Brno', 'Loket', 'Krnov', 'Kutná Hora', 'Opava', 'Znojmo',
    ];

    protected $guarded = [];

    protected $attributes = [
        'is_workshop' => false,
    ];

    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }

    protected function getShowRoute(): string
    {
        return 'organ-builders.show';
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

    public function caseOrgans()
    {
        return $this->hasMany(Organ::class, 'case_organ_builder_id')->orderBy('case_year_built')->orderBy('id');
    }

    public function additionalImages()
    {
        return $this->hasMany(OrganBuilderAdditionalImage::class)->orderByRaw('IFNULL(year_built, 9999)')->orderBy('id');
    }

    public function additionalImagesWithLocation()
    {
        return $this->additionalImages()->whereNotNull(['latitude', 'longitude']);
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
            $this->importance <= 1
            || (
                config('custom.hide_current_organ_builders_importance')
                && $this->isPublic()
                && $this->id !== static::ORGAN_BUILDER_ID_RIEGER_KLOSS
                && $this->id !== static::ORGAN_BUILDER_ID_ORGANA
                && $this->organBuilderCategories->contains(
                    fn($category) => $category->id === OrganBuilderCategoryEnum::BuiltFrom1990->value
                )
            );
    }

    public function scopeOrderByName(Builder $query, string $sortDirection = 'asc'): void
    {
        $raw = DB::raw('
            IF(
                organ_builders.is_workshop,
                organ_builders.workshop_name,
                CONCAT(organ_builders.last_name, IFNULL(organ_builders.first_name, ""))
            )'
        );
        $query->orderBy($raw, $sortDirection);
    }

    public function isInland()
    {
        return isset($this->region_id);
    }

    public function hasLocality()
    {
        return $this->latitude > 0 && $this->longitude > 0;
    }

    public function scopeInland(Builder $query): void
    {
        $query->whereNotNull('region_id');
    }

    // NOVÁK, Jan
    public function name(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if ($attributes['is_workshop']) return $attributes['workshop_name'];
                else {
                    // nepřevádíme na velká písmena, protože jde obvykle o importované varhanáře, kde je v příjmení uloženo i křestní jméno
                    if (!isset($attributes['first_name'])) return $attributes['last_name'];

                    $lastName = mb_strtoupper($attributes['last_name']);
                    return "$lastName, {$attributes['first_name']}";
                }
            }
        );
    }

    // Jan Novák
    public function standardName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if ($attributes['is_workshop']) return $attributes['workshop_name'];
                else {
                    if (!isset($attributes['first_name'])) return $attributes['last_name'];
                    return "{$attributes["first_name"]} {$attributes["last_name"]}";
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

    public function initialsName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['short_name'])) return $attributes['short_name'];
                elseif ($attributes['is_workshop']) return $attributes['workshop_name'];
                else {
                    $firstName = Helpers::makeInitials($attributes['first_name']);
                    return "$firstName {$attributes['last_name']}";
                }
            }
        );
    }

    public function typeName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                $type = $attributes['is_workshop'] ? 'varhanářská dílna' : 'varhanář';
                return __($type);
            }
        );
    }

    public function municipalityWithoutParenthesis(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['municipality'])) {
                    return preg_replace('/ \((.*)\)/', ', $1', $attributes['municipality']);
                }
            }
        );
    }

    public function getThumbnailImage()
    {
        if (isset($this->image_url))
            return ['image_url' => $this->image_url, 'image_credits' => $this->image_credits];
    }

    /**
     * @return int      číslo v rozsahu 0-100 reprezentující rok varhanáře v poměru k varhanářům s nejdřívějším a nejpozdějším rokem
     */
    public function getRelativeActiveFromYear()
    {
        static $yearMin, $yearMax;

        $yearMin ??= static::min('active_from_year') ?? false;
        $yearMax ??= static::max('active_from_year') ?? false;
        $yearMax = min($yearMax, today()->year);    // v importovaných datech je i 9999

        if ($yearMin && $yearMax) {
            $yearMax1 = $yearMax - $yearMin;
            $relativeYear = ($this->active_from_year - $yearMin) / $yearMax1;
            return round($relativeYear * 100);
        }

        return 100;
    }

    // first_name, last_name je rovněž nutné hledat fulltextově, jinak by současně zadané celé jméno (např. "Emanuel Petr") nenašlo nic
    #[SearchUsingFullText(['first_name', 'last_name', 'description', 'perex', 'workshop_members'])]
    public function toSearchableArray(): array
    {
        return
            $this->only([
                'workshop_name', 'first_name', 'last_name',
                'municipality', 'description', 'perex', 'workshop_members',
            ])
            + [
                // HACK: díky tomuto se sloupce hledají i ne-fulltextově (i u description výhodné, protože hledá i neúplná slova)
                'organ_builders.first_name' => '',
                'organ_builders.last_name' => '',
                'organ_builders.description' => '',
                'organ_builders.workshop_members' => '',
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

    public function getLinkComponent()
    {
        return 'components.organomania.organ-builder-link';
    }

    public function getCenter()
    {
        // např. je-li municipality varhanáře 'Praha-Žižkov', vrátíme 'Praha'
        foreach (static::ORGAN_BUILDER_CENTERS as $municipality) {
            if (str_starts_with($this->municipality, $municipality)) {
                return $municipality;
            }
        }
    }

    public function getCenterName()
    {
        return match ($this->getCenter()) {
            'Praha' => __('Varhanáři v Praze'),
            'Brno' => __('Brněnská varhanářská škola'),
            'Loket' => __('Loketská varhanářská škola'),
            'Opava' => __('Varhanáři v Opavě'),
            'Znojmo' => __('Varhanáři ve Znojmě'),
            'Kutná hora' => __('Kutnohorské varhanářství'),
            'Krnov' => __('Krnovské varhanářství'),
            default => $this->municipality . __(' a varhanářství'),
        };
    }

}
