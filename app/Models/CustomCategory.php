<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\OwnedEntityScope;
use App\Interfaces\Category;

abstract class CustomCategory extends Model implements Category
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'description', 'user_id'];
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    public function getValue(): int|string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function getColor(): string
    {
        return 'info';
    }
    
    public function isPeriodCategory(): bool
    {
        return false;
    }
    
}
