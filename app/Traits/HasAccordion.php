<?php

namespace App\Traits;

use Livewire\Attributes\Renderless;

trait HasAccordion
{
    
    public function shouldShowAccordion($key)
    {
        return session($key, true);
    }

    #[Renderless]
    public function accordionToggle($key)
    {
        $showed = $this->shouldShowAccordion($key);
        session([$key => !$showed]);
    }
    
}