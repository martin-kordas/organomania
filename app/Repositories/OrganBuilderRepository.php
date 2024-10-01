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
        ORGAN_BUILDERS_WITH_COUNT = ['organs', 'likes', 'organBuilderCustomCategories'],
        CATEGORIES_WITH_COUNT = ['organBuilders'],
        CUSTOM_CATEGORIES_WITH_COUNT = ['organBuilders'];
    
    public function getOrganBuildersQuery(
        array $filters = [], array $sorts = [],
        $with = self::ORGAN_BUILDERS_WITH, $withCount = self::ORGAN_BUILDERS_WITH_COUNT
    ): Builder
    {
        $query = OrganBuilder::query()->inland();

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        
        foreach ($filters as $field => $value) {
            switch ($field) {
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
                case 'name':
                    $query->orderByName($direction);
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
