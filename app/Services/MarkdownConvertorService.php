<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;

class MarkdownConvertorService
{
    
    public function convert(string $markdown): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($markdown);
    }
    
}
