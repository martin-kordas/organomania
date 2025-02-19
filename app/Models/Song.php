<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\KancionalSongCategory;

class Song extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    const SONG_ID_502 = 1794;
    const SONG_ID_503 = 1795;
    const SONG_ID_504 = 1796;
    
    // https://www.karlin.mff.cuni.cz/~slavika/kanc_tisk/
    const SONG_NUMBERS_WITH_ACCOMPANIMENT = ["103", "104", "105", "106", "107", "108", "109", "111", "112", "114", "115", "116", "117", "121", "122", "123", "124", "125", "126", "127", "129", "130", "182", "183", "184", "185", "201", "202", "204", "205", "206", "207", "208", "209", "210", "211", "212", "213", "214", "215", "216", "217", "218", "220", "221", "223", "224", "225", "226", "228", "281", "281", "283", "285", "286", "288", "289", "290", "291", "301", "302", "303", "304", "305", "307", "308", "309", "310", "311", "312", "313", "313", "314", "316", "317", "318A", "318B", "319", "322", "382", "383", "384", "386", "401", "403", "404", "405", "406A", "406B", "407", "408", "409", "409", "410", "412", "413", "415", "421", "421", "422A", "422B", "423", "424", "425", "481", "482", "483", "484", "510B", "511B", "512", "513", "513", "515", "515", "516", "517", "517", "518A", "518B", "518B", "519", "521", "523", "524", "524", "525", "588", "588", "589", "590", "592", "701", "702", "703", "704A", "707", "708A", "708B", "709", "711", "712", "713", "714", "715", "715", "716", "717A", "717B", "717C", "718", "719", "720", "721", "722", "723", "724", "725", "727", "728", "784", "785", "787", "788", "790", "791", "792", "793", "794", "795", "796", "801", "802", "803", "804", "805", "806", "807", "808", "809", "810", "811", "812", "813A", "813B", "814", "815", "816", "817", "818", "819", "820", "821", "823", "824", "825", "827", "828", "829", "831", "832", "835", "839", "841", "842", "843", "844", "845", "881", "882", "883", "884", "889", "892", "901", "903", "904", "905", "906", "908", "909", "911", "912", "913", "917", "921", "922", "923", "924", "925", "926", "929", "931", "932A", "983"];
    
    public function worshipSongs()
    {
        return $this->hasMany(WorshipSong::class);
    }
    
    public function category(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                $ordinaries = [...range(502, 509), ...range(582, 585)];
                if (str($attributes['number'])->startsWith($ordinaries)) return KancionalSongCategory::Ordinaries;

                if ((int)$attributes['number'] < 100) return KancionalSongCategory::Prayers;
                
                $number = $attributes['number'][0] ?? throw new \RuntimeException;
                return KancionalSongCategory::from($number);
            }
        );
    }
    
    public function purposeFormatted(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (!isset($attributes['purpose'])) return null;
                
                $letter = $attributes['purpose'][0];
                $numbers = str($attributes['purpose'])->substr(1);
                return "$letter<sub>$numbers</sub>";
            }
        );
    }
    
    public function kancionalUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                return "https://kancional.cz/{$attributes['number']}";
            }
        );
    }
    
    public function accompanimentUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (in_array($attributes['number'], static::SONG_NUMBERS_WITH_ACCOMPANIMENT))
                    return "https://www.karlin.mff.cuni.cz/~slavika/kanc_tisk/pdf/{$attributes['number']}-1_0.pdf";
            }
        );
    }
    
}
