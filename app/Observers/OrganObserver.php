<?php

namespace App\Observers;

use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;

class OrganObserver
{
    /**
     * Handle the Organ "created" event.
     */
    public function created(Organ $organ): void
    {
        //
    }

    /**
     * Handle the Organ "updated" event.
     */
    public function updated(Organ $organ): void
    {
        //
    }

    /**
     * Handle the Organ "deleted" event.
     */
    public function deleted(Organ $organ): void
    {
        //
    }
    
    public function deleting(Organ $organ): void
    {
        $organ->likes()->delete();
        $organ->organRebuilds()->delete();
        
        $organ->organCategories()->detach();
        $organ->organCustomCategories()->detach();
    }

    /**
     * Handle the Organ "restored" event.
     */
    public function restored(Organ $organ): void
    {
        //
    }

    /**
     * Handle the Organ "force deleted" event.
     */
    public function forceDeleted(Organ $organ): void
    {
        //
    }
    
}
