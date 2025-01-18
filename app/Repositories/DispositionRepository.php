<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Disposition;
use App\Repositories\AbstractRepository;

class DispositionRepository extends AbstractRepository
{

    protected const MODEL_CLASS = Disposition::class;

}
