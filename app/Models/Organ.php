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
use Laravel\Scout\Attributes\SearchUsingPrefix;
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
use App\Traits\Viewable;
use App\Helpers;

#[ObservedBy([OrganObserver::class])]
class Organ extends Model
{
    use HasFactory, SoftDeletes, Searchable, Sluggable;
    use OwnedEntity, Viewable;
    
    const
        ORGAN_ID_OLOMOUC_KATEDRALA_SV_VACLAVA = 55,
        ORGAN_ID_PRAHA_KOSTEL_SV_LUDMILY = 80,
        ORGAN_ID_PRAHA_KOSTEL_SV_PETRA_A_PAVLA_VYSEHRAD = 83,
        ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_WOHLMUTOVA_KRUCHTA = 86,
        ORGAN_ID_PRAHA_KOSTEL_SV_MARKETY_BREVNOV = 73,
        ORGAN_ID_PRAHA_KOSTEL_SV_CYRILA_A_METODEJE_KARLIN = 82,
        ORGAN_ID_PRAHA_KOSTEL_SV_SALVATORA = 93,
        ORGAN_ID_PRIBRAM_SVATA_HORA = 98,
        ORGAN_ID_BRNO_JEZUITSKY_KOSTEL_NANEBEVZETI_PANNY_MARIE = 13,
        ORGAN_ID_PRAHA_KAROLINUM = 96,
        ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_ZAPADNI_KRUCHTA = 87,
        ORGAN_ID_PRAHA_KOSTEL_MATKY_BOZI_PRED_TYNEM = 74,
        ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_VELKE_VARHANY = 75,
        ORGAN_ID_DOUBRAVNIK = 20,
        ORGAN_ID_POLNA_KOSTEL_NANEBEVZETI_PANNY_MARIE_VELKE_VARHANY = 6,
        ORGAN_ID_PRAHA_OBECNI_DUM = 89,
        ORGAN_ID_KOLIN_KOSTEL_SV_BARTOLOMEJE = 127,
        ORGAN_ID_CESKY_KRUMLOV_KOSTEL_SV_VITA = 19,
        ORGAN_ID_NYMBURK_KOSTEL_SV_JILJI = 49,
        ORGAN_ID_PLZEN_VELKA_SYNAGOGA = 66,
        ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE = 1,
        ORGAN_ID_BRNO_KOSTEL_SV_AUGUSTINA = 15,
        ORGAN_ID_PRAHA_KOSTEL_SV_JAKUBA_VETSIHO = 9,
        ORGAN_ID_PRAHA_RUDOLFINUM = 8,
        ORGAN_ID_CHEB_KOSTEL_SV_MIKULASE = 29,
        ORGAN_ID_SMECNO = 103,
        ORGAN_ID_PLASY = 65,
        ORGAN_ID_ZLATA_KORUNA = 118,
        ORGAN_ID_DUB_NAD_MORAVOU = 2,
        ORGAN_ID_RYCHNOV_NAD_KNEZNOU_ZAMECKY_KOSTEL = 100,
        ORGAN_ID_BRNO_STARE_BRNO = 166,
        ORGAN_ID_PRAHA_KOSTEL_SV_VOJTECHA = 88,
        ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_BOCNI_EMPORA = 37,
        ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_VELKE_VARHANY = 38,
        ORGAN_ID_SLUKNOV = 104,
        ORGAN_ID_TEPLA = 107,
        ORGAN_ID_KLADRUBY = 34,
        ORGAN_ID_ZDAR = 120,
        ORGAN_ID_REPIN = 101,
        ORGAN_ID_OLOMOUC_SVATY_KOPECEK_HLAVNI_KUR = 51,
        ORGAN_ID_FILIPOV = 21,
        ORGAN_ID_KUTNA_HORA_SV_JAKUB_VELKE_VARHANY = 36,
        ORGAN_ID_MOST = 45,
        ORGAN_ID_BOZKOV = 169,
        ORGAN_ID_KRNOV_KOSTEL_SV_DUCHA = 35,
        ORGAN_ID_LUDGEROVICE = 3;
    
    protected $guarded = [];
    
    const DISPOSITION_APPENDIX_DELIMITER = '////';
    
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
        
        return $this
            ->belongsToMany(OrganCategory::class)
            ->withTimestamps()
            // HACK: řeší dodatečné přidání štítku
            ->orderByRaw('
                CASE
                    WHEN id = 18 THEN 10.5
                    WHEN id = 19 THEN 2.5
                    ELSE id
                END
            ');
    }
    
    public function shouldHideImportance()
    {
        return $this->importance <= 2;
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
    
    public function competitions()
    {
        return $this->belongsToMany(Competition::class)->withTimestamps()->orderBy('name');
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
    
    public function getDeclinedManuals($original = false)
    {
        $count = $original ? $this->original_manuals_count : $this->manuals_count;
        return __(Helpers::declineCount($count, 'manuálů', 'manuál', 'manuály'));
    }
    
    public function getDeclinedStops($original = false)
    {
        if ($this->stops_count < 0) throw new \LogicException;
        
        $count = $original ? $this->original_stops_count : $this->stops_count;
        return __(Helpers::declineCount($count, 'rejstříků', 'rejstřík', 'rejstříky'));
    }
    
    public function getDeclinedManualsCount($original = false)
    {
        $manuals = $this->getDeclinedManuals($original);
        $count = $original ? $this->original_manuals_count : $this->manuals_count;
        return "$count $manuals";
    }
    
    public function getDeclinedStopsCount($original = false)
    {
        $stops = $this->getDeclinedStops($original);
        $count = $original ? $this->original_stops_count : $this->stops_count;
        return "$count $stops";
    }
    
    #[SearchUsingFullText(['description', 'perex'])]
    public function toSearchableArray(): array
    {
        return 
            $this->only(['place', 'municipality', 'description', 'perex'])
            + [
                'organ_builders.last_name' => '', 'organ_builders.workshop_name' => '',
                // HACK: díky tomuto se description hledá i ne-fulltextově (výhodné, protože hledá i neúplná slova)
                'organs.description' => '',
            ];
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
    
    public function getSizeInfo($original = false): ?string
    {
        if (isset($this->manuals_count)) {
            if ($original) {
                $manualsCount = $this->original_manuals_count ?? $this->manuals_count;
                $stopsCount = $this->original_stops_count ?? $this->stops_count;
            }
            else {
                $manualsCount = $this->manuals_count;
                $stopsCount = $this->stops_count;
            }
            
            $parts = [];
            $parts[] = Helpers::formatRomanNumeral($manualsCount);
            if (isset($stopsCount)) $parts[] = $stopsCount;
            return implode('/', $parts);
        }
        return null;
    }
    
}
