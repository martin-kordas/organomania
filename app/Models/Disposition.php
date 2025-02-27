<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Observers\DispositionObserver;
use App\Enums\DispositionLanguage;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\OwnedEntity;
use App\Helpers;
use App\Traits\Viewable;

#[ObservedBy([DispositionObserver::class])]
class Disposition extends Model
{
    use HasFactory, SoftDeletes, Sluggable;
    use Viewable;
    use OwnedEntity {
        OwnedEntity::scopeWithUniqueSlugConstraints insteadof Sluggable;
    }
    
    protected $guarded = [];
    
    protected static function booted(): void
    {
        // řešení atributem ScopedBy nefunguje
        static::addGlobalScope(new OwnedEntityScope);
    }
    
    protected function getShowRoute(): string
    {
        return 'dispositions.show';
    }
    
    public function keyboards()
    {
        return $this->hasMany(Keyboard::class)->orderBy('order');
    }
    
    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }
    
    public function registrations()
    {
        return $this->hasMany(Registration::class)->orderBy('name');
    }
    
    public function registrationSets()
    {
        return $this->hasMany(RegistrationSet::class)->latest();
    }
    
    public function manuals()
    {
        return $this->hasMany(Keyboard::class)->where('pedal', 0);
    }
    
    public function realDispositionRegisters()
    {
        return $this->dispositionRegisters()->where('coupler', 0);
    }
    
    public function dispositionRegisters()
    {
        return $this->hasManyThrough(DispositionRegister::class, Keyboard::class);
    }
    
    public function sameOrganDispositions()
    {
        if (!$this->organ) return collect();
        
        return $this->organ->dispositions->filter(
            fn(Disposition $disposition) => $disposition->id !== $this->id
        );
    }
    
    protected function language(): Attribute
    {
        return Helpers::makeEnumAttribute('language', DispositionLanguage::from(...));
    }
    
    public function getKeyboardStartNumbers()
    {
        $number = 1;
        $numbers = [];
        foreach ($this->keyboards as $keyboard) {
            $numbers[$keyboard->id] = $number;
            $number += count($keyboard->dispositionRegisters);
        }
        return $numbers;
    }
    
    public function getProposedCouplers(Keyboard $keyboard)
    {
        $couplers = [];
        foreach ($this->keyboards as $keyboard1) {
            if ($keyboard1->order !== $keyboard->order && !$keyboard1->pedal) {
                // spojují se manuály s vyšším pořadím k manuálům s nižším pořadím
                if ($keyboard->pedal || $keyboard1->order > $keyboard->order) {
                    $from = $keyboard1->getAbbrev();
                    $to = $keyboard->getAbbrev();
                    $couplers[] = "$from/$to";
                }
            }
        }
        return $couplers;
    }
    
    public function getMinMaxPitchRegister($max = false): ?DispositionRegister
    {
        $cmp = fn($val1, $val2)
            => $max ? $val1 > $val2 : $val1 < $val2;
        
        $foundRegister = null;
        foreach ($this->realDispositionRegisters as $register) {
            if ($register->pitch) {
                if (
                    $foundRegister === null
                    || $cmp($register->pitch->getAliquoteOrder(), $foundRegister->pitch->getAliquoteOrder())
                ) {
                    $foundRegister = $register;
                }
            }
        }
        return $foundRegister;
    }
    
    public function getMinPitchRegister(): ?DispositionRegister
    {
        return $this->getMinMaxPitchRegister();
    }
    
    public function getMaxPitchRegister(): ?DispositionRegister
    {
        return $this->getMinMaxPitchRegister(max: true);
    }
    
    public function getDispositionRegisterKeyboard(DispositionRegister $register)
    {
        return $this->keyboards->first(
            fn(Keyboard $keyboard) => $keyboard->dispositionRegisters->contains($register)
        );
    }
    
    public function getDeclinedRealDispositionRegisters()
    {
        $count = $this->real_disposition_registers_count;
        return __(Helpers::declineCount($count, 'rejstříků', 'rejstřík', 'rejstříky'));
    }
    
    public function isEmpty()
    {
        return $this->keyboards->isEmpty();
    }
    
    public function toStructuredArray()
    {
        $keyboardStartNumbers = $this->getKeyboardStartNumbers();
        
        $keyboards = [];
        foreach ($this->keyboards as $keyboard) {
            $number = $keyboardStartNumbers[$keyboard->id];
            $registers = $keyboard->dispositionRegisters->map(
                function(DispositionRegister $register) use (&$number) {
                    return [
                        'id' => $register->id,
                        'number' => $this->numbering ? $number++ : null,
                        'name' => $register->realName,
                        'coupler' => (bool)$register->coupler,
                        'multiplier' => $register->multiplier,
                        'pitch' => $register->pitch?->getLabel($this->language),
                    ];
                }
            )->toArray();
            
            $keyboard = [
                'number' => $this->keyboard_numbering ? $keyboard->getNumber() : null,
                'name' => $keyboard->name,
                'registers' => $registers
            ];
            $keyboards[] = $keyboard;
        }
        
        return [
            'name' => $this->name,
            'keyboards' => $keyboards,
            'appendix' => $this->appendix,
            'description' => $this->description,
        ];
    }
    
    public function toCsvArray()
    {
        $array = [];
        foreach ($this->toStructuredArray()['keyboards'] as $keyboard) {
            foreach ($keyboard['registers'] as $register) {
                $array[] = [
                    'keyboardNumber' => $keyboard['number'],
                    'keyboardName' => $keyboard['name'],
                    ...$register,
                ];
            }
        }
        return $array;
    }
    
    public function toSimpleArray()
    {
        $array = [];
        foreach ($this->toStructuredArray()['keyboards'] as $keyboard) {
            $keyboardName = '';
            if (isset($keyboard['number'])) $keyboardName .= "{$keyboard['number']} ";
            $keyboardName .= $keyboard['name'];
            
            $registers = array_map(function ($register) {
                $registerName = $register['name'];
                if (isset($register['multiplier'])) $registerName .= " {$register['multiplier']}x";
                if (isset($register['pitch'])) $registerName .= " {$register['pitch']}";
                return $registerName;
            }, $keyboard['registers']);
            
            $keyboard = ['name' => $keyboardName, 'stops' => $registers];
            $array[] = $keyboard;
        }
        return $array;
    }
    
    public function toPlaintext($numbering = true, $registerIds = false, &$rowNumberDispositionRegisterId = null)
    {
        $rowNumberDispositionRegisterId = [];
        $rows = [];
        foreach ($this->toStructuredArray()['keyboards'] as $keyboard) {
            $keyboardRow = '';
            if (isset($keyboard['number'])) $keyboardRow .= "{$keyboard['number']}. ";
            $keyboardRow .= $keyboard['name'];
            $rows[] = "**$keyboardRow**";
            
            foreach ($keyboard['registers'] as $register) {
                $registerRow = '';
                if (isset($register['number']) && $numbering) $registerRow .= "{$register['number']}. ";
                if ($register['multiplier'] != '') $register['multiplier'] .= '×';
                $registerData = array_filter([$register['name'], $register['multiplier'], $register['pitch']]);
                $registerRow .= implode(' ', $registerData);
                if ($registerIds) $registerRow .= " [{$register['id']}]";
                $rows[] = $registerRow;
                $rowNumberDispositionRegisterId[array_key_last($rows) + 1] = $register['id'];
            }
            
            $rows[] = '';
        }
        array_pop($rows);
        return implode("\n", $rows);
    }
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['private_prefix', 'name']
            ]
        ];
    }
    
}
