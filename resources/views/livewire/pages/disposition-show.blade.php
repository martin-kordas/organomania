<?php

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL as URLFacade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Disposition;
use App\Models\DispositionRegister;
use App\Models\Registration;
use App\Models\RegisterName;
use App\Models\Keyboard;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\HasAccordion;
use App\Traits\HasHighlightDispositionFilters;
use App\Enums\RegisterCategory;
use App\Enums\DispositionLanguage;
use App\Enums\Pitch;
use App\DispositionFilters\DispositionFilter;
use App\DispositionFilters\PlenoFilter;
use App\DispositionFilters\TuttiFilter;
use App\DispositionFilters\PitchFilter;
use App\DispositionFilters\RegisterCategoryFilter;
use App\DispositionFilters\CouplersFilter;
use App\Services\AI\SuggestRegistrationAI;
use App\Services\MarkdownConvertorService;
use App\Helpers;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion, HasHighlightDispositionFilters;

    #[Locked]
    public $dispositionSlug;
    #[Locked]
    public Disposition $disposition;

    public ?RegisterName $registerName = null;
    public ?Pitch $pitch = null;

    #[Url(history: true)]
    public ?int $registrationId = null;
    public ?int $registrationIdSigned = null;
    private ?Registration $registration = null;
    public bool $isEdit = false;
    public string $registrationName = '';
    // [dispositionRegister1 => true, ...] (se strukturou [dispositionRegister1, ...] nefungovalo dobře)
    public array $dispositionRegisters = [];
    public array $dispositionRegistersBackup = [];
    public array $checkAllKeyboards = [];
    public array $checkAllKeyboardsBackup = [];
    public array $showRegistersKeyboards = [];

    public ?string $translationLanguage = null;
    public string $sort = 'order';

    #[Session]
    public bool $showOnlyRegistered = false;

    #[Session]
    public bool $showRegisterCounts = true;
    #[Session]
    public bool $reedsWithDot = true;
    #[Session]
    public bool $keyboardsInSeparateColumns = true;
    #[Session]
    public bool $showAppendix = true;
    #[Session]
    public bool $showDescription = true;

    #[Locked]
    public bool $signed;

    private $highlightRegisterId;

    protected MarkdownConvertorService $markdownConvertor;

    private $suggestRegistrationInfo;

    const
        SESSION_KEY_SHOW_VIEW_SETTINGS = 'dispositions.edit.showViewSettings',
        SESSION_KEY_SHOW_STATS = 'dispositions.edit.showStats',
        SESSION_KEY_SHOW_SETTINGS = 'dispositions.edit.showSettings';

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;

        $this->signed ??= request()->hasValidSignature(false);
        // nepoužíváme klasický route model binding, protože potřebujeme ručně odebrat OwnedEntityScope
        //  - musí to fungovat i Livewire AJAX requestech
        $this->disposision ??= $this->getDisposition();

        $query = Disposition::query();
        if ($this->signed) $query->withoutGlobalScope(OwnedEntityScope::class);
        if (is_numeric($this->dispositionSlug)) $query->where('id', $this->dispositionSlug);
        else $query->where('slug', $this->dispositionSlug);
        $this->disposition = $query->firstOrFail();

        // při překreslení komponenty se signed URL zřejmě již nepoužije
        //  - proto informaci o autorizovaném registrationId cachujeme
        if ($this->signed) {
            if ($registrationIdSigned = request('registrationId')) {
                $this->registrationIdSigned = (int)$registrationIdSigned;
            }
        }

        if ($this->registrationId != '' && $this->registrationId === $this->registrationIdSigned) {
            $this->disposition->load(['registrations' => function (HasMany $query) {
                $query
                    ->withoutGlobalScope(OwnedEntityScope::class)
                    ->where('user_id', Auth::id())
                    ->orWhere('id', $this->registrationId);
            }]);
        }

        $this->highlightRegisterId = (int)request('highlightRegisterId');
        $this->eagerLoadDisposition();
        $this->setRegistration(boot: true);
    }

    public function mount()
    {
        if (!$this->signed) {
            $this->authorize('view', $this->disposition);
        }

        $this->setRegistration();
    }

    private function getDisposition()
    {
        $query = Disposition::query();
        if ($this->signed) $query->withoutGlobalScope(OwnedEntityScope::class);
        if (is_numeric($this->dispositionSlug)) $query->where('id', $this->dispositionSlug);
        else $query->where('slug', $this->dispositionSlug);
        return $query->firstOrFail();
    }

    public function updatedRegistrationId()
    {
        $this->setRegistration();
    }

    private function eagerLoadDisposition()
    {
        $this->disposition
            ->load('keyboards.dispositionRegisters')
            ->load('keyboards.dispositionRegisters.registerName')
            ->load('keyboards.dispositionRegisters.registerName.register')
            ->load('keyboards.dispositionRegisters.registerName.register.registerPitches')
            ->load('keyboards.dispositionRegisters.registerName.register.registerCategories')
            ->load(['keyboards' => function (HasMany $query) {
                $query->withCount('realDispositionRegisters');
            }])
            ->loadCount([
                'dispositionRegisters',
                'realDispositionRegisters',
            ]);
    }

    private function refreshDisposition()
    {
        $this->disposition->refresh();
        $this->eagerLoadDisposition();
    }

    private function setRegistration($boot = false)
    {
        if (isset($this->registrationId)) {
            $this->registration = $this->getRegistration($this->registrationId);

            if (!$boot) {
                $this->registrationName = $this->registration->name;
                $dispositionRegisters = $this->registration
                    ->dispositionRegisters
                    ->pluck('id')
                    ->toArray();
                $this->dispositionRegisters = array_fill_keys($dispositionRegisters, true);
                $this->refreshCheckAllKeyboards();
            }
        }
        else {
            $this->registration = null;

            if (!$boot) {
                $this->registrationName = '';
                $this->dispositionRegisters = [];
                $this->checkAllKeyboards = [];
            }
        }
    }

    private function setRegistrationId($id)
    {
        $this->registrationId = $id;
        $this->setRegistration();
    }

    private function refreshCheckAllKeyboards()
    {
        $this->checkAllKeyboards = [];
        foreach ($this->disposition->keyboards as $keyboard) {
            $dispositionRegisterIds = $this->getKeyboardDispositionRegisterIds($keyboard->id);
            if (!empty($dispositionRegisterIds)) {
                $missing = array_diff_key(
                    array_flip($dispositionRegisterIds),
                    array_filter($this->dispositionRegisters)
                );
                if (empty($missing)) $this->checkAllKeyboards[$keyboard->id] = true;
            }
        }
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
        $this->dispatch('select2-sync-needed', componentName: 'pages.disposition-show');

        // $view->title() nezajistí přepsání titlu po překreslení komponenty, proto ho přepíšeme ručně
        $titleJs = json_encode($this->getTitle());
        $this->js("document.title = $titleJs");
    }

    public function rendering(View $view): void
    {
        $view->title($this->getTitle());
    }

    private function getRegistration($id)
    {
        return $this->registration
            = $this->disposition->registrations->firstWhere('id', $id)
            ?? abort(404);
    }

    private function getTitle()
    {
        $title = __('Dispozice');
        $title .= " – {$this->disposition->name}";
        // název dispozice v titlu: není možný, protože překreslení strany při výběru jiné dispozice nepřekreslí title
        if ($this->registration) {
            $registrationText = __('registrace');
            $title .= " ($registrationText: {$this->registration->name})";
        }
        return $title;
    }

    #[Computed]
    public function keyboardStartNumbers()
    {
        return $this->disposition->getKeyboardStartNumbers();
    }

    #[Computed]
    public function registrationDispositionFilters()
    {
        $filters = [
            new PlenoFilter($this->disposition),
            new TuttiFilter($this->disposition),
            new RegisterCategoryFilter($this->disposition, RegisterCategory::Reed),
        ];
        $couplersEqualFilter = new CouplersFilter($this->disposition, CouplersFilter::TYPE_EQUAL);
        $filters[] = $couplersEqualFilter;

        $couplersAllFilter = new CouplersFilter($this->disposition, CouplersFilter::TYPE_ALL);
        $existNonEqualCouplers = $couplersAllFilter->getRegisterCount() > $couplersEqualFilter->getRegisterCount();
        if ($existNonEqualCouplers) {
            $filters[] = $couplersAllFilter;
        }
        
        return $filters;
    }

    public function setRegisterName($registerNameId, $pitchId = null)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->registerName = RegisterName::find($registerNameId);
        $this->pitch = $pitchId ? Pitch::from($pitchId) : null;
    }

    public function getDispositionRegisterName(DispositionRegister $dispositionRegister, $translate = false)
    {
        if ($this->translationLanguage && $translate) {
            $translationLanguage = DispositionLanguage::from($this->translationLanguage);
            if ($dispositionRegister?->registerName?->language !== $translationLanguage) {
                $name = $dispositionRegister->register
                    ? $dispositionRegister->register->getNameInPreferredLanguage($translationLanguage, strict: true)
                    : $dispositionRegister->name;
                if ($name) return $name;
            }
        }
        return $dispositionRegister?->registerName?->name ?? $dispositionRegister->name;
    }

    public function updatingCheckAllKeyboards($checked, $keyboardId)
    {
        $dispositionRegisterIds = array_fill_keys(
            $this->getKeyboardDispositionRegisterIds($keyboardId),
            true
        );

        $this->dispositionRegisters =
            $checked
            ? $this->dispositionRegisters + $dispositionRegisterIds
            : array_diff_key($this->dispositionRegisters, $dispositionRegisterIds);
    }

    public function updatedDispositionRegisters()
    {
        $this->refreshCheckAllKeyboards();
    }

    private function getKeyboardDispositionRegisterIds($keyboardId)
    {
        $keyboard
            = $this->disposition->keyboards->firstWhere('id', $keyboardId)
            ?? throw new \RuntimeException;
        return $keyboard->dispositionRegisters->pluck('id')->toArray();
    }

    public function addDispositionRegistersFromFilter($filterIndex)
    {
        $filter = $this->registrationDispositionFilters[$filterIndex] ?? throw new \RuntimeException;
        foreach ($filter->getRegisters() as $register) {
            $this->dispositionRegisters[$register->id] = true;
        }
        $this->refreshCheckAllKeyboards();
    }

    public function removeAllDispositionRegisters()
    {
        $this->dispositionRegisters = [];
        $this->checkAllKeyboards = [];
    }

    public function add()
    {
        unset($this->registrationId);
        $this->setRegistration();
        $this->edit();
    }

    public function edit()
    {
        $this->isEdit = true;
        $this->dispositionRegistersBackup = $this->dispositionRegisters;
        $this->checkAllKeyboardsBackup = $this->checkAllKeyboards;
        $this->js('$(() => $("input.registration-name").focus())');
    }

    public function cancel()
    {
        $this->isEdit = false;
        $this->dispositionRegisters = $this->dispositionRegistersBackup;
        $this->checkAllKeyboards = $this->checkAllKeyboardsBackup;
        $this->resetValidation();
    }

    private function customValidate()
    {
        $this->resetValidation();
        if (empty(array_filter($this->dispositionRegisters))) {
            throw ValidationException::withMessages([
                'dispositionRegisters' => __('Alespoň 1 rejstřík musí být vybrán.')
            ]);
        }
    }

    public function save()
    {
        $this->customValidate();
        $data = [
            'name' => $this->registrationName,
            'user_id' => Auth::id(),
        ];

        if (!$this->registrationId) {
            $data += ['disposition_id' => $this->disposition->id];
            $registration = new Registration($data);
        }
        else $registration = $this->registration->fill($data);
        $registration->save();

        $dispositionRegisterIds = array_keys(array_filter($this->dispositionRegisters));
        $registration->dispositionRegisters()->sync($dispositionRegisterIds);

        $this->refreshDisposition();
        if (!isset($this->registrationId)) {
            $this->setRegistrationId($registration->id);
        }

        session()->flash('status-disposition-show', __('Registrace byla úspěšně uložena.'));
        $this->isEdit = false;
    }

    public function delete()
    {
        $registration
            = $this->disposition->registrations->firstWhere('id', $this->registrationId)
            ?? throw new \RuntimeException;
        $registration->delete();
        $this->refreshDisposition();
        $this->setRegistrationId(null);

        session()->flash('status-disposition-show', __('Registrace byla úspěšně smazána.'));
        $this->isEdit = false;
    }

    private function getRegistrationShareUrl()
    {
        $relativeUrl = URLFacade::signedRoute('dispositions.show', [
            'dispositionSlug' => $this->disposition->id,
            'registrationId' => $this->registrationId
        ], absolute: false);
        return url($relativeUrl);
    }

    #[Computed]
    public function showRegistrations()
    {
        return 
            $this->disposition->disposition_registers_count > 0
            && (
                Gate::allows('create', [Registration::class, $this->disposition])
                || $this->disposition->registrations->isNotEmpty()
            );
    }

    private function getDispositionForHighlight(): Disposition
    {
        return $this->disposition;
    }

    #[Computed]
    public function dispositionRegistersSorted()
    {
        $nameComparator = function (DispositionRegister $register1, DispositionRegister $register2) {
            return strnatcmp(
                $this->getDispositionRegisterName($register1, translate: true),
                $this->getDispositionRegisterName($register2, translate: true)
            );
        };
        $pitchComparator = function (DispositionRegister $register1, DispositionRegister $register2) {
            $getPitchOrder = fn(DispositionRegister $register)
                => $register->pitch?->getAliquoteOrder() ?? INF;
            return $getPitchOrder($register1) <=> $getPitchOrder($register2);
        };
        $keyboardComparator = function (DispositionRegister $register1, DispositionRegister $register2) {
            $getKeyboardOrder = fn(DispositionRegister $register)
                => $this->disposition->getDispositionRegisterKeyboard($register)->order;
            return $getKeyboardOrder($register1) <=> $getKeyboardOrder($register2);
        };

        $registers = $this->disposition->dispositionRegisters;
        switch ($this->sort) {
            case 'name':
                $registers = $registers->sortBy([
                    $nameComparator,
                    $pitchComparator,
                    $keyboardComparator,
                ]);
                break;
            case 'pitch':
                $registers = $registers->sortBy([
                    $pitchComparator,
                    $nameComparator,
                    $keyboardComparator,
                ]);
                break;
        }
        return $registers;
    }

    public function toggleShowRegisters($keyboardId)
    {
        $show = $this->shouldShowRegisters($keyboardId);
        $this->showRegistersKeyboards[$keyboardId] = !$show;
    }

    private function shouldShowRegisters($keyboardId)
    {
        return ($this->showRegistersKeyboards[$keyboardId] ?? true);
    }

    #[Computed]
    public function preferredLanguage()
    {
        return 
            $this->translationLanguage
            ? DispositionLanguage::from($this->translationLanguage)
            : $this->disposition->language;
    }

    #[Computed]
    public function shouldHideUnregistered()
    {
        return $this->showOnlyRegistered && ($this->registrationId ?? null) && !$this->isEdit;
    }

    #[Computed]
    public function registerCategoriesGroups()
    {
        return RegisterCategory::getCategoryGroups();
    }

    private function hasKeyboardVisibleRegisters(Keyboard $keyboard)
    {
        if (!$this->shouldHideUnregistered) return $keyboard->dispositionRegisters->isNotEmpty();

        return $keyboard->dispositionRegisters->contains(
            fn(DispositionRegister $register) => $this->dispositionRegisters[$register->id] ?? false
        );
    }

    private function isRegisterHighlighted($register)
    {
        return
            ($register->register && $register->register->id === $this->highlightRegisterId)
            || $this->highlightedDispositionRegisters->pluck('id')->contains($register->id);
    }

    #[Computed]
    public function exportFilename()
    {
        $dispositionText = __('Dispozice');
        return "$dispositionText - {$this->disposition->name}";
    }

    public function exportAsPdf()
    {
        return response()
            ->streamDownload(
                function () {
                    $pdf = Pdf::loadView('components.organomania.pdf.disposition', [
                        'disposition' => $this->disposition,
                    ]);
                    echo $pdf->stream();
                },
                name: "{$this->exportFilename}.pdf",
                headers: ['Content-Type' => 'application/pdf']
            );
    }

    public function exportAsDoc()
    {
        return response()
            ->streamDownload(
                function () {
                    echo view('components.organomania.pdf.disposition', [
                            'disposition' => $this->disposition,
                            'doc' => true,
                        ])
                        ->render();
                },
                name: "{$this->exportFilename}.doc",
                headers: ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            );
    }

    public function exportAsCsv()
    {
        return response()
            ->streamDownload(
                function () {
                    $header = [
                        __('Číslo manuálu'),
                        __('Název manuálu'),
                        __('Číslo rejstříku'),
                        __('Název rejstříku'),
                        __('Spojka aj.'),
                        __('Násobnost'),
                        __('Poloha'),
                    ];
                    $rows = [
                        $header,
                        ...$this->disposition->toCsvArray(),
                    ];
                    echo Helpers::array2Csv($rows);
                },
                name: "{$this->exportFilename}.csv",
                headers: ['Content-Type' => 'text/csv']
            );
    }

    public function exportAsJson()
    {
        return response()
            ->streamDownload(
                function () {
                    echo collect($this->disposition->toStructuredArray())->toJson();
                },
                name: "{$this->exportFilename}json",
                headers: ['Content-Type' => 'application/json']
            );
    }

    public function suggestRegistration(string $piece)
    {
        Gate::authorize('useAI');
        if (!$this->isEdit) throw new \RuntimeException;

        $rowNumberDispositionRegisterId = [];
        $dispositionText = $this->disposition->toPlaintext(numbering: false, rowNumberDispositionRegisterId: $rowNumberDispositionRegisterId);

        $dispositionRegistersBackup = $this->dispositionRegisters;
        try {
            $AI = app()->makeWith(SuggestRegistrationAI::class, [
                'disposition' => $dispositionText,
                'organ' => $this->disposition->organ,
            ]);
            $res = $AI->suggest($piece);

            $this->dispositionRegisters = [];
            foreach ($res['registerRowNumbers'] as $rowNumber) {
                $dispositionRegisterId = $rowNumberDispositionRegisterId[$rowNumber] ?? throw new \RuntimeException;
                $this->dispositionRegisters[$dispositionRegisterId] = true;
            }
            $this->suggestRegistrationInfo = $res['recommendations'];
            $this->js('showToast("suggestRegistrationSuccess")');
        }
        catch (\Exception $ex) {
            $this->dispositionRegisters = $dispositionRegistersBackup;
            $this->js('showToast("suggestRegistrationFail")');
        }
    }

}; ?>

