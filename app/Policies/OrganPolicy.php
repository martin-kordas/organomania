<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OrganPolicy extends OwnedEntityPolicy
{
    
    public function viewWorshipSongs(?User $user, Model $model): bool
    {
        if (!isset($user)) return false;
        
        return
            !isset($model->user_id)
            || isset($user) && $model->user_id === $user->id;
    }
    
}
