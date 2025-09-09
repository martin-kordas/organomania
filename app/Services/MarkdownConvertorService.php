<?php

namespace App\Services;

use RuntimeException;
use League\CommonMark\ConverterInterface;
use League\CommonMark\CommonMarkConverter;
use N0sz\CommonMark\Marker\MarkerExtension;
use App\Models\Competition;
use App\Models\Festival;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\RegisterName;

class MarkdownConvertorService
{

    private ConverterInterface $converter;
    
    const CUSTOM_LINK_REGEX = '/\{\{(organ|organBuilder|festival|competition|registerName)\}(.*?)\}\((.*?)\)/';

    private function getConverter()
    {
        if (!isset($this->converter)) {
            $this->converter = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            $environment = $this->converter->getEnvironment();
            // ==...== způsobí highlight
            $environment->addExtension(new MarkerExtension());
        }
        return $this->converter;
    }

    private function convertCustom(string $markdown): string
    {
        $res = $this->convertCustomLinks($markdown);
        return $res;
    }
    
    private function convertCustomLinks(string $markdown): string
    {
        // vytvoří odkazy do Organomanie
        return preg_replace_callback(
            static::CUSTOM_LINK_REGEX,
            function ($res) {
                [, $entityType, $text, $slug] = $res;
                $model = match ($entityType) {
                    'organ' => new Organ,
                    'organBuilder' => new OrganBuilder,
                    'festival' => new Festival,
                    'competition' => new Competition,
                    'registerName' => new RegisterName,
                    default => new RuntimeException,
                };
                $entity = $model->firstWhere('slug', $slug);
                if (!$entity) return $text;
                
                $linkParams = match ($entityType) {
                    'organ' => ['showSizeInfo' => true],
                    'organBuilder' => ['showActivePeriod' => !$entity->is_workshop],
                    'festival' => ['showDetails' => false],
                    'registerName' => ['showCategory' => false],
                    default => [],
                };
                $linkParams['name'] = $text;
                $linkParams['iconLink'] = false;
                
                $link = trim($entity->renderLink($linkParams));
                // v markdownu je nastaveno white-space: pre-line, Blade šablony s tím nepočítají a obsahují odřádkování
                $link = "<span class='custom-link'>$link</span>";
                return $link;
            },
            $markdown,
        );
    }

    public function convert(string $markdown): string
    {
        $res = $this->getConverter()->convert($markdown);
        $res = $this->convertCustom($res);
        return $res;
    }
    
    public function stripMarkdown(string $markdown): string
    {
        return str($markdown)
            ->replace('*', '')
            ->replaceMatches('/\s+/', ' ')
            ->replaceMatches(static::CUSTOM_LINK_REGEX, function ($res) {
                [, , $text] = $res;
                return $text;
            })
            ->toString();
    }

}
