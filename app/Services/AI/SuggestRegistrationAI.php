<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class SuggestRegistrationAI extends DispositionAI
{
    
    protected bool $suggestRegistrationRecommendations = true;
    
    public function suggest(string $piece)
    {
        $res = $this->sendRequest($piece);
        Log::info("SuggestRegistrationAI - request made", ['organId' => $this->organ?->id, 'piece' => $piece]);
        return $this->processResponse($res);
    }
    
    protected function sendRequest(string $piece)
    {
        $language = locale_get_display_language($this->locale, 'en');
        
        $systemContent = <<<EOL
            You will be given organ piece and organ disposition.
            You should select organ stops I should use when playing the piece.
            Stop names write always with their ordinal numbers in square brackets.
            Do not propose any alternative stops.
        EOL;
        
        $content = <<<EOL
            I want to play organ piece "$piece". Which organ stops should I use on organ with this disposition?

            $this->dispositionPlain
        EOL;
        
        $chat1 = [
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $content],
            ],
        ];
        $stopsContent = $this->sendChatRequest('suggest_registration.stops', $chat1);
        
        // pokud se recommendations vyžádají už v prvním requestu, zvolená registrace není moc dobrá, proto samostatná request
        if ($this->suggestRegistrationRecommendations) {
            $content = <<<EOL
                Provide recommendations about selected stops in $language language.
                Use Markdown formatting, but only bold text.
                Do not use stop ordinal numbers in the text.
            EOL;
            
            $chat2 = $chat1;
            $chat2['messages'][] = ['role' => 'assistant', 'content' => $stopsContent];
            $chat2['messages'][] = ['role' => 'user', 'content' => $content];
            $recommendationsContent = $this->sendChatRequest('suggest_registration.recommendations', $chat2);
        }
        else $recommendationsContent = null;
        
        return [$stopsContent, $recommendationsContent];
    }
    
    protected function processResponse($res)
    {
        [$contentStops, $recommendationsContent] = $res;
        
        $registerNumbers = str($contentStops)->matchAll('/\[([0-9]+)\]/');
        if ($registerNumbers->isEmpty()) throw new \RuntimeException;
        
        if ($this->suggestRegistrationRecommendations) {
            $recommendations = $recommendationsContent;
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
