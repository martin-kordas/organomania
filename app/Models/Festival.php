<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Region;
use App\Models\Organ;

class Festival extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['organ_id'];
    
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    
    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }
    
}
