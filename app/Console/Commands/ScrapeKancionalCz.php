<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Song;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeKancionalCz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-kancional-cz {idFrom} {idTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts song purpose into songs table';
    
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
        for ($id = $this->argument('idFrom'); $id <= $this->argument('idTo'); $id++) {
            if ($song = Song::find($id)) {
                $url = "https://kancional.cz/{$song->number}";
                $res = $this->client->get($url);
                if (!$res->successful()) $this->error("Údaje písně se nepodařilo zjistit. (song: {$song->number})");
                else {
                    $crawler = new Crawler($res->body());
                    $nodes = $crawler->filter('main > p');
                    
                    for ($i = $nodes->count() - 1; $i >= 0; $i--) {
                         $node = $nodes->eq($i);
                         $text = str($node->text());
                         if ($text->startsWith('M') && $text->length() < 20) {
                            $purpose = $text->replace(' ', '');
                            $song->purpose = $purpose;
                            $song->save();
                            $this->info("Údaje písně byly úspěšně zapsány. (song: {$song->number})");
                            sleep(1);
                            continue 2;
                         }
                    }
                    
                    sleep(1);
                    $this->error("Údaje písně nebyly nalezeny. (song: {$song->number})");
                }
            }
        }
    }
    
}
