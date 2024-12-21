<?php

namespace App\Services;

use League\CommonMark\ConverterInterface;
use League\CommonMark\CommonMarkConverter;
use N0sz\CommonMark\Marker\MarkerExtension;

class MarkdownConvertorService
{
    
    private ConverterInterface $converter;
    
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
    
    public function convert(string $markdown): string
    {
        return $this->getConverter()->convert($markdown);
    }
    
}
