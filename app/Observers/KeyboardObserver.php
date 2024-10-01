<?php

namespace App\Observers;

use App\Models\Keyboard;

class KeyboardObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(Keyboard $keyboard): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(Keyboard $keyboard): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(Keyboard $keyboard): void
    {
        //
    }
    
    public function deleting(Keyboard $keyboard): void
    {
        $keyboard->dispositionRegisters()->delete();
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(Keyboard $keyboard): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(Keyboard $keyboard): void
    {
        //
    }
    
}
