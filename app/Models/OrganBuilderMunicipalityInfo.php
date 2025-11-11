<?php

namespace App\Models;

use App\Services\MarkdownConvertorService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganBuilderMunicipalityInfo extends Model
{
    use HasFactory;

    // TODO: duplicitní kód s OrganMunicipalityInfo
    public function getMetaDescription(): ?string
    {
        if (isset($this->description)) {
            $description = app(MarkdownConvertorService::class)->stripMarkDown($this->description);
            return str($description)->limit(200);
        }
        
        return null;
    }
    
}
