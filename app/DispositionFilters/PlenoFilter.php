<?php

namespace App\DispositionFilters;

use Illuminate\Support\Collection;
use App\Models\DispositionRegister;
use App\Models\Disposition;
use App\Enums\RegisterCategory;
use App\Enums\Pitch;

class PlenoFilter extends DispositionFilter
{
    
    public readonly string $name;
    public readonly ?string $description;
    
    public function __construct(
        protected Disposition $disposition,
    )
    {
        $this->name = 'Pleno';
        $this->description = __('Registrace obsahující v každé poloze principálový rejstřík (není-li, tak flétnový či krytí) a v nejvyšší poloze mixturu');
    }
    
    protected function filterRegisters(): Collection
    {
        $dispositionRegisters = $this->disposition->dispositionRegisters->filter(function (DispositionRegister $dispositionRegister) {
            $register = $dispositionRegister->register;
            if (!$register) return false;
            
            // např. Bifara je principál, ale nepatří do plena
            if ($register->registerCategories->pluck('id')->contains(RegisterCategory::Vychvevne->value)) return false;
            
            return (
                $register->registerCategory === RegisterCategory::Principal
                || $register->isMixture()
            );
        });
        
        $this->addMissingPitches($dispositionRegisters);
        $dispositionRegisters = $this->removeDuplicateMixtures($dispositionRegisters);
        
        return $dispositionRegisters;
    }
    
    private function addMissingPitches(Collection $dispositionRegisters)
    {
        // polohy neobsazené principály obsadíme flétnami nebo kryty
        foreach ($this->disposition->keyboards as $keyboard) {
            // v disposition-edit mají objekty index namísto id
            $keyboardId = $keyboard->id ?? $keyboard->keyboard_index;

            $pitches = [Pitch::Pitch_8, Pitch::Pitch_4];
            if ($keyboard->pedal) $pitches[] = Pitch::Pitch_16;

            foreach ($pitches as $pitch) {
                $isPitchPresent = $dispositionRegisters->contains(
                    fn(DispositionRegister $dispositionRegister) =>
                        ($dispositionRegister->keyboard_id ?? $dispositionRegister->keyboard_index) === $keyboardId
                        && $dispositionRegister->pitch === $pitch
                );
                if (!$isPitchPresent) {
                    $dispositionRegister = $this->disposition->dispositionRegisters->first(
                        fn(DispositionRegister $dispositionRegister) =>
                            ($dispositionRegister->keyboard_id ?? $dispositionRegister->keyboard_index) === $keyboardId
                            && $dispositionRegister->pitch === $pitch
                            && in_array(
                                $dispositionRegister->register?->registerCategory,
                                [RegisterCategory::Flute, RegisterCategory::Gedackt]
                            )
                    );
                    if ($dispositionRegister) $dispositionRegisters[] = $dispositionRegister;
                };
            }
        }
    }
    
    private function removeDuplicateMixtures(Collection $dispositionRegisters)
    {
        $mixtures = [];
        return $dispositionRegisters->filter(function (DispositionRegister $dispositionRegister) use (&$mixtures) {
            $keyboardId = $dispositionRegister->keyboard_id ?? $dispositionRegister->keyboard_index;
            if ($dispositionRegister->register?->isMixture()) {
                if (!isset($mixtures[$keyboardId])) $mixtures[$keyboardId] = $dispositionRegister;
                else return false;
            }
            return true;
        });
    }
    
}
