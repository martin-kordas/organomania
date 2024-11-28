<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\RegistrationSetObserver;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\OwnedEntity;

#[ObservedBy([RegistrationSetObserver::class])]
class RegistrationSet extends Model
{
    use HasFactory, SoftDeletes;
    use OwnedEntity;
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    protected $guarded = [];
    
    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'registration_set_registration')->orderByPivot('order');
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['private_prefix', 'name']
            ]
        ];
    }
    
}
