<?php

namespace App\Quiz\Questions;

use App\Services\MarkdownConvertorService;
use Illuminate\Database\Eloquent\Builder;

class OrganBuilderFromDescriptionQuestion extends OrganBuilderQuestion
{
    public protected(set) string $template = 'organ-builder-from-description';
    
    protected bool $applyScopeForAnswers = false;
    
    const int FREQUENCY = 10;
    
    protected function scope(Builder $query)
    {
        $query
            ->whereRaw('LENGTH(description) > 350')
            ->whereNotNull('name_base_words');
    }
    
    public function getDescription()
    {
        $description = $this->questionedEntity->description;
        
        // výskyty jména varhanáře nahradíme za placeholder
        $baseWords = explode(',', $this->questionedEntity->name_base_words);
        $placeholder = '//placeholder//';
        foreach ($baseWords as $word) {
            $word1 = preg_quote($word, '/');
            $description = preg_replace(
                '/\b' . $word1 . '\S*/iu', $placeholder,
                $description
            );
        }
        
        $markdownConvertor = app(MarkdownConvertorService::class);
        $description = $markdownConvertor->convert($description);
        
        $description = str_replace('//placeholder//', '<span class="placeholder col-1"></span>', $description);
        $description = str_replace('*', '', $description);  // pozůstatky po markdownu
        
        return trim($description);
    }
    
}
