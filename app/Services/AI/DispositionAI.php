<?php

namespace App\Services\AI;

use OpenAI\Contracts\ClientContract;
use App\Helpers;
use App\Models\Organ;

abstract class DispositionAI
{
    
    protected string $locale;
    
    /**
     * @param string $disposition předpokládá se, že názvy manuálů jsou uvozeny **
     * @param bool $addRegisterNumbers zda do dispozice doplnit pořadová čísla rejstříků, anebo už tam jsou
     */
    public function __construct(
        protected string $disposition,
        protected ClientContract $client,
        protected ?Organ $organ = null,
        bool $addRegisterNumbers = true,
    )
    {
        $this->disposition = Helpers::normalizeLineBreaks($this->disposition);
        if ($addRegisterNumbers) $this->disposition = $this->addRegisterNumbers($this->disposition);
        
        $this->locale = app()->getLocale();
    }
    
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }
    
    protected function addRegisterNumbers(string $disposition)
    {
        return str($disposition)->explode("\n")->map(function ($row) {
            static $registerNumber = 1;
            if (trim($row) !== '' && !str($row)->startsWith('*')) {
                $row = "$registerNumber. $row";
                $registerNumber++;
            }
            return $row;
        })->implode("\n");
    }
    
    
}
