<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\OrganCategory;
use App\Models\Organ;
use App\Models\OrganBuilder;
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
    
    protected const MODEL_CLASS = Organ::class;
    
    const SESSION_KEY_LAST_VIEWED_ORGANS = 'organs.repository.last-viewed-organs';
    
    public function getOrgansQuery(
        array $filters = [], array $sorts = [],
        $with = self::ORGANS_WITH, $withCount = self::ORGANS_WITH_COUNT
    ): Builder
    {
        $query = Organ::query()->select('*');

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);
        $filterNear = false;
        
        foreach ($filters as $field => $value) {
            $value = $this->trimFilterValue($value);
            
            switch ($field) {
                case 'locality':
                    $query->whereAny(['place', 'municipality'], 'like', "%$value%");
                    break;
                
                case 'disposition':
                    $query->where(function (Builder $query) use ($field, $value) {
                        $this->filterLike($query, $field, $value);
                        $query->orWhereFulltext($field, $value);
                    });
                    
                    //  - fulltextové hledání zabraňuje nalezení přesného výskytu (např. pro "Kryt jemný" najde i dispozice, kde je jen "Kryt")
                    //  - dispozice s přesným výskytem proto řadíme přednostně, fulltextové shody řadíme dle míry shody
                    //  - uživatelsky nastavené řazení je tím potlačeno, ale aplikuje se jako dodatečné kritérium
                    $query->selectRaw('disposition LIKE ? AS disposition_filter_like', ["%$value%"]);
                    $query->orderByRaw('disposition_filter_like DESC');
                    $query->orderByRaw('
                        IF(
                            disposition_filter_like,
                            0,
                            MATCH(disposition) AGAINST (? IN NATURAL LANGUAGE MODE)
                        ) DESC
                    ', [$value]);
                    break;
                
                case 'manualsCount':
                    $query->whereIn('manuals_count', $value);
                    break;
                
                case 'organBuilderId':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->where('organ_builder_id', $value)
                            ->orWhereHas('organRebuilds', function (Builder $query) use ($value) {
                                $query->where('organ_builder_id', $value);
                            });
                    });
                    break;
                
                case 'caseOrganBuilderId':
                    $query->whereRaw('IFNULL(case_organ_builder_id, organ_builder_id) = ?', $value);
                    break;
                
                case 'preservedCase':
                case 'preservedOrgan':
                case 'concertHall':
                    $column = str($field)->snake();
                    $query->where($column, $value ? 1 : 0);
                    break;
                
                case 'foreignOrganBuilder':
                    $query->whereHas('organBuilder', function (Builder $query) {
                        $query
                            ->whereNull('region_id')
                            ->where('id', '!=', OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED);
                    });
                    break;
                
                case 'hasDisposition':
                    $query->where(function (Builder $query) {
                        $query
                            ->whereNotNull('disposition')
                            ->orWhereHas('dispositions');
                    });
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
                
                case 'original_stops_count':
                    $query->orderByRaw("IFNULL(original_stops_count, stops_count) $direction");
                    break;
                
                case 'manuals_count':
                    $this->orderBy($query, $field, $direction);
                    $this->orderBy($query, 'stops_count', $direction);
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
            ->where(function (Builder $query) {
                $query
                    ->where('importance', '>=', 5)
                    ->orWhereNotNull(['outside_image_url', 'disposition']);
            })
            ->where('importance', '>', 0)
            ->whereNotNull(['description', 'image_url'])
            ->public()
            ->inRandomOrder()
            ->take(1)
            ->first();
    }
    
    public function getSimilarOrgans(Organ $organ)
    {
        $categories = $organ->organCategories->map(
            fn (OrganCategoryModel $category) => $category->getEnum()
        );
        
        // přestavované varhany jsou specifické, podobné varhany k nim nedohledáváme
        if ($organ->organRebuilds->isNotEmpty()) return collect();

        // nemají-li varhany aspoň 2 technické kategorie, jde o chybějící informace a podobné varhany nejde určit
        $technicalCategories = $categories->filter(
            fn (OrganCategory $category) => $category->isTechnicalCategory()
        );
        if ($technicalCategories->count() < 2) return collect();
        
        // čím starší varhany, tím větší rozptyl roku postavení
        $yearRange = match (true) {
            $organ->year >= 1800 => 30,
            $organ->year >= 1900 => 25,
            $organ->year >= 1945 => 20,
            default => 35
        };

        // relativní rozptyl v počtu rejstříku zavádíme kvůli velkým varhanám, kde absolutní rozdíl 5 rejstříků není signifikantní
        $stopsAbsoluteRange = 5;
        $stopsRelativeRange = $organ->stops_count * 0.2;
        $stopsRange = max($stopsAbsoluteRange, $stopsRelativeRange);
        
        // kategorie největší/nejstarší a kategorie období se nemusí shodovat
        $categoryIds = $categories
            ->filter(
                fn (OrganCategory $category) => !$category->isExtraordinaryCategory() && !$category->isPeriodCategory()
            )
            ->map(
                fn (OrganCategory $category) => $category->value
            );
        
        return Organ::query()
            ->where('id', '!=', $organ->id)
            ->where('manuals_count', $organ->manuals_count)
            ->whereBetween('stops_count', [
                $organ->stops_count - $stopsRange, $organ->stops_count + $stopsRange
            ])
            ->whereBetween('year_built', [
                $organ->year_built - $yearRange, $organ->year_built + $yearRange
            ])
            // je-li počet kategorií stejný jako počet požadovaných kategorií, jsou všechny kategorie přítomny
            ->whereHas('organCategories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            }, '=', $categoryIds->count())
            ->whereDoesntHave('organRebuilds')
            ->where('baroque', 0)
            ->public()
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->sortBy('year_built');
    }
    
    public function getOrganInMunicipalityCount(string $municipality)
    {
        return Organ::query()
            ->where('municipality', $municipality)
            ->where('baroque', 0)
            ->public()
            ->count();
    }
    
    public static function logLastViewedOrgan(Organ $organ)
    {
        $organIds = static::getLastViewedOrganIds()->toArray();
        
        $organIds = array_diff($organIds, [$organ->id]);
        if (count($organIds) >= 6) array_pop($organIds);
        array_unshift($organIds, $organ->id);
        
        session([static::SESSION_KEY_LAST_VIEWED_ORGANS => $organIds]);
    }
    
    public static function getLastViewedOrganIds()
    {
        $organIds = session(static::SESSION_KEY_LAST_VIEWED_ORGANS, []);
        return collect($organIds);
    }
    
}
