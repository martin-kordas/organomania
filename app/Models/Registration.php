<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\RegistrationObserver;
use App\Models\Scopes\OwnedEntityScope;

#[ObservedBy([RegistrationObserver::class])]
class Registration extends Model
{
    use HasFactory, SoftDeletes;
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    protected $guarded = [];
    
    public function dispositionRegisters()
    {
        return $this->belongsToMany(DispositionRegister::class, 'registration_disposition_register');
    }
    
}
