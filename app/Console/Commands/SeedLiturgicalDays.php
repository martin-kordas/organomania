<?php

namespace App\Console\Commands;

use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\LiturgicalDay;
use App\Models\LiturgicalCelebration;

class SeedLiturgicalDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-liturgical-days {dateFrom} {dateTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert days into liturgical calendar';
    
    private PendingRequest $client;
    
    public function __construct()
    {
        $this->client = Http::timeout(15)
            ->connectTimeout(15)
            ->retry(3, 2000);
        
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = CarbonPeriod
            ::since($this->argument('dateFrom'))
            ->until($this->argument('dateTo'));
        
        foreach ($period as $date) {
            $dateFormat = $date->format('Y-m-d');
            $exists = LiturgicalDay::where('date', $date)->exists();
            if ($exists) $this->error("Datum již v databázi existuje. (datum: $dateFormat)");
            else {
                $url = "http://calapi.inadiutorium.cz/api/v0/cs/calendars/czech/{$date->year}/{$date->month}/{$date->day}";
                $res = $this->client->get($url);
                
                if (!$res->successful()) $this->error("Datum se nepodařilo zjistit. (datum: $dateFormat)");
                else {
                    $day = new LiturgicalDay([
                       'date' => $date,
                       'season' => $res['season']
                    ]);
                    $day->save();
                    
                    $celebrations = collect($res['celebrations'])->map(function ($celebration) {
                        return new LiturgicalCelebration([
                            'name' => $celebration['title'],
                            'color' => $celebration['colour'],
                            'rank' => $celebration['rank'],
                        ]);
                    });
                    $day->liturgicalCelebrations()->saveMany($celebrations);
                
                    $this->info("Datum bylo úspěšně vloženo. (datum: $dateFormat)");
                }
            }
        }
    }
    
}
