<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganCustomCategory;
use App\Models\Like;
use App\Models\Stats;
use App\Mail\StatsCollected;
use App\Helpers;

class CollectStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:collect-stats
                            {--db : Write stats to database}
                            {--mailto=* : Send stats to given emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Computes stats about application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Do you wish to continue?')) {
            $stats = $this->getStats();
            $this->table(array_keys($stats), [array_values($stats)]);
            
            $mails = $this->option('mailto');
            $validator = Validator::make(['mails' => $mails], [
                'mails' => 'array|distinct',
                'mails.*' => 'email',
            ]);
            if ($validator->fails()) throw new \Exception('Mails are not valid.');
            
            if ($this->option('db')) {
                $attributes = Helpers::arrayKeysSnake($stats);
                $attributes['computed_on'] = new \DateTime;
                $stats = new Stats($attributes);
                $stats->save();
                
                foreach ($mails as $mail) {
                    Mail::to($mail)->send(new StatsCollected($stats));
                }
            }
            
            $this->info('The command was successful!');
        }
    }
    
    private function getOrganLikesMax()
    {
        return Like::query()
            ->selectRaw('count(id) as likes_count')
            ->selectRaw('likeable_id as organ_id')
            ->where('likeable_type', Organ::class)
            ->groupBy('likeable_id')
            ->orderBy('likes_count', 'desc')
            ->take(1)
            ->first();
    }
    
    private function getLikesCount($likeableType)
    {
        return Like::query()->where('likeable_type', $likeableType)->count();
    }
    
    private function getOrganLikesAvg()
    {
        return Organ::withCount('likes')
            ->whereNull('user_id')
            ->get()
            ->avg('organ_likes_count');
    }
    
    private function getStats()
    {
        $organLikesMax = $this->getOrganLikesMax();
        $organLikesAvg = $this->getOrganLikesAvg();
        
        return [
            'usersCount' => User::count(),
            'organsCount' => Organ::count(),
            'organBuildersCount' => OrganBuilder::count(),
            'organCustomCategoriesCount' => OrganCustomCategory::count(),
            'organLikesCount' => $this->getLikesCount(Organ::class),
            
            'organLikesMax' => $organLikesMax['likes_count'] ?? null,
            'organLikesMaxOrganId' => $organLikesMax['organ_id'] ?? null,
            'organLikesAvg' => $organLikesAvg,
        ];
    }
}