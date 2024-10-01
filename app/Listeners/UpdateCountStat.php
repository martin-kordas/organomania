<?php

namespace App\Listeners;

use App\Services\RuntimeStatsService;
use App\Interfaces\EntityEvent;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Events\EntityCreated;
use App\Events\EntityDeleted;

class UpdateCountStat
{
    public function __construct(private RuntimeStatsService $statsService)
    { }

    public function handle(EntityCreated|EntityDeleted $event): void
    {
        $model = $event->getModel();
        
        // do statistik patří jen nesoukromé záznamy
        if (!$model->user_id) {
            $amount = $event->getAmountDiff();
            if ($amount !== 0) {
                $increment = match (get_class($model)) {
                    Organ::class => $this->statsService->incrementOrganCount(...),
                    OrganBuilder::class => $this->statsService->incrementOrganBuilderCount(...),
                    default => throw new \LogicException,
                };
                $increment($amount);
            }
        }
    }
}
