<?php

namespace App\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    const
        AUTHOR_ID_SEHNAL = 1,
        AUTHOR_ID_SCHINDLER = 5,
        AUTHOR_ID_HORAK = 6;

    protected $fillable = [
        'first_name',
        'last_name',
        'year_of_birth',
        'year_of_death',
    ];

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_author')
            ->withPivot('order');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}"
        );
    }

    protected function fullNameReverse(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->last_name}, {$this->first_name}"
        );
    }

    protected function fullNameReverseCapital(): Attribute
    {
        return Attribute::make(
            get: function () {
                $lastName = mb_strtoupper($this->last_name);
                return "$lastName, {$this->first_name}";
            }
        );
    }

    protected function initialsName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->last_name . ", " . Helpers::makeInitials($this->first_name)
        );
    }

    protected function fullNameReverseWithYears(): Attribute
    {
        return Attribute::make(
            get: function () {
                $name = $this->full_name_reverse;
                if ($this->lifeData) $name .= " ({$this->lifeData})";
                return $name;
            }
        );
    }

    protected function lifeData(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->year_of_birth && $this->year_of_death) return "{$this->year_of_birth}–{$this->year_of_death}";
                elseif ($this->year_of_birth) return "*{$this->year_of_birth}";
                elseif ($this->year_of_death) return "✝{$this->year_of_death}";
            }
        );
    }
}
