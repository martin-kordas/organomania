<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasAnniversaryScope
{

    public function scopeYearAnniversary(Builder $query, string $column, int $step, ?int $currentYear = null)
    {
        $currentYear ??= now()->year;
        $query
            ->where($column, '<', $currentYear)
            ->whereRaw("(? - $column) % ? = 0", [$currentYear, $step]);
    }

    public function scopeOrYearAnniversary(Builder $query, string $column, int $step, ?int $currentYear = null)
    {
        $query->orWhere(function (Builder $query) use ($column, $step, $currentYear) {
            $query->yearAnniversary($column, $step, $currentYear);
        });
    }

}