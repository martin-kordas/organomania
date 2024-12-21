<?php

namespace App\Services\AI;

class SuggestRegistrationAI extends DispositionAI
{
    
    // když vrací i recommendations, volba rejstříků není dobrá
    protected bool $suggestRegistrationRecommendations = false;
    
    public function suggest(string $piece)
    {
        $res = $this->sendRequest($piece);
        return $this->processResponse($res);
    }
    
    protected function sendRequest(string $piece)
    {
        $language = locale_get_display_language($this->locale, 'en');
        $disposition = str($this->disposition)->replace('*', '');
        $recommendationsPrompt = $this->suggestRegistrationRecommendations ? "Output detailed recommentations in $language language on the second line." : '';
        
        $systemContent = <<<EOL
            You will be given organ piece and organ disposition.
            You should select organ stops I should use when playing the piece.
            Output only comma separated ordinal numbers of selected organ stops on the first line. $recommendationsPrompt
        EOL;
        
        $content = <<<EOL
            I want to play organ piece "$piece". What organ stops should I use on organ with this disposition?

            $disposition
        EOL;
        
        $chat1 = [
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $content],
            ],
        ];
        return $this->client->chat()->create($chat1);
    }
    
    protected function processResponse($res)
    {
        $content = $res->choices[0]->message->content ?? throw new \RuntimeException;
        $resRows = explode("\n", $content);
        
        $registerNumbers = str($content)
            ->explode(',')
            ->map(fn ($registerNumber) => intval($registerNumber))
            ->filter();
        if ($registerNumbers->isEmpty()) throw new \RuntimeException;
        
        if ($this->suggestRegistrationRecommendations) {
            $recommendations = trim(last($resRows));
            if ($recommendations === '') $recommendations = null;
        }
        else $recommendations = null;

        $dispositionRows = str($this->disposition)->explode("\n");
        $registerRowNumbers = [];
        foreach ($registerNumbers as $registerNumber) {
            $rowIndex = $dispositionRows->search(
                fn ($row) => str($row)->startsWith("$registerNumber. ")
            );
            if ($rowIndex === false) throw new \RuntimeException;
            $registerRowNumbers[] = $rowIndex + 1;
        }
        
        return compact('registerRowNumbers', 'recommendations');
    }
    
}
