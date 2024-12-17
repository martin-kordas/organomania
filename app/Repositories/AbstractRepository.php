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
        switch ($field) {
            case 'regionId':
                $this->filter($query, 'region_id', $value);
                break;

            case 'importance':
                $query->where('importance', '>=', $value * 2 - 1);
                break;

            case 'isFavorite':
                $subFilter = fn(Builder $query) => $query->where('user_id', Auth::id());
                if ($value) $query->whereHas('likes', $subFilter);
                else $query->whereDoesntHave('likes', $subFilter);
                break;

            case 'isPrivate':
                if ($value) $query->whereNotNull('user_id');
                else $query->whereNull('user_id');
                break;
                    
            default:
                throw new \LogicException;
        }
    }
    
    protected function filterNear(Builder $query, float $latitude, float $longitude, float $nearDistance)
    {
        $query->whereRaw('
            ST_DISTANCE_SPHERE(
                POINT(longitude, latitude),
                POINT(?, ?)
            ) <= ?
        ', [$longitude, $latitude, $nearDistance * 1000]);
    }
    
    protected function getCategoriesHelp(
        Model $model,
        $withCount,
    )
    {
        $builder = $model->query();
        if (!empty($withCount)) $builder->withCount($withCount);
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
    
    public function getOrgans(
        array $filters = [], array $sorts = [],
        $with = self::ORGANS_WITH, $withCount = self::ORGANS_WITH_COUNT,
        $perPage = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->getOrgansQuery($filters, $sorts, $with, $withCount);
        if ($perPage === false) return $query->get();
        else return $query->paginate($perPage);
    }
    
    
}
