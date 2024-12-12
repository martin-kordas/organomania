<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Organ;
use App\Models\OrganCustomCategory as OrganCustomCategoryModel;
use App\Models\OrganCategory as OrganCategoryModel;
use App\Models\Scopes\OwnedEntityScope;
use App\Repositories\AbstractRepository;

class OrganRepository extends AbstractRepository
{
    
    const
        ORGANS_WITH = [
            'organBuilder', 'organRebuilds', 'organRebuilds.organBuilder',
            'organCategories', 'organCustomCategories',
            'region',
        ],
        ORGANS_WITH_COUNT = ['organCustomCategories', 'likes'],
        CATEGORIES_WITH_COUNT = ['organs'],
        CUSTOM_CATEGORIES_WITH_COUNT = ['organs'];
    
    public function getOrgansQuery(
        array $filters = [], array $sorts = [],
        $with = self::ORGANS_WITH, $withCount = self::ORGANS_WITH_COUNT
    ): Builder
    {
        $query = Organ::query();

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        
        foreach ($filters as $field => $value) {
            switch ($field) {
                case 'organBuilderId':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->where('organ_builder_id', $value)
                            ->orWhereHas('organRebuilds', function (Builder $query) use ($value) {
                                $query->where('organ_builder_id', $value);
                            });
                    });
                    break;
                
                case 'concertHall':
                    $query->where('concert_hall', $value ? 1 : 0);
                    break;
                
                case 'foreignOrganBuilder':
                    $query->whereHas('organBuilder', function (Builder $query) {
                        $query->whereNull('region_id');
                    });
                    break;
                
                case 'hasDisposition':
                    $query->where(function (Builder $query) {
                        $query
                            ->whereNotNull('disposition')
                            ->orWhereHas('dispositions');
                    });
                    break;
                    
                case 'regionId':
                case 'importance':
                case 'isFavorite':
                case 'isPrivate':
                    $this->filterEntityQuery($query, $field, $value);
                    break;
                
                default:
                    throw new \LogicException;
            }
            
        }

        foreach ($sorts as $field => $direction) {
            switch ($field) {
                case 'organ_builder':
                    $orderExpression = DB::raw('
                        IF(
                            organ_builders.is_workshop,
                            organ_builders.workshop_name,
                            CONCAT(organ_builders.last_name, organ_builders.first_name)
                        )'
                    );
                    $query
                        ->join('organ_builders', 'organ_builders.id', '=', 'organs.organ_builder_id')
                        ->orderBy($orderExpression, $direction);
                    break;
                
                case 'manuals_count':
                    $this->orderBy($query, $field, $direction);
                    $this->orderBy($query, 'stops_count', $direction);
                    break;
                
                default:
                    $this->orderBy($query, $field, $direction);
            }
        }
        
        $query->orderBy('municipality');
        $query->orderBy('id');
        
        return $query;
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
    
    public function getCategories(
        $withCount = self::CATEGORIES_WITH_COUNT,
    )
    {
        return $this->getCategoriesHelp(new OrganCategoryModel, $withCount);
    }
    
    public function getCustomCategories(
        $withCount = self::CUSTOM_CATEGORIES_WITH_COUNT,
        $allowIds = []
    ): Collection
    {
        $mainEntityRelation = 'organs';
        return $this->getCustomCategoriesHelp(new OrganCustomCategoryModel, $mainEntityRelation, $withCount, $allowIds);
    }
    
    public function getOrganOfDay()
    {
        return Organ::query()
            ->where('importance', '>=', 6)
            ->whereNotNull(['description', 'image_url'])
            ->inRandomOrder()
            ->take(1)
            ->first();
    }
    
}
