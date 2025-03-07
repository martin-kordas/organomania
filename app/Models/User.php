<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Scopes\OwnedEntityScope;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const USER_ID_ADMIN = 1;
    const USER_ID_KRASNI = 3;
    const USER_ID_MARTIN_KORDAS = 5;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function isAdmin(): bool
    {
        return (bool)$this->admin;
    }
    
    public function getLastWorshipSongsOrgans(?int $limit = null)
    {
        $organs = Organ::withoutGlobalScope(OwnedEntityScope::class)
            ->with([
                'lastEditedWorshipSong' => fn (HasOne $query)
                    => $query->where('user_id', $this->id)
            ])
            ->whereHas('lastEditedWorshipSong', fn (Builder $query)
                => $query->where('user_id', $this->id)
            )
            ->select(['id', 'municipality', 'place', 'slug', 'user_id'])
            ->get()
            ->sortByDesc('lastEditedWorshipSong.created_at');
        
        // TODO: výkon - limit až na úrovni PHP
        if ($limit) $organs = $organs->slice(0, $limit);
        return $organs;
    }
    
}
