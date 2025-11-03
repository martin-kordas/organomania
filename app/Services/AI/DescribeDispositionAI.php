<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class DescribeDispositionAI extends DispositionAI
{
    
    public function describe()
    {
        $content = $this->sendRequest();
        Log::info("DescribeDispositionAI - request made", ['organId' => $this->organ?->id]);
        return $content;
    }
    
    protected function sendRequest()
    {
        $language = locale_get_display_language($this->locale, 'en');
        
        if ($language === 'cs') $czechAdvices = ' For organ stops use Czech term "rejstříky".';
        else $czechAdvices = '';
        
        $systemContent = <<<EOL
            You will be given organ disposition.
            You should characterize the disposition of organ - describe important organ stops and characterize the whole disposition principles and style.
            Think about organ music reperoir suitable for this organ.
            Use $language language.$czechAdvices
            Use Markdown formatting, but format only bold text, avoid headings.
        EOL;
        
        $content = <<<EOL
            Provide characteristics of pipe organ. {$this->getOrganInfo()}
            The organ has following disposition:

            $this->dispositionPlain
        EOL;
        
        return $this->sendChatRequest('describe_disposition', [
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                ['role' => 'system', 'content' => $systemContent],
                ['role' => 'user', 'content' => $content],
            ],
        ]);
    }
    
}
