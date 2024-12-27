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
    
    public function getFestivalsQuery(
        array $filters = [], array $sorts = [],
        $with = self::FESTIVALS_WITH, $withCount = self::FESTIVALS_WITH_COUNT
    ): Builder
    {
        $query = Festival::query();

        $importanceExpr = '
            CASE
                WHEN importance >= 8 THEN 3
                WHEN importance >= 4 THEN 2
                ELSE 1
            END
        ';
        
        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        
        foreach ($filters as $field => $value) {
            $value = trim($value);
            
            switch ($field) {
                case 'nameLocality':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->where('name', 'LIKE', "%$value%")
                            ->orWhere('locality', 'LIKE', "%$value%")
                            ->orWhere('place', 'LIKE', "%$value%");
                    });
                    break;
                
                case 'regionId':
                    $this->filterEntityQuery($query, $field, $value);
                    break;
                
                case 'importance':
                    $query->whereRaw("$importanceExpr = ?", [$value]);
                    break;
                
                default:
                    throw new \LogicException;
            }
            
        }

        foreach ($sorts as $field => $direction) {
            $directionSql = $direction === 'desc' ? 'DESC' : 'ASC';
            
            switch ($field) {
                // řazení festivalů:
                //  - začínající v aktuálním nebo příštím měsíci
                //  - běžící po celý rok
                //  - začínající v dalších měsících
                case 'starting_month':
                    $currentMonth = (int)date('n');
                    $expr = "
                        IF(
                            starting_month IS NULL,
                            1.5,
                            IF(
                                starting_month >= ?,
                                starting_month - ?,
                                starting_month - ? + 12
                            )
                        ) $directionSql
                    ";
                    $query->orderByRaw($expr, [$currentMonth, $currentMonth, $currentMonth]);
                    break;
                
                case 'importance':
                    $expr = "$importanceExpr $directionSql";
                    $query->orderByRaw($expr);
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
