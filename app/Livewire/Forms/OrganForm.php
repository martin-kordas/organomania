<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\Attributes\Locked;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganRebuild;
use App\Enums\OrganCategory;
use App\Rules\UppercaseFirst;
use App\Events\EntityCreated;
use App\Events\EntityUpdated;
use App\Events\EntityDeleted;

class OrganForm extends Form
{
    #[Validate([
        'municipality' => ['required', new UppercaseFirst]
    ], message: [
        'municipality.required' => 'Obec musí být vyplněna.',
    ], attribute: [
        'municipality' => 'Místo'
    ])]
    public $municipality;
    
    #[Validate('required', message: 'Místo musí být vyplněno.')]
    public $place;
    
    #[Validate('required', message: 'Zeměpisná šířka musí být vyplněna.')]
    public $latitude;
    #[Validate('required', message: 'Zeměpisná délka musí být vyplněna.')]
    public $longitude;
    #[Validate('required', message: 'Kraj musí být vyplněn.')]
    public $regionId;
    public $importance;
    #[Validate('required', message: 'Varhanář musí být vyplněn.')]
    public $organBuilderId;
    public $yearBuilt;
    public $stopsCount;
    public $manualsCount;
    #[Validate('nullable')]
    #[Validate('url', message: 'Nebyla zadána platná URL adresa.')]
    public $imageUrl;
    public $imageCredits;
    #[Validate('nullable')]
    #[Validate('url', message: 'Nebyla zadána platná URL adresa.')]
    public $outsideImageUrl;
    public $outsideImageCredits;
    public $web;
    // u webu chceme validovat zvlášť URL na každém řádku, proto řádky převedeme do pole
    #[Locked]
    #[Validate([
        'webArray.*' => ['nullable', 'url'],
    ], message: [
        'webArray.*.url' => 'Nebyla zadána platná URL adresa.'
    ])]
    public $webArray;
    public $perex;
    public $description;
    public $literature;
    public $disposition;
    public $concertHall;
    
    #[Validate([
        'rebuilds.*.organBuilderId' => 'required',
        'rebuilds.*.yearBuilt' => 'required|gte:yearBuilt',
    ], message: [
        'rebuilds.*.organBuilderId' => 'Přestavující varhanář musí být vyplněn.',
        'rebuilds.*.yearBuilt.required' => 'Rok přestavby musí být vyplněn.',
        'rebuilds.*.yearBuilt.gte' => 'Rok přestavby nesmí být dřívější než rok stavby.',
    ])]
    public $rebuilds = [];
    public $categories = [];
    
    public Organ $organ;
    
    private $public;
    
    // TODO: zřejmě se volá dvakrát (automaticky s DI, pak ručně z organ-edit)
    public function boot($public = false)
    {
        $this->public = $public;
        
        $this->withValidator(function (Validator $validator) {
            $validator->after(function (Validator $validator) {
                $this->checkCategories($validator);
            });
        });
    }
    
    private function getCategories(Organ $organ)
    {
        $ids = $organ->organCategories->pluck('id')->toArray();
        $customIds = array_map(
            fn($id) => "custom-$id",
            $organ->organCustomCategories->pluck('id')->toArray()
        );
        return [...$ids, ...$customIds];
    }
    
    private function getOrganRebuilds(Organ $organ)
    {
        return $organ->organRebuilds->map(function (OrganRebuild $rebuild) {
            $data = $rebuild->only(['id', 'organ_builder_id', 'year_built']);
            return Helpers::arrayKeysCamel($data);
        })->all();
    }
    
    public function setOrgan(Organ $organ)
    {
        $this->organ = $organ;
        
        $data = Helpers::arrayKeysCamel($this->organ->toArray());
        $data['categories'] = $this->getCategories($this->organ);
        $data['rebuilds'] = $this->getOrganRebuilds($this->organ);
        
        $this->fill($data);
        $this->updatedWeb();
    }
    
    public function updatedWeb()
    {
        $this->webArray = explode("\n", $this->web);
    }
    
    private function checkCategories(Validator $validator)
    {
        $periodCategoriesCount = 0;
        foreach ($this->getCategoryIds() as $categoryId) {
            $category = OrganCategory::tryFrom($categoryId);
            if ($category !== null && $category->isPeriodCategory()) {
                $periodCategoriesCount++;
                if ($periodCategoriesCount >= 2) {
                    $validator->errors()->add('categories', 'Lze zadat nejvýše 1 kategorii období.');
                    break;
                }
            }
        }
        if ($periodCategoriesCount <= 0) {
            $validator->errors()->add('categories', 'Je nutné zadat alespoň 1 kategorii období.');
        }
    }
    
    private function getRebuildData($rebuild)
    {
        $data = collect($rebuild)->except(['id'])->toArray();
        return Helpers::arrayKeysSnake($data);
    }
    
    public function isOrganPublic()
    {
        if (!$this->organ->exists) return $this->public;
        else return $this->organ->isPublic();
    }
    
    public function save()
    {
        $this->validate();
        $data = Helpers::arrayKeysSnake($this->except(['categories', 'rebuilds', 'organ', 'webArray']));
        $update = $this->organ->exists;
        
        DB::transaction(function () use ($data) {
            $this->organ->fill($data);
            if (!$this->isOrganPublic()) $this->organ->user_id = Auth::id();
            $this->organ->save();

            // categories
            $categoryIds = $this->getCategoryIds();
            $customCategoryIds = $this->getCategoryIds(custom: true);
            $this->organ->organCategories()->sync($categoryIds);
            $this->organ->organCustomCategories()->sync($customCategoryIds);

            // rebuilds
            $newRebuildModels = collect($this->rebuilds)
                ->filter(
                    fn($rebuild) => !isset($rebuild['id'])
                )
                ->map(
                    fn($rebuild) => new OrganRebuild(
                        $this->getRebuildData($rebuild)
                    )
                );
            $this->organ->organRebuilds()->saveMany($newRebuildModels);
            
            foreach ($this->organ->organRebuilds as $rebuildModel) {
                $rebuild = collect($this->rebuilds)->firstWhere('id', $rebuildModel->id);
                if ($rebuild) {
                    $rebuildModel->fill(
                        $this->getRebuildData($rebuild)
                    );
                }
                else $rebuildModel->delete();
            }
            $this->organ->push();   // uloží změny v existujících rebuildech
        });
        
        if ($update) EntityUpdated::dispatch($this->organ);
        else EntityCreated::dispatch($this->organ);
    }
    
    public function delete()
    {
        if (!$this->organ->exists) throw new \RuntimeException;
        $this->organ->delete();
        EntityDeleted::dispatch($this->organ);
    }
    
    public function getCategoryIds($custom = false)
    {
        $ids = [];
        foreach ($this->categories as $id) {
            $isCustom = str_starts_with($id, 'custom-');
            $add = $custom ? $isCustom : !$isCustom;
            if ($add) {
                if ($isCustom) $id = str_replace('custom-', '', $id);
                $ids[] = $id;
            }
        }
        return $ids;
    }
    
}
