<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Pitch;
use App\Enums\RegisterCategory;
use App\Enums\DispositionLanguage;
use App\Models\Pitch as PitchModel;
use App\Models\RegisterCategory as RegisterCategoryModel;
use App\Helpers;

class Register extends Model
{
    use HasFactory;
    
    private $dispositions;
    
    public function registerCategories()
    {
        return $this->belongsToMany(RegisterCategoryModel::class)->orderBy('id');
    }
    
    public function getCategoriesNames()
    {
        return $this->registerCategories->map(
            fn(RegisterCategoryModel $category) => $category->getEnum()->getName()
        );
    }
    
    public function registerPitches()
    {
        return $this->belongsToMany(PitchModel::class, 'register_pitch')->orderBy('id');
    }
    
    public function getPitchesLabels(DispositionLanguage $language)
    {
        return $this->registerPitches->map(
            fn(PitchModel $pitch) => $pitch->getEnum()->getLabel($language)
        );
    }
    
    public function registerNames()
    {
        return $this->hasMany(RegisterName::class)->orderBy('language');
    }
    
    public function paletteRegisters()
    {
        return $this->hasMany(PaletteRegister::class);
    }
    
    public function getDispositions($excludeDispositionIds = [], $excludeOrganIds = [])
    {
        $registerNameIds = $this->registerNames->pluck('id');
        if ($registerNameIds->isNotEmpty()) {
            $dispositionsIds = DispositionRegister::query()
                ->with(['keyboard:id,disposition_id'])
                ->select(['keyboard_id'])
                ->whereIn('register_name_id', $registerNameIds)
                ->get()
                ->pluck('keyboard.disposition_id')
                ->unique();

            return Disposition::query()
                ->whereIn('id', $dispositionsIds)
                ->when(!empty($excludeDispositionIds), function (Builder $query) use ($excludeDispositionIds) {
                    $query->whereNotIn('id', $excludeDispositionIds);
                })
                ->when(!empty($excludeOrganIds), function (Builder $query) use ($excludeOrganIds) {
                    $query->whereNotIn('organ_id', $excludeOrganIds);
                })
                ->orderBy('name')
                ->get()
                ->unique('organ_id')
                ->take(5);
        }
        return collect();
    }
    
    protected function pitch(): Attribute
    {
        return Helpers::makeEnumAttribute('pitch_id', Pitch::from(...));
    }
    
    protected function registerCategory(): Attribute
    {
        return Helpers::makeEnumAttribute('register_category_id', RegisterCategory::from(...));
    }
    
    public function isMixture()
    {
        // ideálně by se měly posuzovat registerNames, ale to je datově náročnější
        return
            $this->registerCategory === RegisterCategory::Mixed
            && in_array($this->name, [
                'Mixtura', 'Mixtura minor', 'Mixtura major', 'Mixtur',
                'Fourniture',
                'Cimbál', 'Cimbal', 'Cymballe',
                'Akuta', 'Scharf', 'Scharff',
                'Harmonia aetherea', 'Progressio harmonica',
            ]);
    }
    
    public function getNameInPreferredLanguage(?DispositionLanguage $language = null, $strict = false)
    {
        $registerName = $this->registerNames->firstWhere('language', $language);
        if (!$registerName) {
            if ($strict) return null;
            else $registerName = $this->registerNames->first();
        }
        return $registerName->name;
    }
}
