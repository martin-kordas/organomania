<?php

namespace App\Observers;

use App\Models\RegistrationSet;

class RegistrationSetObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(RegistrationSet $registrationSet): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(RegistrationSet $registrationSet): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(RegistrationSet $registrationSet): void
    {
        //
    }
    
    public function deleting(RegistrationSet $registrationSet): void
    {
        $registrationSet->registrations()->detach();
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(RegistrationSet $registrationSet): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(RegistrationSet $registrationSet): void
    {
        //
    }
    
}