<div class="disposition-show container print-backgrounds">
    @if (session('status-disposition-show'))
        <div class="alert alert-success">
            <i class="bi-check-circle-fill"></i> {{ session('status-disposition-show') }}
        </div>
    @endif
    
    <h3 @if (Auth::user()?->admin) title="ID: {{ $disposition->id }}" @endif>
        {{ $disposition->name }}
        @if (!$disposition->isPublic())
            <i class="bi-lock text-warning d-print-none" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
        @endif
    </h3>

    <form wire:submit="save">
            <div class="col-12">
                @isset($disposition->organ)
                    {{ __('Varhany') }}:&nbsp;
                    <x-organomania.organ-organ-builder-link :organ="$disposition->organ" />
                @endisset
            </div>

        @if ($disposition->keyboards->isEmpty())
            <div class="col-12 text-body-secondary mt-2">{{ __('Dispozice zatím neobsahuje žádné manuály a rejstříky.') }}</div>
        @else
            {{-- možnosti --}}
            <div class="accordion mt-2 d-print-none">
                <x-organomania.accordion-item
                    id="accordion-settings"
                    title="{{ __('Možnosti') }}"
                    :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_SETTINGS)"
                    onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_SETTINGS}}')"
                >
                    <div class="row g-1 align-items-center">
                        @if (!empty($this->highlightDispositionFilters))
                            <div class="col-12 lh-lg d-print-none mb-1">
                                <x-organomania.highlight-disposition-filters :filters="$this->highlightDispositionFilters" />
                            </div>
                        @endif

                        <div class="col-4 col-md-auto col-lg-auto d-print-none">
                            <label for="language" class="mb-0 label">{{ __('Převést do jazyka') }}</label>
                        </div>
                        <div class="col-8 col-md-3 col-lg-auto pe-md-3 d-print-none">
                            <select id="language" class="form-select form-select-sm" wire:model.change="translationLanguage">
                                <option value="">{{ __('původní') }}</option>
                                @foreach (DispositionLanguage::cases() as $language)
                                    <option value="{{ $language->value }}">{{ $language->getName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-4 col-md-auto col-lg-auto d-print-none">
                            <label for="sort" class="label">{{ __('Řazení rejstříků') }}</label>
                        </div>
                        <div class="col-8 col-md-4 col-lg-auto d-print-none">
                            <select id="sort" class="form-select form-select-sm" wire:model.change="sort">
                                <option value="order">{{ __('původní') }}</option>
                                <option value="name">{{ __('podle názvů') }}</option>
                                <option value="pitch">{{ __('podle polohy') }}</option>
                            </select>
                        </div>

                        @if ($disposition->sameOrganDispositions()->isNotEmpty())
                            <div class="w-100"></div>
                            <div class="col-4 col-md-auto col-lg-auto d-print-none">
                                <label for="sort" class="label">{{ __('Porovnat s dispozicí') }}</label>
                            </div>
                            <div class="col-8 col-md-4 col-lg-auto d-print-none">
                                <select id="diff" class="form-select form-select-sm" onchange="diffOnchange()" @if ($isEdit) disabled @endif>
                                    @foreach ($disposition->sameOrganDispositions() as $disposition1)
                                        <option>({{ __('zvolte dispozici') }})</option>
                                        <option value="{{ route('dispositions.diff', ['dispositionId1' => $disposition->id, 'dispositionId2' => $disposition1->id]) }}">
                                            {{ $disposition1->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </x-organomania.accordion-item>
            </div>
        
            <h5 class="mt-3">{{ __('Manuály a rejstříky') }}</h5>
            
            <x-organomania.info-alert class="mb-2 d-print-none">
                {!! __('<strong>Rejstřík</strong> je sada píšťal určité zvukové barvy.') !!}
                {!! __('Polohu (výšku tónů) rejstříku určuje stopová výška: <em>8\'</em> značí základní polohu tónu, nižší číslo (např. <em>4\'</em>) značí vyšší polohu tónu, vyšší číslo (např. <em>16\'</em>) značí nižší polohu tónu.') !!}
                {!! __('Pro každý <strong>manuál</strong> (klaviaturu) mají varhany samostatnou sadu rejstříků.') !!}
                {!! __('Rejstříky dělíme do kategorií podle způsobu konstrukce, viz') !!}
                <a class="link-primary text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#categoriesModal">{{ __('Přehled kategorií rejstříků') }}</a>.
            </x-organomania.info-alert>
            
            {{-- registrace --}}
            @if ($this->showRegistrations && $this->disposition->registrations->isEmpty() && !$isEdit)
                <div class="mt-3 d-print-none">
                    <button type="button" class="btn btn-sm btn-primary" wire:click="add()">
                        <i class="bi-plus-lg"></i> {{ __('Přidat registraci') }}
                    </button>
                </div>
            @elseif ($this->showRegistrations)
                <div id="registrationsList" class="row mt-3 gx-2 align-items-center d-print-none" @keydown.esc="$wire.cancel()" style="max-width: 35em;">
                    <div class="col-auto">
                        <label class="label form-label" for="registrationId">{{ __('Registrace') }}</label>&nbsp;&nbsp;
                    </div>
                    @if (isset($registrationId) && !$isEdit)
                        <div class="col-auto">
                            <div class="form-check form-switch" style="font-size: 85%;">
                                <input class="form-check-input" type="checkbox" role="switch" id="showOnlyRegistered" wire:model.change="showOnlyRegistered">
                                <label class="form-check-label" for="showOnlyRegistered">
                                    <span class="d-sm-none">{{ __('Jen naregistrované') }}</span>
                                    <span class="d-none d-sm-inline">{{ __('Zobrazit jen rejstříky v registraci') }}</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    @if (Gate::allows('useRegistrationSets'))
                        <div class="col-auto ms-auto position-relative" style="font-size: 85%; top: -4px;">
                            <a @class(['btn', 'btn-sm', 'btn-outline-primary', 'disabled' => $isEdit]) href="{{ route('dispositions.registration-sets.index', $disposition->slug) }}" wire:navigate>
                                {{ __('Sady') }}<span class="d-none d-sm-inline"> {{ __('registrací') }}</span>
                                @if ($disposition->registrationSets->isNotEmpty())
                                    <span class="badge text-bg-secondary rounded-pill">{{ $disposition->registrationSets->count() }}</span>
                                @endif
                            </a>
                        </div>
                    @endif
                    <div class="w-100"></div>
                    @if ($isEdit || $disposition->registrations->isNotEmpty())
                        <div class="col">
                            @if ($isEdit)
                                <input class="registration-name form-control form-control-sm" id="registrationId" wire:model="registrationName" placeholder="{{ __('např. Bach, J. S.: Toccata a fuga d-moll') }}" required />
                            @else
                                <x-organomania.selects.registration-select :registrations="$disposition->registrations" :allowClear="!$this->signed" />
                            @endif
                        </div>
                    @endif
                    <div class="col-auto">
                        @if ($isEdit)
                            <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-title="{{ __('Uložit registraci') }}">
                                <i class="bi-floppy"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancel()" data-bs-toggle="tooltip" data-bs-title="{{ __('Neukládat registraci') }}">
                                <i class="bi-x"></i>
                            </button>
                        @else
                            @isset($registrationId)
                                @can('update', $this->registration)
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit()" data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit registraci') }}">
                                        <i class="bi-pencil"></i>
                                    </button>
                                @endcan
                                <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getRegistrationShareUrl() }}">
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Sdílet') }}">
                                        <i class="bi-share"></i>
                                    </span>
                                </button>
                                @can('delete', $this->registration)
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat registraci') }}">
                                            <i class="bi-trash"></i>
                                        </span>
                                    </button>
                                @endcan
                            @endisset
                            @can('create', [Registration::class, $this->disposition])
                                <button type="button" class="btn btn-sm btn-primary" wire:click="add()" data-bs-toggle="tooltip" data-bs-title="{{ __('Přidat registraci') }}">
                                    <i class="bi-plus-lg"></i>
                                </button>
                            @endcan
                        @endif
                    </div>
                </div>
                @isset($registrationId)
                    <div class="d-none d-print-block">
                        {{ __('Registrace') }} &ndash; {{ $this->registration->name }}
                    </div>
                @endisset
            {{-- nepřihlášeného uživatele lákáme na možnost přidání registrace po přihlášení --}}
            @elseif (!Auth::id() && $this->disposition->isPublic())
                <div class="mt-3 d-print-none">
                    <a class="btn btn-primary btn-sm" href="{{ route('login') }}">
                        <i class="bi-plus-lg"></i> {{ __('Přidat registraci') }}
                    </a>
                </div>
            @endif
            
            {{-- manuály a rejstříky --}}
            <div class="mt-2" wire:loading.class="opacity-25" wire:target.except="accordionToggle">
                <div wire:loading.block wire:target.except="accordionToggle" class="position-fixed text-center w-100 start-0">
                    <x-organomania.spinner />
                </div>
                @if ($isEdit)
                    <div class="col mb-2 lh-lg">
                        @foreach ($this->registrationDispositionFilters as $i => $filter)
                            @if ($filter->getRegisterCount() > 0)
                                <button wire:key="dispositionFilter{{ $i }}" wire:click="addDispositionRegistersFromFilter({{ $i }})" type="button" class="btn btn-sm btn-secondary">
                                    {{ $filter->name }}
                                </button>
                            @endif
                        @endforeach
                        &nbsp;
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="removeAllDispositionRegisters">
                            <i class="bi-x-circle"></i> {{ __('Vypnout vše') }}
                        </button>
                        @can('useAI')
                            &nbsp;
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Naregistrovat s pomocí umělé inteligence') }}">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#suggestRegistrationModal" @click="prefillPiece()">
                                    <i class="bi-magic"></i> {{ __('Naregistrovat s AI') }}
                                </button>
                            </span>
                        @endcan
                    </div>
                @endif
                
                @if ($isEdit)
                    <div>
                        {{ __('Vybráno rejstříků') }}: {{ count(array_filter($dispositionRegisters)) }}
                    </div>
                @endif
                @error('dispositionRegisters')
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('dispositionRegisters') }}
                    </div>
                @enderror
                

                {{-- manuály --}}
                @if ($sort === 'order')
                    <ol
                        @class(['keyboards', 'disposition', 'mb-0', 'keyboards-in-separate-columns' => $keyboardsInSeparateColumns, 'row' => $keyboardsInSeparateColumns, 'row-cols-lg-3' => $keyboardsInSeparateColumns, 'd-inline-block' => !$keyboardsInSeparateColumns, 'g-0'])
                        @style(['min-width: 22em' => !$keyboardsInSeparateColumns])
                        type="I"
                    >
                        @foreach ($disposition->keyboards as $keyboard)
                            <li wire:key="keyboard{{ $keyboard->id }}" @class(['keyboard', 'col', 'mb-3' => !$loop->last, 'exclude-from-numbering' => !$disposition->keyboard_numbering || $keyboard['pedal']]) @style(['list-style-type: none' => $keyboard->pedal])>
                                <div class="border border-tertiary" style="border-right: none !important; border-top: none !important; border-bottom: none !important;">
                                    <div
                                        class="disposition-item disposition-item-padding"
                                        @if ($isEdit)
                                            style="cursor: pointer"
                                            onclick="keyboardLiOnclick(event)"
                                        @endif
                                    >
                                        <div class="fw-bold hstack gap-2" style="min-width: 13em;">
                                            @if ($isEdit)
                                                <input
                                                    @class(['check-all', 'form-check-input', 'd-print-none', 'invisible' => $keyboard->dispositionRegisters->isEmpty()])
                                                    type="checkbox"
                                                    wire:model.change="checkAllKeyboards.{{ $keyboard->id }}"
                                                    checked
                                                />
                                            @endif
                                            {{ $keyboard->name }}
                                            <div class="ms-auto"></div>
                                            <button
                                                type="button"
                                                @class(['btn', 'btn-outline-secondary', 'btn-sm', 'd-print-none', 'rounded-pill', 'invisible' => !$this->hasKeyboardVisibleRegisters($keyboard)])
                                                wire:click="toggleShowRegisters({{ $keyboard->id }})"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="{{ __('Zobrazit/skrýt rejstříky') }}"
                                            >
                                                @if ($this->shouldShowRegisters($keyboard->id))
                                                    <i class="bi-chevron-contract"></i>
                                                @else
                                                    <i class="bi-chevron-expand"></i>
                                                @endif
                                            </button>
                                        </div>
                                    </div>

                                    {{-- rejstříky --}}
                                    @if ($keyboard->dispositionRegisters->isNotEmpty() && $this->shouldShowRegisters($keyboard->id))
                                        <ol class="mt-0 mb-0 ps-0 registers" start="{{ $this->keyboardStartNumbers[$keyboard->id] }}" @style(['list-style-type: none' => !$disposition->numbering])>
                                            @foreach ($keyboard->dispositionRegisters as $register)
                                                @php $checked = $this->dispositionRegisters[$register->id] ?? false @endphp
                                                @php $highlighted = $this->isRegisterHighlighted($register) @endphp

                                                <x-organomania.disposition-register
                                                    :invisible="$this->shouldHideUnregistered && !$checked"
                                                    :$register :$keyboard :$disposition
                                                    :$isEdit :$checked :$highlighted :$keyboardsInSeparateColumns
                                                />
                                            @endforeach
                                        </ol>
                                        @if ($this->showRegisterCounts && $keyboard->real_disposition_registers_count > 0 && !$showOnlyRegistered)
                                            <div class="fst-italic disposition-item-padding text-body-secondary">
                                                <small>{{ __('Znějících rejstříků celkem') }}: {{ $keyboard->real_disposition_registers_count }}</small>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @else
                    {{-- rejstříky (bez členění po manuálech - při setřídění) --}}
                    <ol class="disposition mb-0 d-inline-block" @style(['min-width: 22em', 'list-style-type: none' => !$disposition->numbering])>
                        @foreach ($this->dispositionRegistersSorted as $register)
                            @php $checked = $this->dispositionRegisters[$register->id] ?? false @endphp
                            @php $highlighted = $this->isRegisterHighlighted($register) @endphp

                            <x-organomania.disposition-register
                                :invisible="$this->shouldHideUnregistered && !$checked"
                                :$register :keyboard="$disposition->getDispositionRegisterKeyboard($register)" :$disposition
                                :$isEdit :$checked :$highlighted :keyboardsInSeparateColumns="false"
                                :showKeyboard="true"
                            />
                        @endforeach
                    </ol>
                @endif
            </div>
            
            @if ($this->showRegisterCounts && $disposition->keyboards->count() > 1 && !$showOnlyRegistered)
                <div class="fst-italic mt-2 text-body-secondary">
                    {{ __('Znějících rejstříků celkem') }}: {{ $disposition->real_disposition_registers_count }}
                </div>
            @endif
        @endif
    </form>
        
    {{-- doplňující údaje --}}
    @if (isset($disposition->appendix) && $this->showAppendix)
        <h5 class="mt-3">{{ __('Doplňující informace') }}</h5>
        <div class="pre-line">{{ $this->disposition->appendix }}</div>
    @endif
    @if (isset($disposition->description) && $this->showDescription)
        <h5 class="mt-3">{{ __('Popis') }}</h5>
        <div class="pre-line">{{ $this->disposition->description }}</div>
    @endif

    {{-- statistiky, nastavení --}}
    @if ($disposition->keyboards->isNotEmpty())
        <div class="accordion mt-3 d-print-none">
            <x-organomania.accordion-item
                id="accordion-stats"
                title="{{ __('Statistiky') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_STATS)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_STATS}}')"
            >
                <x-organomania.disposition-stats :$disposition />
            </x-organomania.accordion-item>

            <x-organomania.accordion-item
                id="accordion-view-settings"
                title="{{ __('Nastavení zobrazení') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_VIEW_SETTINGS)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_VIEW_SETTINGS }}')"
            >
                <div class="row row-cols-1 row-cols-lg-2 gy-3">
                    <div class="col">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="keyboardsInSeparateColumns" wire:model.change="keyboardsInSeparateColumns">
                            <label class="form-check-label" for="keyboardsInSeparateColumns">{{ __('Každý manuál v samostatném sloupci') }}</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="showRegisterCounts" wire:model.change="showRegisterCounts">
                            <label class="form-check-label" for="showRegisterCounts">{{ __('Počty rejstříků') }}</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="reedsWithDot" wire:model.change="reedsWithDot">
                            <label class="form-check-label" for="reedsWithDot">{{ __('Jazykové rejstříky s tečkou') }}</label>
                        </div>
                    </div>
                    <div class="col">
                        @isset($disposition->appendix)
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="showAppendix" wire:model.change="showAppendix">
                                <label class="form-check-label" for="showAppendix">{{ __('Doplňující informace') }}</label>
                            </div>
                        @endisset
                        @isset($disposition->description)
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="showDescription" wire:model.change="showDescription">
                                <label class="form-check-label" for="showDescription">{{ __('Popis') }}</label>
                            </div>
                        @endisset
                    </div>
                </div>
            </x-organomania.accordion-item>
        </div>
    @endif
    
    {{-- tlačítka zpět/zavřít --}}
    <div class="buttons mt-3 hstack d-print-none">
        @if ($disposition->keyboards->isNotEmpty())
            <button type="submit" @class(['btn', 'btn-sm', 'btn-outline-secondary', 'disabled' => $isEdit]) onclick="alert('{{ __('Pro správné vytištění zrušte nastavení tisku okrajů stránky.') }}'); window.print()">
                <i class="bi-printer"></i> {{ __('Tisk') }}
            </button>
            &nbsp;
            <div class="btn-group">
                <button type="button" @class(['btn', 'btn-sm', 'btn-outline-secondary', 'disabled' => $isEdit]) wire:click="exportAsPdf"><i class="bi-file-pdf"></i> {{ __('Export do') }} PDF</button>
                <button type="button" @class(['btn', 'btn-sm', 'btn-outline-secondary', 'dropdown-toggle', 'dropdown-toggle-split', 'disabled' => $isEdit]) data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">{{ __('Zobrazit více') }}</span>
                </button>
                <ul class="dropdown-menu">
                    <button class="dropdown-item" href="#" wire:click="exportAsDoc"><i class="bi-filetype-doc"></i> {{ __('Export do') }} DOC</button>
                    <button class="dropdown-item" href="#" wire:click="exportAsCsv"><i class="bi-filetype-csv"></i> {{ __('Export do') }} CSV</button>
                    <button class="dropdown-item" href="#" wire:click="exportAsJson"><i class="bi-filetype-json"></i> {{ __('Export do') }} JSON</button>
                </ul>
            </div>
        @endif
        <a @class(['btn', 'btn-sm', 'btn-secondary', 'ms-auto', 'disabled' => $isEdit]) wire:navigate href="{{ url()->previous() }}">
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>&nbsp;
        @can('update', $disposition)
            <a @class(['btn', 'btn-sm', 'btn-outline-primary', 'disabled' => $isEdit]) wire:navigate href="{{ route('dispositions.edit', $disposition->id) }}" data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit dispozici') }}">
                <i class="bi-pencil"></i> {{ __('Upravit') }}
            </a>
        @endcan
    </div>

    @isset($this->suggestRegistrationInfo)
        <x-organomania.toasts.ai-info-toast title="{{ __('Podrobnosti k registraci') }}">
            {!! trim($this->markdownConvertor->convert($this->suggestRegistrationInfo)) !!}
        </x-organomania.toasts.ai-info-toast>
    @endisset
        
    <x-organomania.modals.categories-modal :categoriesGroups="$this->registerCategoriesGroups" :categoryClass="RegisterCategory::class" :title="__('Přehled kategorií rejstříků')" />
        
    <x-organomania.modals.share-modal />
        
    <x-organomania.modals.register-modal
        :registerName="$registerName"
        :pitch="$pitch"
        :language="$disposition->language"
        :excludeDispositionIds="[$disposition->id]"
        :excludeOrganIds="$disposition->organ_id ? [$disposition->organ_id] : []"
    />

    <x-organomania.modals.confirm-modal
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="$wire.delete()"
    >
        {{ __('Opravdu chcete registraci smazat?') }}
    </x-organomania.modals.confirm-modal>
      
    <x-organomania.modals.suggest-registration-modal />
        
    <x-organomania.toast toastId="suggestRegistrationFail" color="danger">
        {{ __('Omlouváme se, při zjišťování registrace došlo k chybě.') }}
    </x-organomania.toast>
    <x-organomania.toast toastId="suggestRegistrationSuccess">
        {{ __('Registrace byla úspěšně nastavena.') }}
    </x-organomania.toast>
</div>

@push('styles')
    <style>
        @media print {
            @page {
                size: portrait;
                margin: 0;
            }
        }
    </style>
@endpush

@script
<script>
    window.registerLiOnclick = function (e) {
        if (!$(e.target).is("input, .btn, .btn *")) {
            $(e.currentTarget).find('input').trigger('click')
        }
    }
        
    window.keyboardLiOnclick = function (e) {
        if (!$(e.target).is("input, .btn, .btn *")) {
            $(e.currentTarget).find('input.check-all').trigger('click')
        }
    }
        
    window.diffOnchange = function (e) {
        var url = $('#diff').val()
        if (url !== '') Livewire.navigate(url)
    }
        
    window.prefillPiece = function () {
        let registrationName = $('#registrationId').val()
        if (registrationName !== '') {
            $('#suggestRegistrationModal .piece').val(registrationName)
        }
    }
        
    document.addEventListener('livewire:navigated', function () {
        if (location.hash !== '') {
            $(location.hash).get(0).scrollIntoView({behavior: 'smooth'});
        }
    })
</script>
@endscript