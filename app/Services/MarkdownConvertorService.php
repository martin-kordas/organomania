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
    
    private bool $convertCustomLinks = true;
    
    const CUSTOM_LINK_REGEX = '/\{\{(organ|organBuilder|festival|competition|registerName)\}(.*?)\}\((.*?)\)/u';
    const LINK_REGEX = '/\[(.*?)\]\(.*?\)/u';

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

    private function convertCustom(string $markdown, bool $newTab = false): string
    {
        $res = $this->convertCustomBraces($markdown);
        $res = $this->convertCustomLinks($res, $newTab);
        return $res;
    }
    
    private function convertCustomLinks(string $markdown, bool $newTab = false): string
    {
        // vytvoří odkazy do Organomanie
        return preg_replace_callback(
            static::CUSTOM_LINK_REGEX,
            function ($res) use ($newTab) {
                [, $entityType, $text, $slug] = $res;
                
                if (str_ends_with($text, '|nodetail')) {
                    $noDetail = true;
                    $text = mb_substr($text, 0, mb_strlen($text) - 9);
                }
                else $noDetail = false;
                
                if (!$this->convertCustomLinks) return $text;
              
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
                    'organ' => [
                        'showSizeInfo' => !$noDetail,
                        'year' => $noDetail ? false : null
                    ],
                    'organBuilder' => [
                        'showActivePeriod' => !$noDetail && !$entity->is_workshop && $entity->active_period !== 'současnost'
                    ],
                    'festival' => ['showDetails' => false],
                    'registerName' => ['showCategory' => false],
                    default => [],
                };
                // pracujeme již s převedeným markdownem, který znaky konvertoval na entity
                $linkParams['name'] = html_entity_decode($text);
                $linkParams['iconLink'] = false;
                $linkParams['showDescription'] = !$noDetail;
                if ($newTab) $linkParams['newTab'] = true;
                
                // nadbytečné mezery (např. před tečkou za odkazem) trimováním odstranit nelze - jde o více zanořených HTML elementů a Livewire direktivy v HTML komentářích
                $link = trim($entity->renderLink($linkParams));
                // v markdownu je nastaveno white-space: pre-line, Blade šablony s tím nepočítají a obsahují odřádkování
                $link = "<span class='custom-link $entityType'>$link</span>";
                return $link;
            },
            $markdown,
        );
    }

    private function convertCustomBraces(string $markdown): string
    {
        return str($markdown)
            // letopočty šedě (např. "1900", "1900-1950")
            //  - nefunguje pro částečně známé letopočty ("1751-?", "*1980", "1900?-1950" atd.)
            ->replaceMatches('/\([0-9]{4}([–-][0-9]{4})?\)/u', '<span class="text-secondary">$0</span>')
            // informace o varhanách šedě (např. "1865, II/35")
            //  - nefunguje, je-li uveden jen počet manuálů (např. "1865, II")
            ->replaceMatches('/\([^()]*[IV]+\/[0-9]+[^()]*\)/u', '<span class="text-secondary">$0</span>')
            ->toString();
    }

    public function convert(string $markdown, bool $newTab = false): string
    {
        $res = $this->getConverter()->convert($markdown);
        $res = $this->convertCustom($res, $newTab);
        return trim($res);
    }
    
    public function stripMarkdown(string $markdown, bool $preserveLineBreaks = false): string
    {
        return str($markdown)
            ->replace('*', '')
            ->replace('|nodetail', '')
            ->when(
                $preserveLineBreaks,
                fn ($str) => $str->replaceMatches('/[ ]+/u', ' '),
                fn ($str) => $str->replaceMatches('/\s+/u', ' '),
            )
            ->replaceMatches(static::LINK_REGEX, function ($res) {
                [, $text] = $res;
                return $text;
            })
            ->replaceMatches(static::CUSTOM_LINK_REGEX, function ($res) {
                [, , $text] = $res;
                return $text;
            })
            ->toString();
    }
    
    public function setConvertCustomLinks(bool $convertCustomLinks)
    {
        $this->convertCustomLinks = $convertCustomLinks;
        return $this;
    }

}
