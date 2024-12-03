<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use App\Helpers;
use App\Livewire\Forms\RegistrationSetForm;
use App\Models\Disposition;
use App\Models\RegistrationSet;
use App\Events\EntityCreated;
use App\Events\EntityUpdated;
use App\Events\EntityDeleted;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    #[Locked]
    public Disposition $disposition;

    #[Locked]
    public RegistrationSet $registrationSet;

    public RegistrationSetForm $form;

    public $previousUrl;

    private $select2Rendered = false;

    public function boot()
    {
        // příslušnost registrationSet k disposition měla být zkontrolována už v routě pomocí scopeBindings(), ale ve Voltu zřejmě nefunguje
        if (
            isset($this->registrationSet)
            && $this->registrationSet?->exists
            && !$this->disposition->registrationSets->contains($this->registrationSet)
        ) {
            abort(404);
        }
    }

    public function rendering(View $view): void
    {
        $title = __($this->getTitle());
        $view->title($title);
    }

    public function mount()
    {
        $this->registrationSet ??= new RegistrationSet();
        if ($this->registrationSet->exists) $this->authorize('update', $this->registrationSet);
        else $this->authorize('create', $this->disposition);

        $data = Helpers::arrayKeysCamel($this->registrationSet->toArray());
        $data['registrations'] = $this->registrationSet->registrations->pluck('id')->toArray();
        $this->form->fill($data);

        $this->previousUrl = request()->headers->get('referer');
    }

    public function delete()
    {
        if (!$this->registrationSet->exists) throw new \RuntimeException;

        $this->authorize('delete', $this->registrationSet);
        $this->registrationSet->delete();
        EntityDeleted::dispatch($this->registrationSet);

        session()->flash('status-success', __('Sada registrací byla úspěšně smazána.'));
        $this->redirectBack();
    }

    public function save()
    {
        $this->form->validate();
        $data = Helpers::arrayKeysSnake($this->form->except(['registrations']));
        if (!$this->isRegistrationSetPublic()) $this->registrationSet->user_id = Auth::id();
        $this->registrationSet->fill($data);
        $this->registrationSet->disposition()->associate($this->disposition);
        $this->registrationSet->save();
        $this->registrationSet->registrations()->sync($this->getRegistrationsForSave());

        if ($this->registrationSet->exists) EntityUpdated::dispatch($this->registrationSet);
        else EntityCreated::dispatch($this->registrationSet);

        session()->flash('status-success', __('Sada registrací byla úspěšně uložena.'));
        $this->redirectBack();
    }

    private function getRegistrationsForSave()
    {
        $order = 1;
        $registrations = [];
        foreach (array_filter($this->form->registrations) as $registrationId) {
            $registrations[$registrationId] = ['order' => $order++];
        }
        return $registrations;
    }

    private function redirectBack()
    {
        if (isset($this->previousUrl)) $this->redirect($this->previousUrl, navigate: true);
        else $this->redirectRoute('dispositions.registrationSets.index', ['disposition' => $this->disposition->slug], navigate: true);
    }

    private function isRegistrationSetPublic()
    {
        return false;
    }

    private function getExcludedRegistrationIds($registrationIndex)
    {
        $registrations = $this->form->registrations;
        unset($registrations[$registrationIndex]);
        return array_filter($registrations);
    }

    public function getTitle()
    {
        if ($this->registrationSet->exists) return 'Upravit sadu registrací';
        else return 'Přidat sadu registrací';
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        if (!$this->select2Rendered) {
            $this->dispatch('select2-rendered');
            $this->select2Rendered = true;
        }
        $this->dispatch('select2-sync-needed', componentName: 'pages.registration-set-edit');
    }

    public function addRegistration()
    {
        $this->form->registrations[] = '';

        $this->dispatch('select2-rendered');
        $this->select2Rendered = true;
        $registrationId = array_key_last($this->form->registrations);
        $this->dispatch('select2-open', selector: "#registrationId$registrationId");
    }

    public function deleteRegistration($registrationIndex)
    {
        unset($this->form->registrations[$registrationIndex]);
        $this->form->registrations = array_values($this->form->registrations);
    }

    public function moveRegistration($registrationIndex, $direction = 'up')
    {
        $newRegistrationIndex = $direction === 'up' ? $registrationIndex - 1 : $registrationIndex + 1;
        if (!isset($this->form->registrations[$newRegistrationIndex])) throw new \RuntimeException;
        Helpers::swap($this->form->registrations[$registrationIndex], $this->form->registrations[$newRegistrationIndex]);
    }

    #[Computed]
    public function registrationsCount()
    {
        return collect($this->form->registrations)->filter()->count();
    }
    
}; ?>

<div class="registration-set-edit container">
    <form method="post" wire:submit="save" wire:keydown.ctrl.enter="save">
        <h3>
            {{ __($this->getTitle()) }}
            @if (!$this->isRegistrationSetPublic())
                <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
            @endif
        </h3>
            
        <div class="mb-4">
            <div class="row g-3">
                <div>
                    <div class="form-floating mb-3">
                        <input class="form-control form-control-lg @error('form.name') is-invalid @enderror" id="name" wire:model.blur="form.name" aria-describedby="nameFeedback" autofocus>
                        <label for="name">{{ __('Název') }}</label>
                        @error('form.name')
                            <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span class="form-text">{{ __('např. Varhanní koncert') }} 1. 1. {{ date('Y') }}</span>
                    </div>
                </div>
            </div>
            
            <ol class="mt-0 mb-0 ps-0">
                @foreach ($this->form->registrations as $registrationIndex => $registrationId)
                    <li class="registration hstack gap-2" wire:key="{{ $registrationId }}">
                        <x-organomania.move-buttons
                            actionUp="moveRegistration({{ $registrationIndex }}, 'up')"
                            actionDown="moveRegistration({{ $registrationIndex }}, 'down')"
                            moveWhat="registraci"
                            :isFirst="$loop->first"
                            :isLast="$loop->last"
                        />
                        <span class="w-100">
                            <x-organomania.selects.registration-select
                                id="registrationId{{ $registrationIndex }}"
                                model="form.registrations.{{ $registrationIndex }}"
                                :registrations="$disposition->registrations"
                                :excludeRegistrationIds="$this->getExcludedRegistrationIds($registrationIndex)"
                            />
                        </span>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            wire:click="deleteRegistration({{ $registrationIndex }})"
                        >
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat') }}">
                                <i class="bi-trash"></i>
                            </span>
                        </button>
                    </li>
                @endforeach
            </ol>
            
            @if ($this->registrationsCount > 0)
                <small class="text-body-secondary fst-italic">
                    {{ __('Registrací celkem') }}: {{ $this->registrationsCount }}
                </small>
            @endif
            
            <div class="mt-3">
                <button type="button" class="btn btn-primary btn-sm" wire:click="addRegistration">
                    <i class="bi-plus-lg"></i> {{ __('Registrace') }}
                </button>
            </div>
        </div>
    
        <div class="hstack">
            @if ($this->registrationSet->exists)
                <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat sadu registrací') }}">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        <i class="bi-trash"></i> {{ __('Smazat') }}
                    </button>
                </span>
            @endif
                
            <a class="btn btn-sm btn-secondary ms-auto" href="{{ url()->previous() }}"><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
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
        {{ __('Opravdu chcete sadu registrací smazat?') }}
    </x-organomania.modals.confirm-modal>
</div>
