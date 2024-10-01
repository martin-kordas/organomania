<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OwnedEntityScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();
        $builder->whereNull("$table.user_id");
        if ($userId = Auth::id()) $builder->orWhere("$table.user_id", $userId);
    }
}
