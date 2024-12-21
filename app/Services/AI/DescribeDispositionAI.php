<?php

namespace App\Services\AI;

class DescribeDispositionAI extends DispositionAI
{
    
    public function describe()
    {
        $res = $this->sendRequest();
        return $this->getResponseContent($res);
    }
    
    protected function sendRequest()
    {
        $language = locale_get_display_language($this->locale, 'en');
        
        $systemContent = <<<EOL
            You will be given organ disposition.
            You should characterize the disposition of organ - describe important organ stops and characterize the whole disposition principles and style.
            Think about organ music reperoir suitable for this organ.
            Use $language language.
            Use Markdown formatting, but format only bold text, avoid headings.
        EOL;
        
        $content = <<<EOL
            Provide characteristics of pipe organ. {$this->getOrganInfo()}
            The organ has following disposition:

            $this->dispositionPlain
        EOL;
        
        $res = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $content],
            ],
        ]);
        return $res;
    }
    
}
