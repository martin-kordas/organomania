<?php

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use App\Models\OrganBuilder;
use App\Helpers;
use App\Livewire\Forms\OrganBuilderForm;
use App\Models\OrganBuilderTimelineItem;
use App\Models\Region;
use App\Enums\OrganBuilderCategory;
use App\Events\EntityCreated;
use App\Events\EntityUpdated;
use App\Events\EntityDeleted;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    public OrganBuilder $organBuilder;

    public OrganBuilderForm $form;

    #[Url]
    public string $public = '0';

    public $previousUrl;

    public function boot()
    {
        if ($this->public) {
            Gate::authorize('createPublic', OrganBuilder::class);
        }

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
                        $validator->errors()->add('lastName', 'Příjmení varhanáře musí být vyplněno.');
                    }
                }
                $this->checkCategories($validator);
            });
        });
    }

    public function rendering(View $view): void
    {
        $title = __($this->getTitle());
        $view->title($title);
    }

    private function checkCategories(Validator $validator)
    {
        $periodCategoriesCount = 0;
        foreach ($this->form->categories as $categoryId) {
            $category = OrganBuilderCategory::tryFrom((int)$categoryId);
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
        $ability = $this->organBuilder->exists ? 'update' : 'create';
        $this->authorize($ability, $this->organBuilder);

        $data = Helpers::arrayKeysCamel($this->organBuilder->toArray());
        $data['categories'] = $this->organBuilder->organBuilderCategories->pluck('id')->toArray();
        $this->form->fill($data);

        $this->previousUrl = request()->headers->get('referer');
    }

    public function delete()
    {
        if (!$this->organBuilder->exists) throw new \RuntimeException;

        $this->authorize('delete', $this->organBuilder);
        $this->organBuilder->delete();
        EntityDeleted::dispatch($this->organBuilder);
        session()->flash('status-success', __('Varhanář byl úspěšně smazán.'));

        if (
            isset($this->previousUrl)
            // nemůžeme přesměrovat na detail záznamu, který jsme smazali
            && !in_array($this->previousUrl, [
                route('organ-builders.show', $this->organBuilder->slug),
                route('organ-builders.show', $this->organBuilder->id)
            ])
        ) {
            $this->redirect($this->previousUrl, navigate: true);
        }
        else $this->redirectRoute('organ-builders.index', navigate: true);
    }

    public function save()
    {
        // většina povinných údajů, kde vznikají chyby, jsou v horní části stránky
        $this->js('scrollToTop()');

        if ($this->form->isWorkshop) {
            $this->form->firstName = $this->form->lastName = null;
        }
        else $this->form->workshopName = null;

        $this->form->validate();
        $exists = $this->organBuilder->exists;
        $data = Helpers::arrayKeysSnake($this->form->except(['categories']));
        if (!$this->isOrganBuilderPublic()) $this->organBuilder->user_id = Auth::id();
        $this->organBuilder->fill($data)->save();
        $categoryIds = array_filter($this->form->categories, fn ($categoryId) => $categoryId !== '__rm__');
        $this->organBuilder->organBuilderCategories()->sync($categoryIds);

        // pro private varhanáře se timeline položky ukládají automaticky
        if (!$this->isOrganBuilderPublic()) $this->saveTimelineItem();

        if ($exists) EntityUpdated::dispatch($this->organBuilder);
        else EntityCreated::dispatch($this->organBuilder);

        $this->organBuilder->refresh();
        session()->flash('status-success', __('Varhanář byl úspěšně uložen.'));
        if (isset($this->previousUrl) && $exists) $this->redirect($this->previousUrl, navigate: true);
        else $this->redirectRoute('organ-builders.show', $this->organBuilder->slug, navigate: true);
    }

    private function saveTimelineItem()
    {
        $timelineItem = $this->organBuilder->timelineItems->first() ?? new OrganBuilderTimelineItem;
        $timelineItem->loadFromOrganBuilder($this->organBuilder);
        $timelineItem->save();
    }

    public function isOrganBuilderPublic()
    {
        if (!$this->organBuilder->exists) return $this->public;
        else return $this->organBuilder->isPublic();
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }

    #[Computed]
    public function workshopMembersPlaceholder()
    {
        return <<<EOL
            Bauer, Johann (1680–1735)
            Bauer, Georg (1704–1756)
            EOL;
    }

    public function getTitle()
    {
        if ($this->organBuilder->exists) return 'Upravit varhanáře';
        elseif ($this->public) return 'Přidat varhanáře (veřejně)';
        else return 'Přidat varhanáře';
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
        <h3>
            {{ __($this->getTitle()) }}
            @if (!$this->isOrganBuilderPublic())
                <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
            @endif
        </h3>
            
        <div class="mb-4">
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="isWorkshopYes" value="0" wire:model.change.boolean="form.isWorkshop">
                    <label class="form-check-label" for="isWorkshopYes">
                        {{ __('Individuální varhanář') }}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="isWorkshopNo" value="1" wire:model.change.boolean="form.isWorkshop">
                    <label class="form-check-label" for="isWorkshopNo">
                        {{ __('Varhanářská dílna') }}
                    </label>
                </div>
            </div>

            <div class="row g-3">
                @if ($this->form->isWorkshop)
                <div>
                    <div class="form-floating mb-3">
                        <input class="form-control form-control-lg @error('form.workshopName') is-invalid @enderror" id="workshopName" wire:model.blur="form.workshopName" aria-describedby="workshopNameFeedback" placeholder="Sieber, Jan" autofocus>
                        <label for="workshopName">{{ __('Název dílny') }}</label>
                        @error('form.workshopName')
                            <div id="workshopNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="workshopMembers" class="form-label">{{ __('Členové dílny') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                        <textarea rows="3" class="form-control" id="workshopMembers" wire:model="form.workshopMembers" placeholder="{{ $this->workshopMembersPlaceholder }}"></textarea>
                    </div>
                </div>
                @else
                <div class="col-md-6">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.firstName') is-invalid @enderror" id="firstName" wire:model.blur="form.firstName" aria-describedby="firstNameFeedback" placeholder="Sieber, Jan" autofocus>
                        <label for="firstName">{{ __('Jméno varhanáře') }}</label>
                        @error('form.firstName')
                            <div id="firstNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('form.lastName') is-invalid @enderror" id="lastName" wire:model.blur="form.lastName" aria-describedby="lastNameFeedback" placeholder="Sieber, Jan">
                        <label for="lastName">{{ __('Příjmení varhanáře') }}</label>
                        @error('form.lastName')
                            <div id="lastNameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="placeOfBirth" class="form-label @error('form.placeOfBirth') is-invalid @enderror">{{ __('Místo narození') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <input class="form-control" id="placeOfBirth" wire:model="form.placeOfBirth" placeholder="Praha">
                    @error('form.placeOfBirth')
                        <div id="placeOfBirthFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="placeOfDeath" class="form-label @error('form.placeOfDeath') is-invalid @enderror">{{ __('Místo úmrtí') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <input class="form-control" id="placeOfDeath" wire:model="form.placeOfDeath" placeholder="Brno">
                    @error('form.placeOfDeath')
                        <div id="placeOfDeathFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endif
            </div>
            
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Kategorizace') }}</h5>
                
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
                        <input class="form-control w-auto flex-grow-1 @error('form.importance') is-invalid @enderror" type="number" id="name" placeholder="4" min="1" max="10" wire:model.blur.number="form.importance">
                        <x-organomania.stars :count="$this->getStarsCount()" />
                        @error('form.importance')
                            <div id="importanceFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="municipality" class="form-label">{{ __('Lokalita dílny') }} <span class="text-secondary">({{ __('obec') }})</span></label>
                    <input class="form-control @error('form.municipality') is-invalid @enderror" id="municipality" wire:model="form.municipality" placeholder="Brno">
                    @error('form.municipality')
                        <div id="municipalityFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="regionId" class="form-label">{{ __('Kraj') }}</label>
                    <x-organomania.selects.region-select :regions="$this->regions" id="regionId" model="form.regionId" />
                </div>
                <div class="col-md-3">
                    <label for="latitude" class="form-label @error('form.latitude') is-invalid @enderror">{{ __('Zeměpisná šířka') }}</label>
                    <input class="form-control" id="latitude" min="-90" max="90" type="number" step="0.0000001" wire:model="form.latitude" placeholder="40,1234567">
                    @error('form.latitude')
                        <div id="latitudeFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="longitude" class="form-label @error('form.longitude') is-invalid @enderror">{{ __('Zeměpisná délka') }}</label>
                    <input class="form-control" id="longitude" type="number" min="-180" max="180" step="0.0000001" wire:model="form.longitude" placeholder="20,1234567">
                    @error('form.longitude')
                        <div id="longitudeFeedback" class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text text-end">
                            <a href="#" onclick="return openMap()">{{ __('Zobrazit na mapě') }}</a>
                        </div>
                    @enderror
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
                    <input class="form-control @error('form.activeFromYear') is-invalid @enderror" id="activeFromYear" type="number" min="0" max="3000" wire:model.number="form.activeFromYear" placeholder="1876">
                    @error('form.activeFromYear')
                        <div id="activeFromYearFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        {{ __('Rok slouží pouze pro seřazení varhanářů podle období (nebude zobrazen uživateli).') }}
                        {{ __('U inidividuálních varhanářů lze uvést rok narození.') }}
                    </div>
                </div>
            </div>
            
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Dokumentace') }}</h5>
                
                <div>
                    <label for="perex" class="form-label">{{ __('Perex') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <textarea rows="3" class="form-control" id="perex" wire:model="form.perex" placeholder="{{ __('Krátká jednovětá charakteristika varhanáře, která se vypíše v rámečku s miniaturou.') }}"></textarea>
                </div>
                <div>
                    <label for="description" class="form-label">{{ __('Popis') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <textarea rows="8" class="form-control" id="description" wire:model="form.description" placeholder="{{ __('Podrobnější popis varhanáře, který se vypíše v detailním zobrazení.') }}"></textarea>
                </div>
            </div>
                
            <div class="row g-3 bg-light rounded p-2 mt-4">
                <h5 class="mt-1 mb-0">{{ __('Externí materiály') }}</h5>
                
                <div>
                    <label for="web" class="form-label">{{ __('Web') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <input class="form-control @error('form.web') is-invalid @enderror" id="web" wire:model="form.web" aria-describedby="webFeedback">
                    @error('form.web')
                        <div id="webFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="literature" class="form-label">{{ __('Literatura') }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
                    <textarea rows="3" class="form-control" id="literature" wire:model="form.literature"></textarea>
                    <div class="form-text">
                        {{ __('Každá publikace se uvede na samostatném řádku.') }}
                    </div>
                </div>
            </div>
        </div>
    
        <div class="hstack">
            @if ($this->organBuilder->exists)
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal"><i class="bi-trash"></i> Smazat</button>
            @endif
                
            <div class="ps-2 ms-auto me-2 keyboard-hint">
                <small class="text-secondary"><i class="bi-info-circle-fill"></i> {!! __('Stiskněte <kbd>Ctrl+Enter</kbd> pro uložení') !!}</small>
            </div>
                
            <a class="btn btn-sm btn-secondary" href="{{ url()->previous() }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
            <button type="submit" class="btn btn-sm btn-primary">
                <span wire:loading.remove wire:target="save">
                    <i class="bi-floppy"></i> {{ __('Uložit') }}
                </span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                </span>
            </button>
        </div>
    </form>
    
    <x-organomania.modals.confirm-modal
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="$wire.delete()"
    >
        {{ __('Opravdu chcete varhanáře smazat?') }}
    </x-organomania.modals.confirm-modal>
    
    <x-organomania.toast toastId="locationIncomplete" color="danger">
        {{ __('Souřadnice nejsou úplné.') }}
    </x-organomania.toast>
</div>

@script
<script>
    window.openMap = function () {
        let lat = $('#latitude').val()
        let lon = $('#longitude').val()
        if (!lat | !lon) showToast('locationIncomplete')
        else {
            let url = getMapUrl(lat, lon)
            window.open(url, '_blank')
        }
        return false
    }
</script>
@endscript
