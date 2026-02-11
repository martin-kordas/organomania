<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicationRepository extends AbstractRepository
{
    protected const MODEL_CLASS = Publication::class;

    const PUBLICATIONS_WITH = ['authors'];
    const PUBLICATIONS_WITH_COUNT = [];

    public function getPublicationsQuery(
        array $filters = [],
        array $sorts = [],
        $with = self::PUBLICATIONS_WITH,
        $withCount = self::PUBLICATIONS_WITH_COUNT
    ): Builder {
        $query = Publication::query()->select('publications.*');

        if (!empty($with)) $query->with($with);
        if (!empty($withCount)) $query->withCount($withCount);

        $query
            ->leftJoin('publication_author', 'publications.id', '=', 'publication_author.publication_id')
            ->leftJoin('authors', 'publication_author.author_id', '=', 'authors.id')
            ->where(function (Builder $query) {
                $query
                    ->whereNull('publication_author.id')
                    ->orWhere('publication_author.order', 1);
            })
            ->select('publications.*');

        foreach ($filters as $field => $value) {
            $value = $this->trimFilterValue($value);

            // TODO: názvy filtrů jsou snake_case namísto obvyklého camelCase
            switch ($field) {
                case 'all':
                    $query->where(function (Builder $query) use ($value) {
                        $query
                            ->whereAny(['publications.name', 'publications.name_cz', 'publications.journal'], 'LIKE', "%$value%")
                            ->orWhereHas('authors', function (Builder $query) use ($value) {
                                $query
                                    ->where('authors.first_name', 'like', "%$value%")
                                    ->orWhere('authors.last_name', 'like', "%$value%");
                            });
                    });
                    break;

                case 'author_id':
                    $query->whereHas('authors', function (Builder $query) use ($value) {
                        $query->where('authors.id', $value);
                    });
                    break;

                case 'publication_type_id':
                case 'publication_topic_id':
                case 'language':
                case 'journal':
                    $this->filter($query, "publications.$field", $value);
                    break;

                case 'regionId':
                    $this->filterEntityQuery($query, $field, $value);
                    break;

                case 'online_only':
                    if ($value) $query->whereNotNull('publications.url');
                    break;

                default:
                    throw new \LogicException;
            }
        }

        foreach ($sorts as $field => $direction) {
            switch ($field) {
                case 'author':
                    $query
                        ->orderBy('authors.last_name', $direction)
                        ->orderBy('authors.first_name', $direction);
                    break;

                default:
                    $this->orderBy($query, $field, $direction);
                    break;
            }
        }

        $query->orderBy('authors.last_name');
        $query->orderBy('authors.first_name');
        $query->orderBy('publications.name');
        $query->orderBy('publications.id');

        return $query;
    }

    public function getPublications(
        array $filters = [],
        array $sorts = [],
        $with = self::PUBLICATIONS_WITH,
        $withCount = self::PUBLICATIONS_WITH_COUNT,
        $perPage = null
    ): Collection|LengthAwarePaginator {
        $query = $this->getPublicationsQuery($filters, $sorts, $with, $withCount);
        if ($perPage === false) {
            return $query->get();
        } else {
            return $query->paginate($perPage);
        }
    }

    public function getImportantAuthors()
    {
        return Author::query()
            ->whereIn('id', [Author::AUTHOR_ID_SEHNAL, Author::AUTHOR_ID_SCHINDLER, Author::AUTHOR_ID_HORAK])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
