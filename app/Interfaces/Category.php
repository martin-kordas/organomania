<?php

namespace App\Interfaces;

interface Category
{
    
    public function getValue(): int|string;
    public function getName(): string;
    public function getDescription(): ?string;
    public function getColor(): string;
    
}