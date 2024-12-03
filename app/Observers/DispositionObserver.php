<?php

namespace App\Observers;

use App\Models\Disposition;

class DispositionObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(Disposition $disposition): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(Disposition $disposition): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(Disposition $disposition): void
    {
        //
    }
    
    public function deleting(Disposition $disposition): void
    {
        // relace je nutné mazat po jednom, aby se zavolala i událost deleting
        foreach ($disposition->keyboards as $keyboard) {
            $keyboard->delete();
        }
        $disposition->registrations()->delete();
        
        foreach ($disposition->registrationSets as $registrationSet) {
            $registrationSet->delete();
        }
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(Disposition $disposition): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(Disposition $disposition): void
    {
        //
    }
    
}
