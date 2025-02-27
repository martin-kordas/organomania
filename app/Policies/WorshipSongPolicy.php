<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WorshipSongPolicy
{

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $worshipSong): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $worshipSong): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, Model $worshipSong, array $allowedIds = []): bool
    {
        if (in_array($worshipSong->id, $allowedIds)) return true;
        
        if (isset($user)) {
            return $worshipSong->user_id === $user->id || $worshipSong->organ?->user_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Model $worshipSong): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Model $worshipSong): bool
    {
        //
    }
    
    
}
