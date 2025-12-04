<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Models\Organ;
use App\Models\OrganBuilderTimelineItem;
use App\Helpers;

class AnniversariesService
{

    const int DEFAULT_STEP = 10;

    const int SHOWED_COUNT_STEP = 50;

    private function getOrgans(int $year, int $step) {
        return Organ::query()
            ->where(function (Builder $query) use ($step, $year) {
                $query
                    ->yearAnniversary('year_built', $step, $year)
                    ->orYearAnniversary('case_year_built', $step, $year);
            })
            ->where('importance', '>', 0)
            ->public()
            ->get()
            ->map(function (Organ $organ) use ($step, $year) {
                return $this->fillOrganAnniversaries($organ, $year, $step);
            })
            ->sortByDesc('anniversaryYears');
    }

    private function fillOrganAnniversaries(Organ $organ, int $year, int $step)
    {
        $anniversaries = [];
        $anniversaryYears = null;

        $caseYearBuiltAnniversary = $year - $organ->case_year_built;
        if ($caseYearBuiltAnniversary % $step === 0) {
            $anniversaries[] = [$organ->case_year_built, $caseYearBuiltAnniversary, __('let od') . ' ' . __('postavení skříně')];
            $anniversaryYears = min($anniversaryYears ?? INF, $caseYearBuiltAnniversary);
        }

        $yearBuiltAnniversary = $year - $organ->year_built;
        if ($yearBuiltAnniversary % $step === 0) {
            $anniversaries[] = [$organ->year_built, $yearBuiltAnniversary, __('let od') . ' ' . __('postavení')];
            $anniversaryYears = min($anniversaryYears ?? INF, $yearBuiltAnniversary);
        }

        $organ->anniversaries = $anniversaries;
        $organ->anniversaryYears = $anniversaryYears;
        return $organ;
    }

    private function getTimelineItems(int $year, int $step) {
        return OrganBuilderTimelineItem::query()
            ->with('organBuilder')
            ->where(function (Builder $query) use ($step, $year) {
                $query
                    ->yearAnniversary('year_from', $step, $year)
                    ->orYearAnniversary('year_to', $step, $year);
            })
            ->whereHas('organBuilder', function (Builder $query) {
                $query
                    ->whereNull('user_id')
                    ->where('importance', '>', 0);
            })
            // vyřadit nepřesné datace
            ->where(function (Builder $query) {
                $query
                    ->whereNull('active_period')
                    ->orWhere('active_period', 'not like', '%?%');
            })
            ->where('is_workshop', 0)
            ->get()
            ->map(function (OrganBuilderTimelineItem $timelineItem) use ($step, $year) {
                return $this->fillTimelineItemAnniversaries($timelineItem, $year, $step);
            })
            ->sortByDesc('anniversaryYears');
    }

    private function fillTimelineItemAnniversaries(OrganBuilderTimelineItem $timelineItem, int $year, int $step)
    {
        $anniversaries = [];
        $anniversaryYears = null;

        $yearFromAnniversary = $year - $timelineItem->year_from;
        if ($yearFromAnniversary % $step === 0) {
            $anniversaries[] = [$timelineItem->year_from, $yearFromAnniversary, __('let od') . ' ' . __('narození')];
            $anniversaryYears = min($anniversaryYears ?? INF, $yearFromAnniversary);
        }

        $yearToAnniversary = $year - $timelineItem->year_to;
        if ($yearToAnniversary % $step === 0) {
            $anniversaries[] = [$timelineItem->year_to, $yearToAnniversary, __('let od') . ' ' . __('úmrtí')];
            $anniversaryYears = min($anniversaryYears ?? INF, $yearToAnniversary);
        }

        $timelineItem->anniversaries = $anniversaries;
        $timelineItem->anniversaryYears = $anniversaryYears;
        return $timelineItem;
    }

    public function getAnniversaries(?int $year = null, int $step = self::DEFAULT_STEP)
    {
        $year ??= now()->year;
        
        $anniversaries = [];

        $anniversaries['organs'] = $this->getOrgans($year, $step);
        $anniversaries['timelineItems'] = $this->getTimelineItems($year, $step);
        $anniversaries['count'] = $anniversaries['organs']->count() + $anniversaries['timelineItems']->count();

        return $anniversaries;
    }

    public function getCachedAnniversaryCount()
    {
        $firstJanuaryNextYear = Carbon::create(
            now()->year + 1, 1, 1, 0, 0, 0
        );

        return Cache::remember('anniversaryCount', $firstJanuaryNextYear, function () {
            return $this->getAnniversaries(step: static::SHOWED_COUNT_STEP)['count'];
        });
    }
    
}
