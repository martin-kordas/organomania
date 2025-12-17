<?php

namespace App\Traits;

use ReflectionClass;

trait HasLinkComponent
{

    abstract public function getLinkComponent();
    
    public function renderLink(array $params = [])
    {
        $component = $this->getLinkComponent();

        // $entityParam: např. ["organ" => $organ]
        $entityParam = (new ReflectionClass($this))->getShortName();
        $entityParam = str($entityParam)->lcfirst()->toString();
        
        $paramsAll = $params + [$entityParam => $this];
        return view($component, $paramsAll)->render();
    }
    
}