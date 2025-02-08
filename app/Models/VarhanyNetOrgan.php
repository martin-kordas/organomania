<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarhanyNetOrgan extends Model
{
    
    protected $guarded = [];
    
    public $timestamps = false;
    
    public function casts()
    {
        return [
            'scraped_at' => 'datetime',
        ];
    }
    
}
