<?php

namespace App\Services\AI;

use OpenAI\Contracts\ClientContract;
use OpenAI\Contracts\ResponseContract;
use App\Helpers;
use App\Models\AiRequestLog;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    
    protected function sendChatRequest(string $operation, array $payload)
    {
        $retries = max((int) config('custom.ai.retry_attempts', 2), 0);
        $sleepMs = max((int) config('custom.ai.retry_sleep_ms', 500), 0);

        $attempt = 0;
        while (true) {
            try {
                $response = $this->client->chat()->create($payload);
                $content = $this->getResponseContent($response);
                $this->guardResponseLength($content);
                $this->logAiRequest($operation, $payload, $content, null);
                return $content;
            }
            catch (\Throwable $exception) {
                if ($attempt >= $retries) {
                    $this->logAiRequest($operation, $payload, null, $exception);
                    throw $exception;
                }

                $attempt++;
                if ($sleepMs > 0) usleep($sleepMs * 1000);
            }
        }
    }

    protected function addRegisterNumbers(string $disposition)
    {
        // sjednotí číslování rejstříků, aby AI dokázala jednoznačně odkazovat na konkrétní řádky
        $registerNumber = 1;   // explicitní čítač zabrání pokračování číslování při opakovaném volání metody
        return str($disposition)->explode("\n")->map(function ($row) use (&$registerNumber) {
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
    
    protected function guardResponseLength(string $content): void
    {
        $maxLength = (int) config('custom.ai.max_response_length', 6000);
        if ($maxLength > 0 && mb_strlen($content) > $maxLength) {
            throw new \LengthException('AI response exceeded configured length limit.');
        }
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
                    // data z importů nemusí vždy obsahovat rok přestavby; pokud chybí, vypustíme jej a ponecháme jen stavitele
                    if (isset($rebuild->year_built)) $label .= " in {$rebuild->year_built}";
                    return $label;
                })->join(', ', ' and ');
                $info .= " It was later rebuilt $rebuildsStr.";
            }
            elseif (isset($yearBuilt) && $yearBuilt < 1800) {
                // heuristika pro starší nástroje pomáhá modelu doplnit kontext o omezeném rozsahu a vhodném repertoáru
                $info .= " Organ was built in South German baroque style, which means it has probably limited keyboard range. Consider this when thinking about suitable repertoire.";
            }
        }
        else {
            // fallback věta, aby prompt dával smysl i při volání bez konkrétního modelu varhan
            $info .= ' Details about builder and construction year are not available.';
        }
        
        return $info;
    }
    
    protected function logAiRequest(string $operation, array $payload, ?string $response, ?\Throwable $exception): void
    {
        try {
            AiRequestLog::create([
                'operation' => Str::limit($operation, 100, ''),
                'prompt' => $this->truncateForDb($this->extractPromptFromPayload($payload)),
                'response' => isset($response) ? $this->truncateForDb($response) : null,
                'success' => !isset($exception),
                'error' => isset($exception) ? $this->truncateForDb($exception->getMessage()) : null,
            ]);
        }
        catch (\Throwable $loggingException) {
            Log::warning('AI request logging failed.', [
                'operation' => $operation,
                'original_error' => $exception?->getMessage(),
                'logging_error' => $loggingException->getMessage(),
            ]);
        }
    }

    protected function extractPromptFromPayload(array $payload): string
    {
        $messages = $payload['messages'] ?? [];
        if (!is_array($messages)) {
            return $this->encodePayload($payload);
        }

        $parts = [];
        foreach ($messages as $message) {
            if (is_array($message) && isset($message['role'], $message['content']) && is_string($message['content'])) {
                $parts[] = "{$message['role']}: {$message['content']}";
            }
            else {
                $parts[] = $this->encodePayload($message);
            }
        }

        return implode("\n---\n", $parts);
    }

    protected function encodePayload($payload): string
    {
        return (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function truncateForDb(string $value, int $limit = 65000): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return Str::limit($value, $limit, '...');
    }

}
