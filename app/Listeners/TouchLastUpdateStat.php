<?php

namespace App\Listeners;

use App\Services\RuntimeStatsService;
use App\Events\EntityCreated;
use App\Events\EntityUpdated;
use App\Events\EntityDeleted;

class TouchLastUpdateStat
{

    public function __construct(private RuntimeStatsService $statsService)
    { }

    public function handle(EntityCreated|EntityUpdated|EntityDeleted $event): void
    {
        $model = $event->getModel();
        
        // do statistik patří jen nesoukromé záznamy
        if (!$model->user_id) {
            $this->statsService->touchLastUpdate();
        }
    }
}
