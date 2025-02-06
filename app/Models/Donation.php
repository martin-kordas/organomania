<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    
    public function casts()
    {
        return [
            'date' => 'date',
        ];
    }
    
}
