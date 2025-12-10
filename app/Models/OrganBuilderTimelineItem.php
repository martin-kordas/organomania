<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAnniversaryScope;
use App\Helpers;

class OrganBuilderTimelineItem extends Model
{
    use HasFactory, SoftDeletes;
    use HasAnniversaryScope;

    public function casts()
    {
        return [
            'is_workshop' => 'bool',
        ];
    }
    
    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }

    public function organs()
    {
        return $this->belongsToMany(Organ::class, 'organ_builder_timeline_item_organ');
    }

    public function activePeriod(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['active_period'])) return $attributes['active_period'];
                
                return "{$attributes['year_from']}–" . ($attributes['year_to'] ?? '?');
            }
        );
    }
    
    public function nameLowercase(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['name'])) {
                    return Helpers::nameToLowerCase($attributes['name']);
                }
            }
        );
    }

    public function nameLowercaseWithoutComma(): Attribute
    {
        return Attribute::make(
            get: function (mixed $_value, array $attributes) {
                if (isset($attributes['name'])) {
                    $name = Helpers::nameToLowerCase($attributes['name']);
                    $name = str_replace(',', '', $name);
                    return $name;
                }
            }
        );
    }

    public function loadFromOrganBuilder(OrganBuilder $organBuilder)
    {
        $this->organBuilder()->associate($organBuilder);
        
        $this->name = $organBuilder->name;
        $this->year_from = $organBuilder->active_from_year;
        
        $matches = [];
        if (preg_match('/[-|–]([0-9]{4})$/', $organBuilder->active_period, $matches))
            $yearTo = $matches[1];
        else $yearTo = $this->year_from + 50;
        
        $this->year_to = $yearTo;
        $this->is_workshop = $organBuilder->is_workshop;
        $this->active_period = $organBuilder->active_period;
        $this->land = 'Neurčeno';
    }
    
}
