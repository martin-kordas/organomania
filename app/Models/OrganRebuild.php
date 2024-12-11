<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrganBuilder;

class OrganRebuild extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    
    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }
    
    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }
    
}
