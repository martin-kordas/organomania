<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        
        foreach ($filters as $field => $value) {
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
                case 'importance':
                    $this->filterEntityQuery($query, $field, $value);
                    break;
                
                default:
                    throw new \LogicException;
            }
            
        }

        foreach ($sorts as $field => $direction) {
            switch ($field) {
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