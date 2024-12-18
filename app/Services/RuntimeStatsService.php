<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Organ;
use App\Models\OrganBuilder;

class RuntimeStatsService
{
    
    public function forget()
    {
        Cache::forget('runtimeStats.organCount');
        Cache::forget('runtimeStats.organBuilderCount');
        Cache::forget('runtimeStats.lastUpdate');
    }
    
    public function getOrganCount()
    {
        return Cache::rememberForever('runtimeStats.organCount', function () {
            Log::debug('runtimeStats: Computed organCount.');
            return $this->getModelCount(new Organ);
        });
    }
    
    public function getOrganBuilderCount()
    {
        return Cache::rememberForever('runtimeStats.organBuilderCount', function () {
            Log::debug('runtimeStats: Computed organBuilderCount.');
            return $this->getModelCountQuery(new OrganBuilder)->inland()->count();
        });
    }
    
    public function incrementOrganCount($amount)
    {
        Log::debug('runtimeStats: Incremented organCount.', [compact('amount')]);
        $this->incrementModelCount('runtimeStats.organCount', $amount);
    }
    
    public function incrementOrganBuilderCount($amount)
    {
        Log::debug('runtimeStats: Incremented organBuilderCount.', [compact('amount')]);
        $this->incrementModelCount('runtimeStats.organBuilderCount', $amount);
    }
    
    public function getLastUpdate()
    {
        return Cache::rememberForever('runtimeStats.lastUpdate', function () {
            Log::debug('runtimeStats: Computed lastUpdate.');
            return collect([new Organ, new OrganBuilder])
                ->map($this->getModelLastUpdate(...))
                ->filter()
                ->max() ?? new \DateTime;
        });
    }
    
    public function touchLastUpdate()
    {
        Log::debug('runtimeStats: Touched lastUpdate.');
        Cache::set('runtimeStats.lastUpdate', new \DateTime);
    }
    
    private function getModelCountQuery(Model $model)
    {
        return $model->newQuery()->public();
    }
    
    private function getModelCount(Model $model)
    {
        return $this->getModelCountQuery($model)->count();
    }
    
    private function incrementModelCount($key, $amount)
    {
        if (Cache::has($key)) {
            Cache::increment($key, $amount);
        }
    }
    
    private function getModelLastUpdate(Model $model)
    {
        return $model->newQuery()
            ->latest('updated_at')
            ->first()
            ?->updated_at;
    }
    
}
