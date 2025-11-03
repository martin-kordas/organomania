<?php

namespace App\Services;

use App\Enums\DispositionLanguage;
use App\Enums\Pitch;
use App\Models\RegisterName;

class DispositionTextualFormatter
{
    
    const APPENDIX_DELIMITER = '////';
    const CREDITS_DELIMITER = '---';
    
    private DispositionLanguage $dispositionLanguage;
    
    public function __construct(
        private MarkdownConvertorService $markdownConvertor,
    ) {
        $this->dispositionLanguage = DispositionLanguage::getDefault();
    }
    
    public function setDispositionLanguage(DispositionLanguage $dispositionLanguage)
    {
        $this->dispositionLanguage = $dispositionLanguage;
    }
    
    public function format(?string $disposition, bool $links = false, bool $credits = true)
    {
        // volitelné odstranění kreditů
        if (!$credits) {
            $pos = str($disposition)->position(static::CREDITS_DELIMITER);
            if ($pos !== false) {
                $disposition = str($disposition)->substr(0, $pos);
            }
        }
        
        $disposition = preg_replace('/([1-9])x\b/', '$1×', $disposition);

        $disposition = str($this->markdownConvertor->convert($disposition))->trim();
        
        // zarovnání stopových výšek doprava
        //  - na řádku nesmí být čárka, to značí více spojek na 1 řádku - nemá smysl zarovnávat
        $disposition = preg_replace_callback('#^([^,]+?)(([0-9]+ )?[0-9/]+\'[^,\\n]*)$#m', function ($matches) {
            // je-li floatující část stringu příliš velká, rozbila by vykreslení
            // TODO: zohlednit raději součet počtu znaků vč. názvu rejstříku ($matches[1])
            if (mb_strlen($matches[2]) <= 10) {
                return "{$matches[1]}<span class='register-pitch float-end'>{$matches[2]}</span>";
            }
            return $matches[0];
        }, $disposition);

        // odkazy na rejstříky do encyklopedie rejstříků - dynamicky získáním názvu z textu dispozice a dohledáním rejstříku v db.
        $disposition = str($disposition)->explode("\n")->map(function ($row) use ($links) {
            static $appendix = false;
            if (str($row)->contains(static::APPENDIX_DELIMITER)) $appendix = true;
            elseif (!$appendix && $links) $row = $this->addLinkToDispositionRow($row);
            return $row;
        })->implode("\n");

        // appendix vypíšeme malým písmem
        $pos = str($disposition)->position(static::APPENDIX_DELIMITER);
        if ($pos !== false) {
            $disposition = str($disposition)
                ->replace(static::APPENDIX_DELIMITER, '<small>')
                ->append('</small>');
        }
        
        return $disposition;
    }
    
    private function addLinkToDispositionRow(string $row)
    {
        //  - <mark>: zvýraznění rejstříku při suggestRegistration()
        //  - 0-9: číslování
        return preg_replace_callback('/^(<mark>)?([0-9+]+\\\\?\. )?([[:alpha:]´-]+( [[:alpha:]´-]+)*)/u', function ($matches) use ($row) {
            $registerName = RegisterName::where('name', $matches[3])->first();
            if ($registerName) {
                $url = route('dispositions.registers.show', $registerName->slug);
                $urlHtml = e($url);
                $class = 'link-dark link-offset-1 link-underline-opacity-10 link-underline-opacity-50-hover';
                $registerNameStr = $matches[3];
                $pitchIdArg = $this->getPitchFromDispositionRow($row)?->value ?? 'null';
                return "{$matches[1]}{$matches[2]}<a href='$urlHtml' class='$class' wire:click='setRegisterName({$registerName->id}, $pitchIdArg)' data-bs-toggle='modal' data-bs-target='#registerModal'>$registerNameStr</a>";
            }
            return $matches[0];
        }, $row, limit: 1);
    }
    
    private function getPitchFromDispositionRow(string $row): ?Pitch
    {
        $matches = [];
        if (preg_match("#[0-9/ ]+'#", $row, $matches)) {
            return Pitch::tryFromLabel($matches[0], $this->dispositionLanguage);
        }
        return null;
    }
    
}