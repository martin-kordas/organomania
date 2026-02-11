<?php

namespace App\Models;

use App\Enums\DispositionLanguage;
use App\Enums\PublicationTopic;
use App\Enums\PublicationType;
use App\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use HasFactory, SoftDeletes;

    protected function publicationType(): Attribute
    {
        return Helpers::makeEnumAttribute('publication_type_id', PublicationType::from(...));
    }

    protected function publicationTopic(): Attribute
    {
        return Helpers::makeEnumAttribute('publication_topic_id', PublicationTopic::from(...));
    }

    protected function language(): Attribute
    {
        return Helpers::makeEnumAttribute('language', DispositionLanguage::from(...));
    }

    protected function displayedName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name_cz ?? $this->name
        );
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }

    public function organBuilder()
    {
        return $this->belongsTo(OrganBuilder::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'publication_author')
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
