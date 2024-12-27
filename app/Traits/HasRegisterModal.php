<?php

namespace App\Traits;

use App\Enums\Pitch;
use App\Models\RegisterName;

trait HasRegisterModal
{
    
    public ?RegisterName $registerName = null;
    public ?Pitch $pitch = null;
    
    public function setRegisterName($registerNameId, $pitchId = null)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->registerName = RegisterName::find($registerNameId);
        $this->pitch = $pitchId ? Pitch::from($pitchId) : null;
    }
    
}
