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
        return $this->getData()['description'] ?? null;
    }
    
}
