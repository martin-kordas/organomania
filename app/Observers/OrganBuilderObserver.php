<?php

namespace App\Observers;

use App\Models\OrganBuilder;

class OrganBuilderObserver
{
    /**
     * Handle the OrganBuilder "created" event.
     */
    public function created(OrganBuilder $organBuilder): void
    {
        //
    }

    /**
     * Handle the OrganBuilder "updated" event.
     */
    public function updated(OrganBuilder $organBuilder): void
    {
        //
    }

    /**
     * Handle the OrganBuilder "deleted" event.
     */
    public function deleted(OrganBuilder $organBuilder): void
    {
        $organBuilder->organBuilderCategories()->detach();
        $organBuilder->timelineItems()->delete();
    }

    /**
     * Handle the OrganBuilder "restored" event.
     */
    public function restored(OrganBuilder $organBuilder): void
    {
        //
    }

    /**
     * Handle the OrganBuilder "force deleted" event.
     */
    public function forceDeleted(OrganBuilder $organBuilder): void
    {
        //
    }
}
