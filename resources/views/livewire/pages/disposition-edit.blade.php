<?php

use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use App\Helpers;
use App\RegisterPaletteItem;
use App\Models\Register;
use App\Models\Disposition;
use App\Models\Keyboard;
use App\Models\DispositionRegister;
use App\Models\RegisterName;
use App\Models\Organ;
use App\Models\PaletteRegister;
use App\Livewire\Forms\DispositionForm;
use App\Services\DispositionParser;
use App\Traits\ConvertEmptyStringsToNull;
use App\Traits\HasAccordion;
use App\Traits\HasHighlightDispositionFilters;
use App\Enums\RegisterCategory;
use App\Enums\Pitch;
use App\Enums\DispositionLanguage;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull, HasAccordion, HasHighlightDispositionFilters;

    public DispositionForm $form;

    #[Url]
    public string $public = '0';

    #[Locked]
    public Disposition $disposition;

    const
        EDIT_KEYBOARD = 'keyboard',
        EDIT_REGISTER = 'register';
    public $isEdit = false;
    public $isNew = false;
    #[Locked]
    public $keyboardIndex;
    public $keyboardBackup;
    #[Locked]
    public $registerIndex;
    public $registerBackup;

    public $dispositionText;

    public $previousUrl;

    private $select2Rendered = false;

    public $paletteKeyboardIndex;

    public ?RegisterName $registerName = null;

    const
        SESSION_KEY_SELECTED_PALETTE_TAB = 'dispositions.edit.selectedPaletteTab',
        SESSION_KEY_SHOW_STATS = 'dispositions.edit.showStats';

    public function mount()
    {
        $this->disposition ??= new Disposition();
        $ability = $this->disposition->exists ? 'update' : 'create';
        $this->authorize($ability, $this->disposition);

        $this->dispositionText = DispositionParser::DISPOSITION_TEXT_EXAMPLE;
        $this->previousUrl = request()->headers->get('referer');

        if ($organId = request('organId')) {
            if ($organ = Organ::find($organId)) {
                $this->form->organId = $organId;
                $this->form->name = "{$organ->municipality}, {$organ->place}";
            }
        }

        $this->form->setDisposition($this->disposition);
    }

    public function boot()
    {
        $this->form->boot($this->public);

        if ($this->public) {
            Gate::authorize('createPublic', Disposition::class);
        }
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        if (!$this->select2Rendered) $this->dispatch('select2-rendered');
    }

    public function updatedFormLanguage()
    {
        unset($this->registerNamesLanguge);
    }

    public function rendering(View $view): void
    {
        $title = __($this->getTitle());
        $view->title($title);
    }

    public function getTitle()
    {
        if ($this->disposition->exists) return 'Upravit dispozici';
        elseif ($this->public) return 'Přidat dispozici (veřejně)';
        else return 'Přidat dispozici';
    }

    #[Computed]
    public function pedalExists()
    {
        return collect($this->form->keyboards)->contains(
            fn($keyboard) => $keyboard['pedal']
        );
    }

    #[Computed(persist: true)]
    public function registerNames()
    {
        return RegisterName::query()
            ->with('register')
            ->orderBy('name')
            ->get()
            // je-li ve více jazycích stejný název rejstříku, sloučíme do 1
            //  - TODO: ideálně upřednostnit variantu, která má uveden jazyk dispozice
            ->unique('name');
    }

    #[Computed]
    public function pitchGroups()
    {
        return Pitch::getPitchGroups();
    }

    #[Computed]
    public function organs()
    {
        return Organ::query()
            ->with('organBuilder')
            ->when($this->form->isDispositionPublic(), function (Builder $query) {
                // k nesoukromým dispozicím lze uložit jen nesoukromé varhanany
                $query->public();
            })
            ->orderBy('municipality')
            ->orderBy('place')
            ->get();
    }

    #[Computed]
    public function keyboardStartNumbers()
    {
        $number = 1;
        $numbers = [];
        foreach ($this->form->registers as $keyboardIndex => $registers) {
            $numbers[$keyboardIndex] = $number;
            $number += count($registers);
        }
        return $numbers;
    }

    #[Computed]
    public function paletteItemGroups()
    {
        if (!isset($this->paletteKeyboardIndex)) throw new \LogicException;
        $keyboard = $this->form->keyboards[$this->paletteKeyboardIndex];
        $registers = $this->form->registers[$this->paletteKeyboardIndex];

        $groups = [0 => []];
        foreach (RegisterCategory::getMainCategories() as $category) {
            $groups[$category->value] = [];
        }

        $paletteRegisters = PaletteRegister::query()
            ->when(!$keyboard['pedal'], function (Builder $query) {
                $query->where('pedal', 0);
            })
            ->get();

        foreach ($paletteRegisters as $paletteRegister) {
            $categoryId = $paletteRegister->register->registerCategory->value;
            $pitch = $paletteRegister->pitch ?? $paletteRegister->register->pitch;

            // pro rejstřík v paletě najdeme jeho jméno v preferovaném jazyce, nebo jakékoli jméno
            $registerNames = $paletteRegister->register->registerNames;
            $registerName = $registerNames->first(
                fn(RegisterName $registerName) => $registerName->language === $this->form->getlanguage()
            ) ?? $registerNames->first();
            $existsInKeyboard = collect($registers)->firstWhere('registerNameId', $registerName->id) !== null;
            if ($existsInKeyboard) continue;

            $paletteItem = new RegisterPaletteItem(
                $registerName, $pitch, $paletteRegister->multiplier,
                $this->form->getLanguage()
            );
            $groups[$categoryId][] = $paletteItem;
            $frequent = $keyboard['pedal'] ? $paletteRegister->frequent_pedal : $paletteRegister->frequent_manual;
            if ($frequent) $groups[0][] = $paletteItem;
        }

        ksort($groups);
        foreach ($groups as $categoryId => $paletteItems) {
            $groups[$categoryId] = collect($paletteItems)->sortBy([
                ['pitchOrder', 'asc'],
                ['registerName.name', 'asc'],
            ])->toArray();
        }
        return $groups;
    }

    private function getPaletteItemSubgroups($group)
    {
        $subgroups = [];
        foreach (RegisterCategory::getMainCategories() as $category) {
            $subgroups[$category->value] = [];
        }

        foreach ($group as $paletteItem) {
            $categoryId = $paletteItem->registerName->register->registerCategory->value;
            $subgroups[$categoryId] ??= [];
            $subgroups[$categoryId][] = $paletteItem;
        }
        
        $subgroups = array_filter($subgroups);

        return $subgroups;
    }

    private function getPaletteKeyboard()
    {
        return $this->form->keyboards[$this->paletteKeyboardIndex] ?? throw new \LogicException;
    }

    private function resetPalette()
    {
        unset($this->paletteKeyboardIndex);
        $this->js('closeRegisterPalette()');
    }

    private function getPaletteItemGroupName($categoryId)
    {
        return $categoryId === 0
            ? __('Nejčastější')
            : RegisterCategory::from($categoryId)->getName();
    }

    public function isKeyboardEdited($keyboardIndex)
    {
        return
            $this->isEdit === static::EDIT_KEYBOARD
            && $this->keyboardIndex === $keyboardIndex;
    }

    public function isRegisterEdited($keyboardIndex, $registerIndex)
    {
        return
            $this->isEdit === static::EDIT_REGISTER
            && $this->keyboardIndex === $keyboardIndex
            && $this->registerIndex === $registerIndex;
    }

    private function isFirstKeyboardAdding()
    {
        return
            $this->isEdit === static::EDIT_KEYBOARD
            && $this->isNew
            && count($this->form->keyboards) === 1;
    }

    public function importDispositionFromText()
    {
        if ($this->dispositionText == '') throw new \RuntimeException;

        $parser = new DispositionParser($this->dispositionText, DispositionLanguage::Czech, $this->form->keyboardNumbering);
        $disposition = $parser->parse();
        $this->form->setDisposition($disposition, overwrite: false);

        $this->js('showToast("importedToast")');
    }

    public function addKeyboard(bool $pedal = false)
    {
        $this->form->keyboards[] = [
            'name' => $pedal ? Keyboard::getDefaultName($this->form->getLanguage(), $pedal) : '',
            'pedal' => $pedal,
        ];
        $keyboardIndex = array_key_last($this->form->keyboards);
        $this->editKeyboard($keyboardIndex);
        $this->isNew = true;
        $this->form->registers[$keyboardIndex] = [];
    }

    public function addRegister($keyboardIndex, $coupler = false)
    {
        $this->form->registers[$keyboardIndex][] = [
            'registerNameId' => '',
            'multiplier' => '',
            'pitchId' => '',
            'coupler' => $coupler,
        ];
        $registerIndex = array_key_last($this->form->registers[$keyboardIndex]);
        $this->editRegister($keyboardIndex, $registerIndex, isNew: true);
        $this->isNew = true;
    }

    public function addRegisterFromPalette($registerNameId, $pitchId, $multiplier = null)
    {
        if (!isset($this->paletteKeyboardIndex)) throw new \LogicException;
        
        $this->form->registers[$this->paletteKeyboardIndex][] = [
            'registerNameId' => $registerNameId,
            'multiplier' => $multiplier ?? '',
            'pitchId' => $pitchId,
            'coupler' => false,
        ];
    }

    public function editKeyboard($keyboardIndex)
    {
        $this->isEdit = static::EDIT_KEYBOARD;
        $this->keyboardIndex = $keyboardIndex;
        if (!isset($this->form->keyboards)) throw new \RuntimeException;
        $this->keyboardBackup = $this->form->keyboards[$keyboardIndex];
        $this->js('$(() => $("input.keyboard-name").focus())');
    }

    public function editRegister($keyboardIndex, $registerIndex, $isNew = false)
    {
        $this->isEdit = static::EDIT_REGISTER;
        $this->keyboardIndex = $keyboardIndex;
        $this->registerIndex = $registerIndex;
        $register = $this->form->registers[$this->keyboardIndex][$this->registerIndex] ?? null;
        if (!isset($register)) throw new \RuntimeException;
        $this->registerBackup = $register;

        $this->dispatch('select2-rendered');
        $this->select2Rendered = true;
        $this->dispatch('select2-sync-needed', componentName: 'pages.disposition-edit');
        if ($register['coupler']) $this->js('$(() => $("input.coupler-name").focus())');
        else {
            $event = $isNew ? 'select2-open' : 'select2-focus';
            $this->dispatch($event, selector: 'select.register-name-select');
        }
    }

    public function saveKeyboard()
    {
        $keyboard = &$this->form->keyboards[$this->keyboardIndex];
        if ($keyboard['name'] === '') {
            $keyboard['name'] = Keyboard::getDefaultName($this->form->getLanguage(), $keyboard['pedal']);
        }

        $this->isEdit = false;
        $this->jsFocusAddRegister($this->keyboardIndex);        // ve Firefoxu se focus nastaví, ale není viditelný
        $this->keyboardIndex = null;
        $this->isNew = false;
    }

    public function saveRegister()
    {
        $this->form->validateRegister($this->keyboardIndex, $this->registerIndex);
        $this->isEdit = false;
        $this->jsFocusAddRegister($this->keyboardIndex);
        $this->keyboardIndex = $this->registerIndex = null;
        $this->isNew = false;
    }

    private function jsFocusAddRegister($keyboardIndex)
    {
        $js = sprintf(
            '$(() => focusAddRegister(%s))',
            $keyboardIndex
        );
        $this->js($js);
    }

    public function deleteKeyboard($keyboardIndex)
    {
        unset($this->form->keyboards[$keyboardIndex]);
        unset($this->form->registers[$keyboardIndex]);
        $this->form->keyboards = array_values($this->form->keyboards);
        $this->form->registers = array_values($this->form->registers);
        $this->resetPalette();
    }

    public function deleteRegister($keyboardIndex, $registerIndex)
    {
        unset($this->form->registers[$keyboardIndex][$registerIndex]);
        $this->form->registers[$keyboardIndex] = array_values($this->form->registers[$keyboardIndex]);
    }

    public function cancelKeyboard()
    {
        if ($this->isEdit !== static::EDIT_KEYBOARD) throw new \LogicException;

        if ($this->isNew) {
            $this->deleteKeyboard($this->keyboardIndex);
            $this->isNew = false;
        }
        else $this->form->keyboards[$this->keyboardIndex] = $this->keyboardBackup;

        $this->isEdit = false;
        $this->keyboardIndex = null;
    }

    public function cancelRegister()
    {
        if ($this->isEdit !== static::EDIT_REGISTER) throw new \LogicException;

        if ($this->isNew) {
            $this->deleteRegister($this->keyboardIndex, $this->registerIndex);
            $this->isNew = false;
        }
        else $this->form->registers[$this->keyboardIndex][$this->registerIndex] = $this->registerBackup;

        $this->isEdit = false;
        $this->keyboardIndex = $this->registerIndex = null;
    }

    public function save()
    {
        // toto by bylo lepší dělat v DispositionForm::save(), ale tam není metoda isCustomRegister()
        foreach ($this->form->registers as &$registers) {
            foreach ($registers as &$register) {
                $register['custom'] = $this->isCustomRegister($register);
            }
        }

        $this->form->save();
        session()->flash('status-success', __('Dispozice byla úspěšně uložena.'));
        if (isset($this->previousUrl)) $this->redirect($this->previousUrl, navigate: true);
        else $this->redirectRoute('dispositions.index', navigate: true);
    }

    public function delete()
    {
        $this->authorize('delete', $this->disposition);
        $this->form->delete();
        session()->flash('status-success', __('Dispozice byla úspěšně smazána.'));

        if (
            isset($this->previousUrl)
            // nemůžeme přesměrovat na detail záznamu, který jsme smazali
            && !in_array($this->previousUrl, [
                route('dispositions.show', $this->disposition->slug),
                route('dispositions.show', $this->disposition->id)
            ])
        ) {
            $this->redirect($this->previousUrl, navigate: true);
        }
        else $this->redirectRoute('dispositions.index', navigate: true);
    }

    private function move(&$data, $index, $direction = 'up')
    {
        $newIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if (!isset($data[$newIndex])) throw new \RuntimeException;
        Helpers::swap($data[$index], $data[$newIndex]);
    }
    
    public function moveKeyboard($keyboardIndex, $direction = 'up')
    {
        $this->move($this->form->keyboards, $keyboardIndex, $direction);
        $this->move($this->form->registers, $keyboardIndex, $direction);
        $this->resetPalette();
    }
    
    public function moveRegister($keyboardIndex, $registerIndex, $direction = 'up')
    {
        $this->move($this->form->registers[$keyboardIndex], $registerIndex, $direction);
    }

    private function isCustomRegister($register)
    {
        return $this->getRegisterNameModel($register['registerNameId']) === null;
    }

    private function getRegisterNameModel($registerNameId)
    {
        if (is_numeric($registerNameId)) {
            return $this->registerNames->firstWhere('id', $registerNameId);
        }
    }

    private function getRegisterName($register)
    {
        $registerName = $this->getRegisterNameModel($register['registerNameId']);
        if (isset($registerName)) return $registerName->name;
        return $register['registerNameId'];
    }

    private function isReedRegister($register)
    {
        $registerName = $this->getRegisterNameModel($register['registerNameId']);
        return $registerName && $registerName->register->registerCategory === RegisterCategory::Reed;
    }

    private function getPitchLabel($id)
    {
        return Pitch::from($id)->getLabel($this->form->getLanguage());
    }

    public function setRegisterName($registerNameId)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->registerName = $this->getRegisterNameModel($registerNameId);
    }

    public function setPaletteKeyboardIndex($keyboardIndex)
    {
        $this->paletteKeyboardIndex = $keyboardIndex;
    }

    #[Renderless]
    public function selectPaletteTab($categoryId)
    {
        session([static::SESSION_KEY_SELECTED_PALETTE_TAB => $categoryId]);
    }

    private function isTabSelected($categoryId)
    {
        return session(static::SESSION_KEY_SELECTED_PALETTE_TAB, 0) === $categoryId;
    }

    private function getKeyboardName($keyboardIndex)
    {
        $keyboard = $this->form->keyboards[$keyboardIndex];
        $name = '';
        if (!$keyboard['pedal']) {
            $number = Helpers::formatRomanNumeral($keyboardIndex + 1);
            $name .=  "$number. ";
        }
        $name .= $keyboard['name'];
        return $name;
    }

    private function getKeyboardRealDispositionRegistersCount($keyboardIndex)
    {
        return collect($this->form->registers[$keyboardIndex])
            ->filter(fn($register) => !$register['coupler'])
            ->count();
    }

    private function getRealDispositionRegistersCount()
    {
        return collect($this->form->keyboards)
            ->map(fn($_keyboard, $keyboardIndex) => $this->getKeyboardRealDispositionRegistersCount($keyboardIndex))
            ->sum();
    }

    private function isRegisterHighlighted($keyboardIndex, $registerIndex)
    {
        return $this->highlightedDispositionRegisters->contains(
            fn(DispositionRegister $register)
                => $register->keyboard_index === $keyboardIndex && $register->register_index === $registerIndex
        );
    }

    private function getDispositionForHighlight(): Disposition
    {
        return $this->currentDisposition;
    }

    private function createDispositionFromForm()
    {
        $dispositionModel = new Disposition(['language' => $this->form->getLanguage()]);
        $keyboardOrder = 1;
        foreach ($this->form->keyboards as $keyboardIndex => $keyboard) {
            $keyboardModel = new Keyboard([
                'keyboard_index' => $keyboardIndex,
                'name' => $keyboard['name'],
                'pedal' => $keyboard['pedal'],
                'order' => $keyboardOrder++,
            ]);
            $registerOrder = 1;
            foreach ($this->form->registers[$keyboardIndex] as $registerIndex => $register) {
                $registerData = [
                    'keyboard_index' => $keyboardIndex,
                    'register_index' => $registerIndex,
                    'multiplier' => $register['multiplier'] !== '' ? $register['multiplier'] : null,
                    'pitch_id' => $register['pitchId'] !== '' ? $register['pitchId'] : null,
                    'coupler' => $register['coupler'],
                    'order' => $registerOrder++,
                ];
                if ($this->isCustomRegister($register)) $registerData['name'] = $register['registerNameId'];
                else $registerData['register_name_id'] = $register['registerNameId'];

                $registerModel = new DispositionRegister($registerData);
                $keyboardModel->dispositionRegisters->push($registerModel);
                $dispositionModel->dispositionRegisters->push($registerModel);
                if (!$registerModel->coupler) $dispositionModel->realDispositionRegisters->push($registerModel);
            }
            $dispositionModel->keyboards->push($keyboardModel);
        }
        return $dispositionModel;
    }

    #[Computed]
    public function currentDisposition()
    {
        return $this->createDispositionFromForm();
    }

}; ?>

