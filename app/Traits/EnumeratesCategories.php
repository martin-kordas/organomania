<?php

namespace App\Traits;

trait EnumeratesCategories
{
    
    private function getData()
    {
        return static::DATA[$this->value] ?? throw new \LogicException;
    }
    
    public function getName(): string
    {
        return $this->getData()['name'];
    }
    
    public function getDescription(): ?string
    {
        $description = $this->getData()['description'] ?? null;
        if (isset($description)) return $description;
        
        if (method_exists($this, 'isPeriodCategory') && $this->isPeriodCategory()) {
            return __('Období');
        }
        return null;
    }
    
}
