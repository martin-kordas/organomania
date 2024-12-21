<?php

namespace App\Services\AI;

use OpenAI\Contracts\ClientContract;
use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
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
    
    protected function getThreadReponse(ThreadRunResponse $threadRun)
    {
        // https://gehri.dev/blog/how-to-use-the-openai-assistants-api-in-php-and-laravel
        while (in_array($threadRun->status, ['queued', 'in_progress'])) {
            $threadRun = $this->client->threads()->runs()->retrieve(
                threadId: $threadRun->threadId,
                runId: $threadRun->id,
            );
            sleep(0.5);
        }

        if ($threadRun->status !== 'completed') throw new \RuntimeException;

        $messageList = $this->client->threads()->messages()->list(
            threadId: $threadRun->threadId,
        );

        return $messageList;
    }
    
    protected function getThreadReponseContent(ThreadRunResponse $threadRun)
    {
        $messageList = $this->getThreadReponse($threadRun);
        return $messageList->data[0]->content[0]->text->value;
    }
    
}
