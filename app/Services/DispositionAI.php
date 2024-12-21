<?php

namespace App\Services;

use OpenAI\Contracts\ClientContract;
use App\Helpers;
use App\Models\Organ;

class DispositionAI
{
    
    private string $locale;
    
    /**
     * @param bool $addRegisterIds zda do dispozice doplnit identifikační čísla rejstříků, anebo už tam jsou
     */
    public function __construct(
        private string $disposition,
        private ClientContract $client,
        private ?Organ $organ = null,
        bool $addRegisterIds = true,
    )
    {
        $this->disposition = Helpers::normalizeLineBreaks($this->disposition);
        if ($addRegisterIds) $this->disposition = $this->addRegisterIds($this->disposition);
        
        $this->locale = app()->getLocale();
    }
    
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }
    
    private function addRegisterIds(string $disposition)
    {
        return str($disposition)->explode("\n")->map(function ($row) {
            static $registerId = 1;
            if (trim($row) !== '' && !str($row)->startsWith('*')) {
                $row .= "[$registerId]";
                $registerId++;
            }
            return $row;
        })->implode("\n");
    }
    
    public function suggestRegistration(string $piece)
    {
        $res = $this->sendSuggestRegistrationRequest($piece);
        return $this->processSuggestRegistrationResponse($res);
    }
    
    private function sendSuggestRegistrationRequest(string $piece)
    {
        $language = locale_get_display_language($this->locale, 'en');
        
        $content = <<<EOL
            I will write you pipe organ disposition where each stop is identified by ID in square brackets.
            Stop names may be in Czech, German or other languages.
            On this organ I want to play piece $piece.
            What organ stops and couplers should I use? Output only 1 variant.
            On the last line of output specify recommendations about chosen stops (use $language language and avoid any formatting).

            Disposition:
            $this->disposition
        EOL;
        
        return $this->client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                ['role' => 'user', 'content' => $content],
            ],
        ]);
    }
    
    private function processSuggestRegistrationResponse($res)
    {
        $content = $res->choices[0]->message->content ?? throw new \RuntimeException;
        $rows = explode("\n", $content);
        
        $matches = [];
        $registerIds = [];
        if (preg_match_all('/\[([0-9]+)\]/', $content, $matches))
            $registerIds = array_map(intval(...), $matches[1]);

        $recommendations = trim(last($rows));
        if ($recommendations === '') $recommendations = null;

        $rows = str($this->disposition)->explode("\n");
        $registerRowNumbers = [];
        foreach ($registerIds as $registerId) {
            $rowIndex = $rows->search(
                fn ($row) => str($row)->contains("[$registerId]")
            );
            if ($rowIndex === false) throw new \LogicException;
            $registerRowNumbers[] = $rowIndex + 1;
        }
        
        return compact('registerIds', 'registerRowNumbers', 'recommendations');
    }
    
}
