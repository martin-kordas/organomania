<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organist extends Model
{
    
    use HasFactory, SoftDeletes;
    
    protected function casts()
    {
        return [
            'last_video_date' => 'date',
        ];
    }
    
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    protected function channelUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $_val, array $organist) => "https://www.youtube.com/{$organist['channel_username']}",
        );
    }
    
    protected function lastVideoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $_val, array $organist) => url()->query('https://www.youtube.com/watch', ['v' => $organist['last_video_id']]),
        );
    }
    
    protected function facebookUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $_val, array $organist) => "https://www.facebook.com/{$organist['facebook']}",
        );
    }
    
    protected function instagramUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $_val, array $organist) => "https://www.instagram.com/{$organist['instagram']}",
        );
    }
    
    public function getAvatarRelativeFilename(): string
    {
        return "organist-avatars/{$this->id}";
    }
    
    public function getLocalAvatarUrl(): string
    {
        return Storage::url(
            $this->getAvatarRelativeFilename()
        );
    }
    
    public function saveLocalAvatar($avatar)
    {
        Storage::disk('public')->put(
            $this->getAvatarRelativeFilename(),
            $avatar
        );
    }
    
    public function localAvatarExists(): bool
    {
        return Storage::disk('public')->exists(
            $this->getAvatarRelativeFilename()
        );
    }
    
    public function shouldHighlightLastVideoDate()
    {
        return isset($this->last_video_date) && $this->last_video_date >= today()->subDays(5);
    }
    
    public static function getHighlightedCount()
    {
        return static::query()
            ->whereRaw('last_video_date >= CURDATE() - INTERVAL 5 DAY')
            ->count();
    }
}
