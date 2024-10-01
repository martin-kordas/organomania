<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Registration;
use App\Models\Disposition;

class RegistrationPolicy
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
    public function view(User $user, Registration $registration): bool
    {
        return $registration->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Disposition $disposition): bool
    {
        return !isset($disposition->user_id) || $disposition->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Registration $registration): bool
    {
        return $registration->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Registration $registration): bool
    {
        return $this->update($user, $registration);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Registration $registration): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Registration $registration): bool
    {
        //
    }
    
    
}
