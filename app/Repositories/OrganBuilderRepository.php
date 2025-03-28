<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\OrganBuilderCategory as OrganBuilderCategoryModel;
use App\Models\OrganBuilderCustomCategory as OrganBuilderCustomCategoryModel;
use App\Models\OrganBuilder;
use App\Repositories\AbstractRepository;

class OrganBuilderRepository extends AbstractRepository
{
    
    const
        ORGAN_BUILDERS_WITH = [
            'organs',
            'organBuilderCategories', 'organBuilderCustomCategories',
            'region',
        ],
        ORGAN_BUILDERS_WITH_COUNT = ['organs', 'organRebuilds', 'likes', 'organBuilderCustomCategories'],
        CATEGORIES_WITH_COUNT = ['organBuilders'],
        CUSTOM_CATEGORIES_WITH_COUNT = ['organBuilders'];
    
    protected const MODEL_CLASS = OrganBuilder::class;
    
    public function getOrganBuildersQuery(
        array $filters = [], array $sorts = [],
        $with = self::ORGAN_BUILDERS_WITH, $withCount = self::ORGAN_BUILDERS_WITH_COUNT
    ): Builder
    {
        $query = OrganBuilder::query()->select('*')->inland();

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        $filterNear = false;
        
        foreach ($filters as $field => $value) {
            $value = $this->trimFilterValue($value);
            
            switch ($field) {
                case 'name':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->where('workshop_name', 'like', "%$value%")
                            ->orWhereRaw('
                                CONCAT(
                                    IFNULL(first_name, ""),
                                    " ",
                                    IFNULL(last_name, "")
                                ) LIKE ?
                            ', ["%$value%"]);
                    });
                    break;
                    
                case 'municipality':
                    $this->filterLike($query, $field, $value);
                    break;
                
                case 'id':
                case 'regionId':
                case 'importance':
                case 'isFavorite':
                case 'isPrivate':
                    $this->filterEntityQuery($query, $field, $value);
                    break;
                
                case 'nearLongitude':
                case 'nearLatitude':
                case 'nearDistance':
                    if ($field === 'nearLongitude' && isset($filters['nearLatitude'], $filters['nearDistance'])) {
                        $this->filterNear($query, $filters['nearLatitude'], $filters['nearLongitude'], $filters['nearDistance']);
                        $filterNear = true;
                    }
                    break;
                
                default:
                    throw new \LogicException;
            }
            
        }

        foreach ($sorts as $field => $direction) {
            switch ($field) {
                case 'name':
                    $query->orderByName($direction);
                    break;
                
                case 'importance':
                    // současné varhanáře na konec
                    $query->orderByRaw('active_from_year >= 1989');
                    $this->orderBy($query, $field, $direction);
                    break;
                
                case 'organs_count':
                    $query->orderByRaw("organs_count + organ_rebuilds_count $direction");
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
        
        $query->orderByName();
        $query->orderBy('id');
        
        return $query;
    }
    
    public function getOrganBuilders(
        array $filters = [], array $sorts = [],
        $with = self::ORGAN_BUILDERS_WITH, $withCount = self::ORGAN_BUILDERS_WITH_COUNT,
        $perPage = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->getOrganBuildersQuery($filters, $sorts, $with, $withCount);
        if ($perPage === false) return $query->get();
        else return $query->paginate($perPage);
    }
    
    public function getCategories(
        $withCount = self::CATEGORIES_WITH_COUNT,
    )
    {
        return $this->getCategoriesHelp(new OrganBuilderCategoryModel, $withCount);
    }
    
    public function getCustomCategories(
        $withCount = self::CUSTOM_CATEGORIES_WITH_COUNT,
        $allowIds = []
    ): Collection
    {
        $mainEntityRelation = 'organBuilders';
        return $this->getCustomCategoriesHelp(new OrganBuilderCustomCategoryModel, $mainEntityRelation, $withCount, $allowIds);
    }
    
}
