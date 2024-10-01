<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait OwnedEntity
{
    
    public function isPublic()
    {
        return !isset($this->user_id);
    }
    
    public function scopePublic(Builder $query): void
    {
        $query->whereNull('user_id');
    }
    
    protected function privatePrefix(): Attribute
    {
        // při vytváření slugu zajišťuje, že slug soukromých položek má prefix 'private'
        return Attribute::make(
            get: fn () => !$this->isPublic() ? 'private' : '',
        );
    }
    
}
