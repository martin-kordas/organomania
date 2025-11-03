<?php

namespace App\Services\AI;

use OpenAI\Contracts\ClientContract;
use OpenAI\Contracts\ResponseContract;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;

abstract class DispositionAI
{
    
    protected string $dispositionPlain;
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
        $this->dispositionPlain = str($this->disposition)->replace('*', '');
        
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
                $row = str($row)->replaceMatches('/^[0-9]+\\\\?\. /', '');     // odstranění existujícího číslování
                $row = "$registerNumber. $row";
                $registerNumber++;
            }
            return $row;
        })->implode("\n");
    }
    
    protected function getResponseContent(ResponseContract $response)
    {
        return $response->choices[0]->message->content ?? throw new \RuntimeException;
    }
    
    protected function getOrganBuilderLabel(OrganBuilder $organBuilder)
    {
        if ($organBuilder->is_workshop) return "organ workshop '{$organBuilder->name}'";
        else return "organ builder '{$organBuilder->first_name} {$organBuilder->last_name}'";
    }
    
    protected function getOrganInfo()
    {
        $info = 'The organ is located in Czech Republic.';
        
        $organ = $this->organ;
        if (isset($organ)) {
            $organBuilder = $organ->organBuilder;
            $info .= ' It was built by ';
            if (!isset($organBuilder)) $info .= 'unknown organ builder';
            else $info .= $this->getOrganBuilderLabel($organBuilder);

            $yearBuilt = $organ->year_built;
            if ($yearBuilt) $info .= " in $yearBuilt";
            $info .= ".";

            $organRebuilds = $organ->organRebuilds;
            if ($organRebuilds->isNotEmpty()) {
                $rebuildsStr = $organRebuilds->map(function (OrganRebuild $rebuild) {
                    $label = 'by ';
                    $label .= $this->getOrganBuilderLabel($rebuild->organBuilder);
                    if (isset($rebuild->year_built)) $label .= " in {$rebuild->year_built}";
                    return $label;
                })->join(', ', ' and ');
                $info .= " It was later rebuilt $rebuildsStr.";
            }
            elseif (isset($yearBuilt) && $yearBuilt < 1800) {
                $info .= " Organ was built in South German baroque style, which means it has probably limited keyboard range. Consider this when thinking about suitable repertoire.";
            }
        }
        else {
            $info .= ' Details about builder and construction year are not available.';
        }
        
        return $info;
    }
    
}
