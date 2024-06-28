<?php

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use App\Models\OrganBuilder;
use App\Helpers;
use App\Livewire\Forms\OrganBuilderForm;
use App\Models\Region;
use App\Enums\OrganBuilderCategory;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    public OrganBuilder $organBuilder;

    public OrganBuilderForm $form;

    public function boot()
    {
        $this->form->withValidator(function (Validator $validator) {
            $validator->after(function (Validator $validator) {
                // podmíněná validace: šlo by použít standardní validaci pomocí exclude_if, ale to v Livewire zatím nefunguje
                if ($this->form->isWorkshop) {
                    if (!$this->form->workshopName) {
                        $validator->errors()->add('workshopName', 'Název dílny musí být vyplněn.');
                    }
                }
                else {
                    if (!$this->form->firstName) {
                        $validator->errors()->add('firstName', 'Jméno varhanáře musí být vyplněno.');
                    }
                    if (!$this->form->lastName) {
                        $validator->errors()->add('lastName', 'Jméno varhanáře musí být vyplněno.');
                    }
                }
                $this->checkCategories($validator);
            });
        });
    }

    private function checkCategories(Validator $validator)
    {
        $periodCategoriesCount = 0;
        foreach ($this->form->categories as $categoryId) {
            $category = OrganBuilderCategory::tryFrom($categoryId);
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

    public function mount()
    {
        $this->organBuilder ??= new OrganBuilder();
        $data = Helpers::arrayKeysCamel($this->organBuilder->toArray());
        $data['categories'] = $this->organBuilder->organBuilderCategories->pluck('id')->toArray();
        $this->form->fill($data);
    }

    public function delete()
    {
        if (!$this->organBuilder->exists) throw new \RuntimeException;

        $this->organBuilder->delete();
        session()->flash('status-success', __('Varhanář byl úspěšně smazán'));
        redirect()->route('organ-builders.index');
    }

    public function save()
    {
        $this->form->validate();
        $data = Helpers::arrayKeysSnake($this->form->except(['categories']));
        if (!$this->organBuilder->exists) $data['user_id'] = Auth::id();
        $this->organBuilder->fill($data)->save();
        $this->organBuilder->organBuilderCategories()->sync($this->form->categories);
        session()->flash('status-success', __('Varhanář byl úspěšně uložen.'));
        redirect()->route('organ-builders.index');
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }

    public function getTitle()
    {
        return $this->organBuilder->exists ? 'Upravit varhanáře' : 'Přidat varhanáře';
    }

    public function getStarsCount()
    {
        if (isset($this->form->importance)) return round($this->form->importance / 2);
        return null;
    }

    public function rendered()
    {
        $this->dispatch("select2-rendered");
    }
    
}; ?>

<div class="organ-builder-edit container">
    <form method="post" wire:submit="save" wire:keydown.ctrl.enter="save">
        @if ($this->form->regionId)
            <img class="float-end z-1 position-relative" src="{{ Vite::asset("resources/images/regions/{$this->form->regionId}.png") }}" width="110" />
        @endif
        <h3>{{ __($this->getTitle()) }}</h3>
        
        <div class="alert alert-info" role="alert">
          {{ __('Validaci je možné vyzkoušet u jména varhanáře (dílny) - jméno musí být vyplněno a musí začínat velkým písmenem.') }}
          {{ __('U kategorií je nutné zadat vždy právě 1 kategorii týkající se období.') }}
        </div>

        
        <div class="mb-4">
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="isWorkshopYes" value="0" wire:model.live.boolean="form.isWorkshop">
                    <label class="form-check-label" for="isWorkshopYes">
                        {{ __('Individuální varhanář') }}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="isWorkshopNo" value="1" wire:model.live.boolean="form.isWorkshop">
                    <label class="form-check-label" for="isWorkshopNo">
                        {{ __('Varhanářská dílna') }}
                    </label>
                </div>
            </div>
            

            <div class="row g-3">
                @if ($this->form->isWorkshop)
                <div>
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.workshopName') is-invalid @enderror" id="workshopName" wire:model.live="form.workshopName" aria-describedby="workshopNameFeedback" placeholder="Sieber, Jan">
                        <label for="workshopName">{{ __('Název dílny') }}</label>
                        @error('form.workshopName')
                            <div id="workshopNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @else
                <div class="col-md-6">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.firstName') is-invalid @enderror" id="firstName" wire:model.live="form.firstName" aria-describedby="firstNameFeedback" placeholder="Sieber, Jan">
                        <label for="firstName">{{ __('Jméno varhanáře') }}</label>
                        @error('form.firstName')
                            <div id="firstNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.lastName') is-invalid @enderror" id="lastName" wire:model.live="form.lastName" aria-describedby="lastNameFeedback" placeholder="Sieber, Jan">
                        <label for="lastName">{{ __('Příjmení varhanáře') }}</label>
                        @error('form.lastName')
                            <div id="lastNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="placeOfBirth" class="form-label">{{ __('Místo narození') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <input class="form-control" id="placeOfBirth" wire:model="form.placeOfBirth" placeholder="Praha">
                </div>
                <div class="col-md-6">
                    <label for="placeOfDeath" class="form-label">{{ __('Místo úmrtí') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <input class="form-control" id="placeOfDeath" wire:model="form.placeOfDeath" placeholder="Brno">
                </div>
                @endif
            </div>
            
            <hr>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="categories" class="form-label">{{ __('Kategorie') }}</label>
                    <select id="categories" class="form-select select2 @error('form.categories') is-invalid @enderror" wire:model.live="form.categories" data-placeholder="{{ __('Zvolte kategorii') }}" multiple aria-describedby="categoriesFeedback">
                        @foreach (OrganBuilderCategory::getCategoryGroups() as $group => $categories)
                            <optgroup label="{{ __(OrganBuilderCategory::getGroupName($group)) }}">
                                @foreach ($categories as $category)
                                    <option title="{{ __($category->getDescription()) }}" value="{{ $category->value }}">{{ __($category->getName()) }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('form.categories')
                        <div id="categoriesFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label">{{ __('Význam') }} <span class="text-secondary">({{ __('od 1 do 10') }})</span></label>
                    <div class="hstack gap-3">
                        <input class="form-control w-auto flex-grow-1" type="number" id="name" placeholder="4" min="1" max="10" wire:model.live.number="form.importance">
                        <x-organomania.stars :count="$this->getStarsCount()" />
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="municipality" class="form-label">{{ __('Lokalita dílny') }} <span class="text-secondary">({{ __('obec') }})</span></label>
                    <input class="form-control" id="municipality" wire:model="form.municipality" placeholder="Brno">
                </div>
                <div class="col-md-3">
                    <label for="regionId" class="form-label">{{ __('Kraj') }}</label>
                    <select id="regionId" class="form-select select2" wire:model.live="form.regionId" data-placeholder="{{ __('Zvolte kraj') }}">
                        <option></option>
                        @foreach ($this->regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="latitude" class="form-label">{{ __('Zeměpisná šířka') }}</label>
                    <input class="form-control" id="latitude" min="-90" max="90" type="number" step="0.0000001" wire:model="form.latitude" placeholder="123.45">
                </div>
                <div class="col-md-3">
                    <label for="longitude" class="form-label">{{ __('Zeměpisná délka') }}</label>
                    <input class="form-control" id="longitude" type="number" min="-180" max="180" step="0.0000001" wire:model="form.longitude" placeholder="123.45">
                </div>

                <div class="col-md-7">
                    <label for="activePeriod" class="form-label">{{ __('Období působení') }}</label>
                    <input class="form-control" id="activePeriod" wire:model="form.activePeriod" placeholder="20. století">
                    <div class="form-text">
                        {{ __('Období může být zapsáno jako přesný rozsah let (např. 1876-2021, 2001-současnost) nebo jako přibližná datace (např. 2. polovina 18. století).') }}
                        {{ __('U inidividuálních varhanářů lze uvést rok narození a úmrtí. ') }}
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="activeFromYear" class="form-label">{{ __('Rok začátku působení') }}</label>
                    <input class="form-control" id="activeFromYear" type="number" min="0" max="3000" wire:model.number="form.activeFromYear" placeholder="1876">
                    <div class="form-text">
                        {{ __('Rok slouží pouze pro seřazení varhanářů podle období (nebude zobrazen uživateli).') }}
                        {{ __('U inidividuálních varhanářů lze uvést rok narození.') }}
                    </div>
                </div>
            </div>
                
            <hr>
            
            <div class="row g-3">
                <div>
                    <label for="description" class="form-label">{{ __('Popis') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <textarea rows="8" class="form-control" id="description" wire:model="form.description"></textarea>
                </div>
            </div>
        </div>
    
        <div class="hstack">
            @if ($this->organBuilder->exists)
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi-trash"></i> Smazat</button>
            @endif
                
            <small class="text-secondary ms-auto me-2"><i class="bi-info-circle-fill"></i> {!! __('Stiskněte <kbd>Ctrl+Enter</kbd> pro uložení') !!}</small>
            <a class="btn btn-sm btn-secondary" href="{{ url()->previous() }}"><i class="bi-arrow-return-left"></i> Zpět</a>&nbsp;
            <button type="submit" class="btn btn-sm btn-primary" href="{{ route('organ-builders.index') }}">
                <span wire:loading.remove wire:target="save">
                    <i class="bi-floppy"></i> Uložit
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                </span>
            </button>
        </div>
    </form>
    
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmDeleteModalLabel">{{ __('Smazat') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavřít"></button>
                </div>
                <div class="modal-body">
                    {{ __('Opravdu chcete varhanáře smazat?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                    <button type="button" class="btn btn-danger" wire:click="delete"><i class="bi-trash"></i> Smazat</button>
                </div>
            </div>
        </div>
    </div>
</div>