<div class="disposition-edit container" x-data="{ deletePedal: false }">
    
    <h3>
        {{ __($this->getTitle()) }}
        @if (!$this->form->isDispositionPublic())
            <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
        @endif
    </h3>
    
    {{-- základní údaje (název, jazyk...) --}}
    <form id="basicDataForm" class="row gy-3 gx-0 mb-3 align-items-stretch" wire:submit="save" onsubmit="disableOnbeforeunload()">
        <div class="col-lg-7">
            <label class="form-label" for="name">{{ __('Název dispozice') }}</label>
            <input
                class="form-control form-control-lg @error('form.name') is-invalid @enderror"
                id="name"
                wire:model="form.name"
                aria-describedby="nameFeedback"
                placeholder="{{ __('např. Dolní Lhota, kostel sv. Tomáše - aktuální stav') }}"
                required
                autofocus
            >
            @error('form.name')
                <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
            @enderror
                
            <div class="row mt-3 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0" for="organId">{{ __('Varhany') }}</label>
                </div>
                <div class="col">
                    <x-organomania.selects.organ-select :organs="$this->organs" model="form.organId" allowClear="true" />
                </div>
                <div class="col-12">
                    <span class="form-text">{{ __('Nejsou-li varhany v databázi varhan obsaženy, ponechte nevyplněno.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-auto text-center d-none d-lg-block mx-4">
            <div class="vr h-100"></div>
        </div>
        <div class="col-lg-4">
            <div class="row gy-1 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0" for="language">{{ __('Preferovaný jazyk') }}</label>
                </div>
                <div class="col-auto">
                    <select id="language" class="form-select form-select-sm" wire:model.change="form.language">
                        @foreach (DispositionLanguage::cases() as $language)
                            <option value="{{ $language->value }}">{{ $language->getName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-100 d-none d-lg-block"></div>
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="keyboardNumbering" wire:model.change="form.keyboardNumbering">
                        <label class="form-check-label" for="keyboardNumbering">{{ __('Číslovat manuály') }}</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="numbering" wire:model.change="form.numbering">
                        <label class="form-check-label" for="numbering">{{ __('Číslovat rejstříky') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <button id="basicDataFormSubmit" class="d-none"></button>
    </form>
    
    <hr />
    
    {{-- manuály a rejstříky --}}
    <div class="alert alert-warning py-1 px-2 float-end mb-2" style="margin-top: -10px;">
        <small>
            <i class="bi-exclamation-circle"></i> {!! __('Nezapomeňte  <strong>uložit</strong> změny tlačítkem ve spodní části stránky!') !!}
        </small>
    </div>
    <h5>{{ __('Manuály a rejstříky') }}</h5>
    
    @if (empty($this->form->keyboards))
        <p class="form-text">{{ __('Zatím nebyly vloženy žádné manuály a rejstříky.') }}</p>
    @else
        @if (!empty($this->highlightDispositionFilters))
            <x-organomania.highlight-disposition-filters :filters="$this->highlightDispositionFilters" />
        @endif
        
        {{-- manuály --}}
        <div class="text-center">
            <ol class="keyboards disposition mt-2 mb-0 w-100 d-inline-block" type="I" style="max-width: 40em !important; text-align: left;">
                @foreach ($this->form->keyboards as $keyboardIndex => $keyboard)
                    <li @class(['mb-3', 'w-100', 'exclude-from-numbering' => $keyboard['pedal']]) @style(['list-style-type: none' => !$this->form->keyboardNumbering || $keyboard['pedal']])>
                        <div class="disposition-item">
                            @if ($this->isKeyboardEdited($keyboardIndex))
                                <form class="fw-bold hstack gap-2" wire:submit="saveKeyboard" @keydown.esc="$wire.cancelKeyboard()">
                                    <input
                                        class="keyboard-name form-control form-control-sm"
                                        list="manual-names"
                                        type="text"
                                        wire:model="form.keyboards.{{ $keyboardIndex }}.name"
                                        placeholder="{{ Keyboard::getDefaultName($this->form->getLanguage(), $keyboard['pedal']) }}"
                                    />
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Uložit') }}">
                                            <i class="bi-floppy"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" wire:click="cancelKeyboard" data-bs-toggle="tooltip" data-bs-title="{{ __('Neukládat') }}">
                                            <i class="bi-x"></i>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="fw-bold hstack gap-2">
                                    <x-organomania.move-buttons
                                        class="ms-1 bg-body-tertiary"
                                        actionUp="moveKeyboard({{ $keyboardIndex }}, 'up')"
                                        actionDown="moveKeyboard({{ $keyboardIndex }}, 'down')"
                                        moveWhat="manuál"
                                        :isFirst="$loop->first"
                                        :isLast="$loop->last"
                                        :disabled="$this->isEdit"
                                    />
                                    {{ $keyboard['name'] }}
                                    <div class="ms-auto"></div>
                                    <div class="btn-group">
                                        <button
                                            type="button"
                                            @class(['btn', 'btn-primary', 'btn-sm', 'invisible'])
                                        >
                                            <i class="bi-eye"></i>
                                        </button>
                                        <button type="button" @class(['btn', 'btn-outline-primary', 'btn-sm', 'disabled' => $this->isEdit]) wire:click="editKeyboard({{ $keyboardIndex }})" data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit') }}">
                                            <i class="bi-pencil"></i>
                                        </button>
                                        <button
                                            type="button"
                                            @class(['btn', 'btn-danger', 'btn-sm', 'disabled' => $this->isEdit])
                                            @click="deletePedal = {{ Js::from($keyboard['pedal']) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteKeyboardModal"
                                            data-keyboard-index="{{ $keyboardIndex }}"
                                        >
                                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat') }}">
                                                <i class="bi-trash"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- rejstříky --}}
                        @if (!empty($this->form->registers[$keyboardIndex]))
                            <ol class="mt-0 mb-0 ps-0" start="{{ $this->keyboardStartNumbers[$keyboardIndex] }}" @style(['list-style-type: none' => !$this->form->numbering])>
                                @foreach ($this->form->registers[$keyboardIndex] as $registerIndex => $register)
                                    @php $highlighted = $this->isRegisterHighlighted($keyboardIndex, $registerIndex) @endphp
                                    <li @class(['rounded', 'disposition-item', 'register', 'highlighted' => $highlighted])>
                                        @if ($this->isRegisterEdited($keyboardIndex, $registerIndex))
                                            <form class="hstack gap-2" wire:submit="saveRegister" @keydown.esc="$wire.cancelRegister()">
                                                @if ($register['coupler'])
                                                    <input
                                                        type="text"
                                                        class="coupler-name flex-grow-1 form-control form-control-sm"
                                                        wire:model="form.registers.{{ $keyboardIndex }}.{{ $registerIndex }}.registerNameId"
                                                        placeholder="{{ __('např.') }} II/I"
                                                        list="coupler-names"
                                                        required
                                                    />
                                                    <datalist id="coupler-names">
                                                        @foreach ($this->currentDisposition->getProposedCouplers($this->currentDisposition->keyboards[$keyboardIndex]) as $coupler))
                                                            <option value="{{ $coupler }}"></option>
                                                        @endforeach
                                                        <option value="{{ DispositionRegister::getTremulantName($this->form->getLanguage()) }}"></option>
                                                    </datalist>
                                                @else
                                                    <x-organomania.selects.register-name-select
                                                        :registerNames="$this->registerNames"
                                                        id="registerNameId{{ $keyboardIndex }}_{{ $registerIndex }}"
                                                        model="form.registers.{{ $keyboardIndex }}.{{ $registerIndex }}.registerNameId"
                                                        :language="$this->form->getLanguage()"
                                                        customRegisterName="{{ $this->isCustomRegister($register) ? $register['registerNameId'] : null }}"
                                                    />
                                                @endif

                                                <x-organomania.selects.pitch-select
                                                    :pitchGroups="$this->pitchGroups"
                                                    id="pitchId{{ $keyboardIndex }}_{{ $registerIndex }}"
                                                    model="form.registers.{{ $keyboardIndex }}.{{ $registerIndex }}.pitchId"
                                                    :language="$this->form->getLanguage()"
                                                    allow-clear="true"
                                                />
                                                @if (!$register['coupler'])
                                                    <div class="multiplier input-group input-group-sm">
                                                        <input
                                                            class="form-control form-control-sm"
                                                            wire:model="form.registers.{{ $keyboardIndex }}.{{ $registerIndex }}.multiplier"
                                                            size="3"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-title="{{ __('Násobnost rejstříku') }}"
                                                        />
                                                        <span class="input-group-text d-none d-md-flex">&times;</span>
                                                    </div>
                                                @endif
                                                <div class="ms-auto"></div>
                                                <div class="btn-group">
                                                    <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="tooltip" data-bs-title="{{ __('Uložit') }}">
                                                        <i class="bi-floppy"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" wire:click="cancelRegister" data-bs-toggle="tooltip" data-bs-title="{{ __('Neukládat') }}">
                                                        <i class="bi-x"></i>
                                                    </button>
                                                </div>
                                            </form>
                                            @error('register')
                                                <div class="invalid-feedback d-block text-center">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        @else
                                            <div class="hstack gap-2">
                                                <x-organomania.move-buttons
                                                    class="ms-1"
                                                    actionUp="moveRegister({{ $keyboardIndex }}, {{ $registerIndex }}, 'up')"
                                                    actionDown="moveRegister({{ $keyboardIndex }}, {{ $registerIndex }}, 'down')"
                                                    moveWhat="rejstřík"
                                                    :isFirst="$loop->first"
                                                    :isLast="$loop->last"
                                                    :disabled="$this->isEdit"
                                                />
                                                <div @class(['coupler' => $register['coupler']])>
                                                    {{ $this->getRegisterName($register) }}
                                                    @if ($register['multiplier'] != '')
                                                        {{ DispositionRegister::formatMultiplier($register['multiplier']) }}
                                                    @endif
                                                </div>

                                                <div class="ms-auto"></div>
                                                @if ($this->isReedRegister($register))
                                                    &bull;
                                                @endif
                                                @if ($register['pitchId'] != '')
                                                    <div>
                                                        {{ $this->getPitchLabel($register['pitchId']) }}
                                                    </div>
                                                @endif
                                                <div class="btn-group">
                                                    <button
                                                        type="button"
                                                        @class(['btn', 'btn-outline-primary', 'btn-sm', 'disabled' => $this->isEdit, 'invisible' => $this->isCustomRegister($register)])
                                                        wire:click="setRegisterName({{ $register['registerNameId'] }})"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#registerModal"
                                                    >
                                                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Podrobnosti o rejstříku') }}">
                                                            <i class="bi-eye"></i>
                                                        </span>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @class(['btn', 'btn-outline-primary', 'btn-sm', 'disabled' => $this->isEdit])
                                                        wire:click="editRegister({{ $keyboardIndex }}, {{ $registerIndex }})"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-title="{{ __('Upravit') }}"
                                                    >
                                                        <i class="bi-pencil"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @class(['btn', 'btn-danger', 'btn-sm', 'disabled' => $this->isEdit])
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteRegisterModal"
                                                        data-keyboard-index="{{ $keyboardIndex }}"
                                                        data-register-index="{{ $registerIndex }}"
                                                    >
                                                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat') }}">
                                                            <i class="bi-trash"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                            @if (($registerCount = $this->getKeyboardRealDispositionRegistersCount($keyboardIndex)) > 0)
                                <div class="fst-italic disposition-item-padding text-body-secondary">
                                    <small>{{ __('Znějících rejstříků celkem') }}: {{ $registerCount }}</small>
                                </div>
                            @endif
                        @endif

                        {{-- přidat rejstřík --}}
                        @if (!($this->isKeyboardEdited($keyboardIndex) && $this->isNew))
                            <div class="mt-1 lh-lg">
                                <button
                                    id="addRegister{{ $keyboardIndex }}"
                                    type="button"
                                    @class(['btn', 'btn-primary', 'btn-sm', 'disabled' => $this->isEdit])
                                    wire:click="addRegister({{ $keyboardIndex }})"
                                >
                                    <i class="bi-plus-lg"></i> {{ __('Rejstřík') }}
                                </button>
                                <button
                                    type="button"
                                    @class(['btn', 'btn-primary', 'btn-sm', 'disabled' => $this->isEdit])
                                    @click="$wire.setPaletteKeyboardIndex({{ $keyboardIndex }}); focusPalette()"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#registerPalette"
                                >
                                    <i class="bi-plus-lg"></i> {{ __('Rejstřík z palety') }}
                                </button>
                                <button
                                    type="button"
                                    @class(['btn', 'btn-primary', 'btn-sm', 'disabled' => $this->isEdit])
                                    wire:click="addRegister({{ $keyboardIndex }}, true)"
                                >
                                    <i class="bi-plus-lg"></i> {{ __('Spojka aj.') }}
                                </button>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
        <div class="fst-italic disposition-item-padding text-body-secondary">
            {{ __('Znějících rejstříků celkem') }}: {{ $this->getRealDispositionRegistersCount() }}
        </div>
    @endif
    
    {{-- přidat manuál --}}
    <div class="mt-3">
        <button type="button" @class(['btn', 'btn-primary', 'btn-sm', 'disabled' => $this->isEdit]) wire:click="addKeyboard">
            <i class="bi-plus-lg"></i> {{ __('Manuál') }}
        </button>
        @if (!$this->pedalExists)
            <button type="button" @class(['btn', 'btn-primary', 'btn-sm', 'disabled' => $this->isEdit]) wire:click="addKeyboard(true)">
                <i class="bi-plus-lg"></i> {{ __('Pedál') }}
            </button>
        @endif
        <button type="button" @class(['btn', 'btn-primary', 'btn-sm', 'ms-1', 'disabled' => $this->isEdit]) data-bs-toggle="modal" data-bs-target="#importDispositionFromTextModal">
            {{ __('Importovat textově') }}
        </button>
    </div>
        
    <div class="accordion mt-3">
        <x-organomania.accordion-item
            id="accordion-stats"
            title="{{ __('Statistiky') }}"
            :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_STATS)"
            onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_STATS}}')"
        >
            <x-organomania.disposition-stats :disposition="$this->currentDisposition" />
        </x-organomania.accordion-item>
    </div>
    
    <hr />
    
    {{-- doplňující údaje --}}
    @foreach (['appendix', 'description'] as $textField)
        @php 
            if ($textField === 'appendix') {
                $label = __('Doplňující informace');
                $placeholder = implode("\n", [
                    __('Např.:'),
                    __('Rozsah manuálů: C-c4'),
                    __('Crescendový válec'),
                    sprintf('%s: A, B, C, D', __('Volné kombinace')),
                    sprintf('%s: p, f, ff', __('Kolektivy'))
                ]);
            }
            else {
                $label = __('Popis');
                $placeholder = 'Charakteristika dispozice varhan a jednotlivých strojů.';
            }
        @endphp
        <div class="mt-3">
            <label for="{{ $textField }}" class="form-label">{{ $label }} <span class="text-secondary">({{ __('nepovinné') }})</span></label>
            <textarea
                rows="5"
                class="form-control @error("form.$textField") is-invalid @enderror"
                id="{{ $textField }}"
                wire:model="form.{{ $textField }}"
                aria-describedby="{{ $textField }}Feedback"
                placeholder="{{ $placeholder }}"
            ></textarea>
            @error("form.$textField")
                <div id="{{ $textField }}Feedback" class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
    
    {{-- tlačítka uložit/zavřít --}}
    <div class="hstack mt-4">
        @if ($this->disposition->exists)
            <button
                type="button"
                @class(['btn', 'btn-sm', 'btn-danger', 'disabled' => $this->isEdit])
                data-bs-toggle="modal"
                data-bs-target="#deleteDispositionModal"
            >
                <i class="bi-trash"></i> {{ __('Smazat') }}
            </button>
        @endif

        <a 
            @class(['btn', 'btn-sm', 'btn-secondary', 'ms-auto', 'disabled' => $this->isEdit])
            href="{{ url()->previous() }}"
            wire:navigate
        >
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>&nbsp;
        <button type="button" @class(['btn', 'btn-sm', 'btn-primary', 'disabled' => $this->isEdit]) onclick="$('#basicDataFormSubmit').click()">
            <span wire:loading.remove wire:target="save">
                <i class="bi-floppy"></i> {{ __('Uložit') }}
            </span>
            <span wire:loading wire:target="save">
                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
            </span>
        </button>
    </div>
    
    <datalist id="manual-names">
        @foreach (Keyboard::getProposedManualNames($this->form->getLanguage()) as $name)
            <option value="{{ $name }}"></option>
        @endforeach
    </datalist>
    
    <x-organomania.register-palette />
    
    <x-organomania.modals.register-modal :registerName="$this->registerName" :language="$this->form->getLanguage()" />
    
    <x-organomania.modals.confirm-modal
        id="deleteDispositionModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="$wire.delete()"
    >
        {{ __('Opravdu chcete dispozici smazat?') }}
    </x-organomania.modals.confirm-modal>
        
    <x-organomania.modals.confirm-modal
        id="deleteKeyboardModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="deleteKeyboard"
    >
        <template x-if="deletePedal">
            <span>
                {{ __('Opravdu chcete pedál smazat?') }}
            </span>
        </template>
        <template x-if="!deletePedal">
            <span>
                {{ __('Opravdu chcete manuál smazat?') }}
            </span>
        </template>
    </x-organomania.modals.confirm-modal>
    
    <x-organomania.modals.confirm-modal
        id="deleteRegisterModal"
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="deleteRegister"
    >
        {{ __('Opravdu chcete rejstřík smazat?') }}
    </x-organomania.modals.confirm-modal>
        
    <x-organomania.modals.import-disposition-from-text-modal />
    <x-organomania.modals.disposition-text-example-modal />
        
    <x-organomania.toast toastId="importedToast">
        {{ __('Dispozice byla úspěšně importována.') }}
    </x-organomania.toast>
    
</div>

@script
<script>
    window.PREVENT_UNLOAD = true
    
    window.deleteDisposition = function () {
        $wire.delete()
    }
        
    window.deleteKeyboard = function () {
        var btn = confirmModal.getInvokeButton('deleteKeyboardModal')
        $wire.deleteKeyboard(btn.dataset.keyboardIndex)
    }
        
    window.deleteRegister = function () {
        var btn = confirmModal.getInvokeButton('deleteRegisterModal')
        $wire.deleteRegister(btn.dataset.keyboardIndex, btn.dataset.registerIndex)
    }
        
    window.focusPalette = function () {
        $('#registerPalette').focus()
    }
    
    window.disableOnbeforeunload = function () {
        PREVENT_UNLOAD = false
    }
    
    window.focusAddRegister = function (keyboardIndex) {
        $(`#addRegister${keyboardIndex}`).focus()
    }
        
    window.addEventListener("beforeunload", (event) => {
        if (PREVENT_UNLOAD) {
            // kontrolujeme, protože listener může zůstat zaregistrován i po navigaci na jinou stranu
            var isThisPage
                = location.pathname === '/dispositions/create'
                || /^\/dispositions\/[0-9]+\/edit$/.test(location.pathname)
                    
            if (isThisPage) {
                event.returnValue = 'Opravdu chcete odejít? Neuložené změny budou ztraceny.'
            }
        }
    })
</script>
@endscript