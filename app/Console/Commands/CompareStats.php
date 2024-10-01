<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stats;

class CompareStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compare-stats {id1} {id2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare stats about application stored in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stats1 = Stats::findOrFail($this->argument('id1'));
        $stats2 = Stats::findOrFail($this->argument('id2'));
        
        $message = sprintf(
            'Comparing stats: %s vs. %s',
            $this->formatDateTime($stats1->computed_on), $this->formatDateTime($stats2->computed_on)
        );
        $this->info($message);
        
        $diff = $this->diffStats($stats1, $stats2);
        $this->table(array_keys($diff), [array_values($diff)]);
    }
    
    private function formatDateTime(\DateTime $dateTime)
    {
        return $dateTime->format('d-m-Y H:i:s');
    }
    
    private function diffStats(Stats $stats1, Stats $stats2)
    {
        $diff = [];
        foreach ($stats1->getAttributes() as $field => $value) {
            $value1 = $value ?? 0;
            if (is_numeric($value1)) {
                $value2 = $stats2[$field] ?? 0;
                $diff[$field] = $value2 - $value1;
            }
        }
        return $diff;
    }
}
