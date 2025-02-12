<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use OpenAI\Contracts\ClientContract;
use OpenAI\Contracts\ResponseContract;
use RuntimeException;

class DispositionOcr
{
    
    public function __construct(
        protected ClientContract $client
    )
    {
        
    }
    
    
    public function doOcr(array $photos)
    {
        if (empty($photos)) throw new RuntimeException;
        
        $res = $this->sendRequest($photos);
        Log::info("DispositionOcr - request made", ['imagesCount' => count($photos)]);
        return $this->getResponseContent($res);
    }
    
    protected function getResponseContent(ResponseContract $response)
    {
        return $response->choices[0]->message->content ?? throw new RuntimeException;
    }
    
    protected function sendRequest(array $photos)
    {
        $contentText = <<<EOL
            Output list of organ stops at given images.
            Output each stop on separate line.
            Stops from different photos separate by blank line.
            Do not ouput any other comments.
            If there is no organ stops at the image, do not output anything.
        EOL;
        $content = [
            ['type' => 'text', 'text' => $contentText]
        ];
        foreach ($photos as $path) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $url = "data:image/$extension;base64," . base64_encode($data);
            
            $content[] = [
                'type' => 'image_url',
                'image_url' => ['url' => $url],
            ];
        }
        
        $res = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'temperature' => 1,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ]);
        return $res;
    }
    
}
