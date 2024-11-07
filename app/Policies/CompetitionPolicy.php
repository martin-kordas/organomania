<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CompetitionPolicy extends OwnedEntityPolicy
{
    
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }
    
    public function createPublic(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        return false;
    }
    
}
