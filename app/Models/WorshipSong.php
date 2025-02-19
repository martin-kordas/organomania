<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorshipSong extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];
    
    public function casts()
    {
        return [
            'date' => 'date',
        ];
    }
            
    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }
            
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
