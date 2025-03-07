<?php

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use App\Models\Organ;
use App\Models\Region;
use App\Rules\UppercaseFirst;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    #[Locked]
    public Organ $organ;

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

    #[Validate('required', message: 'Kraj musí být vyplněn.')]
    public $regionId;

    public function rendering(View $view): void
    {
        $view->title(__('Přidat varhany'));
    }

    public function mount()
    {
        $this->organ = new Organ();
        $this->authorize('create', $this->organ);
    }

    public function save()
    {
        $this->validate();

        $this->organ->municipality = $this->municipality;
        $this->organ->place = $this->place;
        $this->organ->region_id = $this->regionId;
        $this->organ->latitude = $this->organ->longitude = 1;
        $this->organ->importance = 1;
        $this->organ->concert_hall = 0;
        $this->organ->user_id = Auth::user()->id;
        $this->organ->save();

        $message = __('Varhany byly úspěšně uloženy.') . ' ' . __('Chcete-li doplnit podrobnější údaje o varhanách, klikněte na tlačítko Upravit ve spodní části stránky.');
        session()->flash('status-success', $message);
        $this->redirectRoute('organs.show', ['organSlug' => $this->organ->slug], navigate: true);
    }

    #[Computed]
    public function regions()
    {
        return Region::query()->orderBy('name')->get();
    }

    public function rendered()
    {
        $this->dispatch("select2-rendered");
    }
    
}; ?>

<div class="organ-edit container">
    <form method="post" wire:submit="save" wire:keydown.ctrl.enter="save">
        <h3>
            {{ __('Přidat varhany') }}
            <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
        </h3>
        
        <div class="mb-4 mt-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('municipality') is-invalid @enderror" id="municipality" wire:model.live="municipality" aria-describedby="municipalityFeedback" autofocus>
                        <label for="municipality">{{ __('Obec') }}</label>
                        @error('municipality')
                            <div id="municipalityFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-floating">
                        <input class="form-control form-control-lg @error('place') is-invalid @enderror" id="place" wire:model.live="place" aria-describedby="placeFeedback">
                        <label for="place">{{ __('Místo') }}</label>
                        @error('place')
                            <div id="placeFeedback" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="regionId" class="form-label @error("regionId") is-invalid @enderror">{{ __('Kraj') }} <span class="text-danger">*</span></label>
                    <x-organomania.selects.region-select :regions="$this->regions" id="regionId" model="regionId" />
                    @error('regionId')
                        <div id="regionIdFeedback" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    
        <div class="text-end">
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
</div>
