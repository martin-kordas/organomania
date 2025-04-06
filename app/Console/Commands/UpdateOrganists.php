<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Organist;
use App\Services\YoutubeService;

class UpdateOrganists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-organists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update organists\' Youtube channel information using Youtube API';
    
    protected YoutubeService $youtube;

    /**
     * Execute the console command.
     */
    public function handle(YoutubeService $youtube)
    {
        $this->youtube = $youtube;
        
        $okCount = $errorCount = 0;
        
        Organist::orderBy('id')->get()->each(function (Organist $organist) use (&$okCount, &$errorCount) {
            try {
                $this->updateOrganist($organist);
                $okCount++;
            }
            catch (\Exception $ex) {
                $this->error("Chyba při aktualizaci varhaníka (channel_id: {$organist->channel_id})");
                $errorCount++;
            }
        });
        
        $this->info("Aktualizace varhaníků skončila (počet aktualizovaných: $okCount, počet chybných: $errorCount)");
    }
    
    private function updateOrganist(Organist $organist)
    {
        $lastVideo = $this->youtube->getChannelLastVideo($organist->channel_id);
        if (isset($lastVideo)) $lastVideoDate = new Carbon($lastVideo->getPublishedAt());

        $organist->subscribers_count = $this->youtube->getChannelSubscriberCount($organist->channel_id);
        $organist->videos_count = $this->youtube->getChannelVideoCount($organist->channel_id);
        
        //  - pokud se uložené video liší od posledního videa v kanálu, které je však již staršího data, uložené video posledním videem NEPŘEPÍŠEME
        //  - patrně jde totiž o případ RUČNĚ PŘEPSANÉHO VIDEA (poslední video v kanálu bylo obsahově irelevantní, proto bylo v db. ručně přepsáno starším relevantním)
        if (!isset($organist->last_video_id) || $lastVideoDate && $lastVideoDate >= today()->subDay()) {
            $organist->last_video_date = $lastVideoDate?->format('Y-m-d');
            $organist->last_video_name = $lastVideo?->getTitle();
            $organist->last_video_id = $lastVideo?->getResourceId()?->getVideoId();
        }

        if (!isset($organist->avatar_url) || !$organist->localAvatarExists() || $organist->updated_at < now()->subDays(30)) {
            $organist->avatar_url = $this->youtube->getChannelAvatarUrl($organist->channel_id);
            
            // avatar je nutné cachovat lokálně (při opakovaném stahování z Youtube vzniká HTTP 429)
            $avatar = file_get_contents($organist->avatar_url);
            if ($avatar !== false) $organist->saveLocalAvatar($avatar);
        }
        $organist->save();
    }
    
}
