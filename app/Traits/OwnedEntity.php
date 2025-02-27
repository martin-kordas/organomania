<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\Scopes\OwnedEntityScope;

trait OwnedEntity
{
    
    abstract protected function getShowRoute(): string;
    
    public function isPublic()
    {
        return !isset($this->user_id);
    }
    
    public function scopePublic(Builder $query): void
    {
        $query->whereNull('user_id');
    }
    
    protected function privatePrefix(): Attribute
    {
        // při vytváření slugu zajišťuje, že slug soukromých položek má prefix 'private'
        return Attribute::make(
            get: fn () => !$this->isPublic() ? 'private' : '',
        );
    }
    
    public function getShareUrl()
    {
        $route = $this->getShowRoute();
        if (isset($this->user_id)) $relativeUrl = URL::signedRoute($route, $this->slug, absolute: false);
        else $relativeUrl = route($route, $this->slug, absolute: false);
        return url($relativeUrl);
    }
    
    private function getViewUrl()
    {
        $route = $this->getShowRoute();
        if (!Gate::allows('view', $this)) $relativeUrl = URL::signedRoute($route, $this->slug, absolute: false);
        else $relativeUrl = route($route, $this->slug, absolute: false);
        return url($relativeUrl);
    }
    
    // přizpůsobení sluggable: konflikty slugů se mají hledat napříč všemi záznamy
    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, string $attribute, array $config, string $slug): Builder
    {
        $query->withoutGlobalScope(OwnedEntityScope::class);
        return $query;
    }
    
}
