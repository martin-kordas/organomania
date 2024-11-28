<?php

namespace App\Observers;

use App\Models\RegistrationSet;

class RegistrationSetObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(RegistrationSet $registration): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(RegistrationSet $registration): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(RegistrationSet $registration): void
    {
        //
    }
    
    public function deleting(RegistrationSet $registration): void
    {
        $registration->registrations()->detach();
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(RegistrationSet $registration): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(RegistrationSet $registration): void
    {
        //
    }
    
}
