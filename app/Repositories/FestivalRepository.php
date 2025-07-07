<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Festival;
use App\Repositories\AbstractRepository;

class FestivalRepository extends AbstractRepository
{
    
    const
        FESTIVALS_WITH = [
            'organ', 'region',
        ],
        FESTIVALS_WITH_COUNT = [];
    
    protected const MODEL_CLASS = Festival::class;
    
    public function getFestivalsQuery(
        array $filters = [], array $sorts = [],
        $with = self::FESTIVALS_WITH, $withCount = self::FESTIVALS_WITH_COUNT
    ): Builder
    {
        $query = Festival::query()->select('*');

        $importanceExpr = '
            CASE
                WHEN importance >= 8 THEN 3
                WHEN importance >= 4 THEN 2
                ELSE 1
            END
        ';
        
        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        $filterNear = false;
        
        foreach ($filters as $field => $value) {
            $value = $this->trimFilterValue($value);
            
            switch ($field) {
                case 'nameLocality':
                    $query->whereAny(['name', 'locality', 'place'], 'LIKE', "%$value%");
                    break;
                
                case 'id':
                case 'regionId':
                    $this->filterEntityQuery($query, $field, $value);
                    break;
                
                case 'importance':
                    $query->whereRaw("$importanceExpr = ?", [$value]);
                    break;
                
                case 'nearLongitude':
                case 'nearLatitude':
                case 'nearDistance':
                    if ($field === 'nearLongitude' && isset($filters['nearLatitude'], $filters['nearDistance'])) {
                        $this->filterNear($query, $filters['nearLatitude'], $filters['nearLongitude'], $filters['nearDistance']);
                        $filterNear = true;
                    }
                    break;
                    
                case 'month':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->whereNull('starting_month')
                            ->orWhereRaw('? BETWEEN starting_month AND ending_month', [$value]);
                    });
                    break;
                
                default:
                    throw new \LogicException;
            }
            
        }

        foreach ($sorts as $field => $direction) {
            $directionSql = $direction === 'desc' ? 'DESC' : 'ASC';
            
            switch ($field) {
                // řazení festivalů podle období konání
                case 'starting_month':
                    $currentMonth = (int)date('n');
                    $expr = "
                        CASE
                            -- 1) aktuálně probíhající, řazené dle měsíce začátku
                            -- - TODO: nefunguje pro festivaly probíhající na přelomu roku (např. v měsících listopad-únor)
                            WHEN ? BETWEEN starting_month AND ending_month THEN starting_month
                            -- 2) probíhající v příštím měsíci
                            WHEN starting_month - ? = 1 THEN 10
                            -- 3) probíhající po celý rok
                            WHEN starting_month IS NULL THEN 20
                            -- 4) zbylé, řazené dle měsíce začátku
                            ELSE 20 + IF(
                                starting_month >= ?,
                                starting_month - ?,
                                starting_month - ? + 12
                            )
                        END $directionSql
                    ";
                    $bindings = array_fill(0, 5, $currentMonth);
                    $query->orderByRaw($expr, $bindings);
                    break;
                
                case 'importance':
                    $expr = "$importanceExpr $directionSql";
                    $query->orderByRaw($expr);
                    break;
                
                case 'distance':
                    if ($filterNear) {
                        $this->orderBy($query, $field, $direction);
                    }
                    break;
                
                default:
                    $this->orderBy($query, $field, $direction);
            }
        }
        
        $query->orderBy('name');
        $query->orderBy('id');
        
        return $query;
    }
    
    public function getFestivals(
        array $filters = [], array $sorts = [],
        $with = self::FESTIVALS_WITH, $withCount = self::FESTIVALS_WITH_COUNT,
        $perPage = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->getFestivalsQuery($filters, $sorts, $with, $withCount);
        if ($perPage === false) return $query->get();
        else return $query->paginate($perPage);
    }
    
}
