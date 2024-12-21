<?php

namespace App\Services\AI;

class SuggestRegistrationAI extends DispositionAI
{
    
    protected bool $suggestRegistrationRecommendations = true;
    
    public function suggest(string $piece)
    {
        $res = $this->sendRequest($piece);
        return $this->processResponse($res);
    }
    
    protected function sendRequest(string $piece)
    {
        $language = locale_get_display_language($this->locale, 'en');
        $disposition = str($this->disposition)->replace('*', '');
        
        $systemContent = <<<EOL
            You will be given organ piece and organ disposition.
            You should select organ stops I should use when playing the piece.
            Output only comma separated ordinal numbers of organ stops.
        EOL;
        
        $content = <<<EOL
            I want to play organ piece "$piece". Which organ stops should I use on organ with this disposition?

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
        $resStops = $this->client->chat()->create($chat1);
        
        // pokud se recommendations vyžádají už v prvním requestu, zvolená registrace není moc dobrá, proto samostatná request
        if ($this->suggestRegistrationRecommendations) {
            $content = <<<EOL
                Provide recommentations about selected stops in $language language.
                Use Markdown formatting, but only bold text.
                Do not use stop ordinal numbers in the text.
            EOL;
            
            $chat2 = $chat1;
            $chat2['messages'][] = ['role' => 'assistant', 'content' => $this->getResponseContent($resStops)];
            $chat2['messages'][] = ['role' => 'user', 'content' => $content];
            $resRecommendations = $this->client->chat()->create($chat2);
        }
        else $resRecommendations = null;
        
        return [$resStops, $resRecommendations];
    }
    
    protected function processResponse($res)
    {
        [$resStops, $resRecommendations] = $res;
        $contentStops = $this->getResponseContent($resStops);
        
        $registerNumbers = str($contentStops)
            ->explode(',')
            ->map(fn ($registerNumber) => intval($registerNumber))
            ->filter();
        if ($registerNumbers->isEmpty()) throw new \RuntimeException;
        
        if ($this->suggestRegistrationRecommendations) {
            $recommendations = $this->getResponseContent($resRecommendations);
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
        //dd($this->disposition, $registerNumbers, $registerRowNumbers);
        
        return compact('registerRowNumbers', 'recommendations');
    }
    
}
