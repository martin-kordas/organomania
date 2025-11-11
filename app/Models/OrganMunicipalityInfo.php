<?php

namespace App\Models;

use App\Services\MarkdownConvertorService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganMunicipalityInfo extends Model
{
    use HasFactory;

    public function getMetaDescription(): ?string
    {
        if (isset($this->description)) {
            $description = app(MarkdownConvertorService::class)->stripMarkDown($this->description);
            return str($description)->limit(200);
        }
        
        return null;
    }
    
}
