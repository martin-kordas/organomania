<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Competition;
use App\Repositories\AbstractRepository;

class CompetitionRepository extends AbstractRepository
{
    
    const
        COMPETITIONS_WITH = [
            'organs', 'region',
        ],
        COMPETITIONS_WITH_COUNT = [];
    
    public function getCompetitionsQuery(
        array $filters = [], array $sorts = [],
        $with = self::COMPETITIONS_WITH, $withCount = self::COMPETITIONS_WITH_COUNT
    ): Builder
    {
        $query = Competition::query();

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
    
    public function getCompetitions(
        array $filters = [], array $sorts = [],
        $with = self::COMPETITIONS_WITH, $withCount = self::COMPETITIONS_WITH_COUNT,
        $perPage = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->getCompetitionsQuery($filters, $sorts, $with, $withCount);
        if ($perPage === false) return $query->get();
        else return $query->paginate($perPage);
    }
    
}
