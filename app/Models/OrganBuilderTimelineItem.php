<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganBuilderTimelineItem extends Model
{
    use HasFactory, SoftDeletes;

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
