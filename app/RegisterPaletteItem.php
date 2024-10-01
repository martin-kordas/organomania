<?php

namespace App;

use App\Enums\DispositionLanguage;
use App\Enums\Pitch;
use App\Models\RegisterName;

final readonly class RegisterPaletteItem
{
    
    public int $pitchOrder;
    
    public function __construct(
        public RegisterName $registerName,
        public Pitch $pitch,
        public ?string $multiplier,
        public DispositionLanguage $language
    ) {
        $this->pitchOrder = $pitch->getAliquoteOrder();
    }
    
    public function getRegisterName()
    {
        return $this->registerName->name;
    }
    
    public function getPitchLabel(?DispositionLanguage $language = null)
    {
        return $this->pitch->getLabel($language ?? $this->language);
    }
    
    public function getName()
    {
        return sprintf('%s %s', $this->getRegisterName(), $this->getPitchLabel());
    }
    
}
