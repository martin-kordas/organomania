<?php

namespace App\Traits;

use LogicException;

trait EnumeratesCategories
{
    
    abstract public function isPeriodCategory(): bool;

    private function getData()
    {
        return static::DATA[$this->value] ?? throw new \LogicException;
    }
    
    public function getName(): string
    {
        return $this->getData()['name'];
    }
    
    public function getShortName(): string
    {
        $data = $this->getData();
        return $data['shortName'] ?? $data['name'];
    }
    
    public function getDescription(): ?string
    {
        $description = $this->getData()['description'] ?? null;
        if (isset($description)) return $description;
        
        if ($this->isPeriodCategory()) {
            return __('Období');
        }
        return null;
    }

    public function getPeriodRange(): array
    {
        if (!$this->isPeriodCategory()) throw new LogicException;

        return $this->getData()['periodRange'];
    }
    
}
