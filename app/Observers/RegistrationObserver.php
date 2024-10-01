<?php

namespace App\Observers;

use App\Models\Registration;

class RegistrationObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(Registration $registration): void
    {
        //
    }
    
    public function deleting(Registration $registration): void
    {
        $registration->dispositionRegisters()->detach();
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(Registration $registration): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(Registration $registration): void
    {
        //
    }
    
}
