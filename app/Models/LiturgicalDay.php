<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\KancionalSongCategory;

class LiturgicalDay extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function casts()
    {
        return [
            'date' => 'date',
        ];
    }
    
    public function liturgicalCelebrations()
    {
        return $this->hasMany(LiturgicalCelebration::class);
    }
    
    public function worshipSongs()
    {
        return $this->hasMany(WorshipSong::class, 'date', 'date')
            ->orderBy('date', 'desc')
            ->orderBy('time')
            ->orderBy('id');
    }
    
    public function isToday()
    {
        return $this->date->equalTo(today());
    }
    
    public function isSunday()
    {
        return $this->date->dayOfWeekIso === 7;
    }
    
    public function getCalendarUrl()
    {
        return url()->query('http://www.katolik.cz/kalendar/kalendar.asp', [
            'd' => $this->date->day,
            'm' => $this->date->month,
            'r' => $this->date->year
        ]);
    }
    
    public function getSeasonLocalized()
    {
        return match ($this->season) {
            'ordinary' => 'liturgické mezidobí',
            'advent' => 'adventní',
            'christmas' => 'vánoční',
            'lent' => 'postní',
            'easter' => 'velikonoční',
            default => throw new \RuntimeException,
        };
    }
    
    public function getPreferredKancionalSongCategory()
    {
        return match ($this->season) {
            'ordinary' => KancionalSongCategory::General,
            'advent' => KancionalSongCategory::Advent,
            'christmas' => KancionalSongCategory::Christmas,
            'lent' => KancionalSongCategory::Lent,
            'easter' => KancionalSongCategory::Easter,
            default => throw new \RuntimeException,
        };
    }
    
    public function getWorshipSongsGroups()
    {
        return $this->worshipSongs->groupBy('time');
    }
    
    public static function getMaxDisplayedDate()
    {
        return today()
            ->add(3, 'month')
            ->endOfWeek(Carbon::SUNDAY)
            ->startOfDay();
    }
    
    public static function getMaxDate()
    {
        static $date;
        if (!isset($date)) {
            $date = static::orderBy('date', 'desc')->first()?->date;
            $date = $date->min(static::getMaxDisplayedDate());
        }
        return $date;
    }
    
    public static function getMinDate()
    {
        static $date;
        $date ??= static::orderBy('date')->first()?->date;
        return $date;
    }
    
    public static function getMaxDateSunday()
    {
        static $date;
        if (!isset($date)) {
            $date = static
                ::orderBy('date', 'desc')
                ->whereRaw('WEEKDAY(date) = 6')
                ->where('date', '<=', static::getMaxDisplayedDate())
                ->first()
                ?->date;
        }
        return $date;
    }
    
    public static function getToday()
    {
        static $day;
        $day ??= static::where('date', today())->first();
        return $day;
    }
    
}
