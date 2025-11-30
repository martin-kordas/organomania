<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\OwnedEntityScope;

class AbstractRepository
{
    
    protected const MODEL_CLASS = Model::class;
    
    protected function filter(Builder $query, string $column, mixed $value)
    {
        if (is_array($value)) {
            $query->whereIn($column, $value);
        }
        else {
            $query->where($column, $value);
        }
    }
    
    protected function orderBy(Builder $query, string $column, string $direction)
    {
        if ($column === 'importance') $expr = DB::raw('ROUND(importance / 2)');
        else $expr = $column;
        
        $query
            ->orderBy(DB::raw("`$column` IS NULL"))
            ->orderBy($expr, $direction);
    }
    
    // filtry společné pro různé entity
    protected function filterEntityQuery(Builder $query, $field, $value)
    {
        // kvůli joinům může nastávat konflikt názvů sloupců, proto uvádíme i tabulku
        //  - např. v organs se někdy joinuje organ_builders
        $tbl = $query->getModel()->getTable();
        
        switch ($field) {
            case 'id':
                $this->filter($query, 'id', $value);
                break;
            
            case 'regionId':
                $query->where("$tbl.region_id", $value);
                break;

            case 'importance':
                $query->where("$tbl.importance", '>=', $value * 2 - 1);
                break;

            case 'isFavorite':
                $subFilter = fn(Builder $query) => $query->where('user_id', Auth::id());
                if ($value) $query->whereHas('likes', $subFilter);
                else $query->whereDoesntHave('likes', $subFilter);
                break;

            case 'isPrivate':
                if ($value) $query->whereNotNull("$tbl.user_id");
                else $query->whereNull("$tbl.user_id");
                break;
                    
            default:
                throw new \LogicException;
        }
    }
    
    protected function filterLike(Builder $query, $field, $value)
    {
        $query->whereLike($field, "%$value%");
    }

    protected function selectDistance(Builder $query, float $latitude, float $longitude)
    {
        $tbl = $query->getModel()->getTable();
        
        $query->selectRaw("
            ST_DISTANCE_SPHERE(
                POINT($tbl.longitude, $tbl.latitude),
                POINT(?, ?)
            ) AS distance
        ", [$longitude, $latitude]);
        
        $query->selectRaw("
            DEGREES(
                ATAN2($tbl.longitude - ?, $tbl.latitude - ?)
            ) AS angle
        ", [$longitude, $latitude]);
    }
    
    protected function filterNear(Builder $query, float $latitude, float $longitude, float $nearDistance)
    {
        $this->selectDistance($query, $latitude, $longitude);
        
        $query->having('distance', '<=', $nearDistance * 1000);
    }
    
    protected function trimFilterValue($value)
    {
        if (is_array($value)) return array_map(trim(...), $value);
        else {
            $value = str($value)->replaceMatches('/\s+/u', ' ')->trim()->toString();
            return $value;
        }
    }
    
    protected function getCategoriesHelp(
        Model $model,
        $withCount,
        $allowIds = []
    )
    {
        $builder = $model->query();
        if (!empty($withCount)) $builder->withCount($withCount);
        if (!empty($allowIds)) $builder->whereIn('id', $allowIds);
        return $builder->get();
    }
    
    protected function getCustomCategoriesHelp(
        Model $model,
        $mainEntityRelation,
        $withCount,
        $allowIds = []
    ): Collection
    {
        $builder = $model->query();
        if (!empty($allowIds)) {
            $builder
                ->withoutGlobalScope(OwnedEntityScope::class)
                ->where(function (Builder $query) use ($allowIds) {
                    if ($userId = Auth::id()) {
                        $query->where('user_id', $userId);
                    }
                    $query->orWhereIn('id', $allowIds);
                });
            if (in_array($mainEntityRelation, $withCount)) {
                $withCount = array_diff($withCount, [$mainEntityRelation]);
                $withCount[$mainEntityRelation] = function (Builder $query) {
                    $query->withoutGlobalScope(OwnedEntityScope::class);
                };
            }
        }
        if (!empty($withCount)) $builder->withCount($withCount);
        if ($userId = Auth::id()) {
            // sdílené kategorie jako první
            $builder->orderByRaw('user_id = ?', [$userId]);
        }
        $builder->orderBy('name');
        return $builder->get();
    }
    
    // použije se, pokud nepoužíváme klasický route model binding
    public function getBySlug($slug, bool $signed = false)
    {
        $model = static::MODEL_CLASS;
        $query = $model::query();
        if ($signed) $query->withoutGlobalScope(OwnedEntityScope::class);
        if (is_numeric($slug)) $query->where('id', $slug);
        else $query->where('slug', $slug);
        return $query->firstOrFail();
    }
    
}
