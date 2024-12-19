<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use App\Helpers;
use App\Models\Disposition;
use App\Models\RegistrationSet;
use App\Models\Registration;
use App\Models\Scopes\OwnedEntityScope;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Disposition $disposition;

    #[Locked]
    public RegistrationSet $registrationSet;

    #[Session]
    public $lastVisitedRegistrationId = null;

    public function boot()
    {
        $removeOwnedEntityScope = fn(Builder $query) => $query->withoutGlobalScope(OwnedEntityScope::class);
        $signed = request()->hasValidSignature(false);

        if ($signed) {
            $this->disposition->load([
                'registrationSets' => $removeOwnedEntityScope,
                'organ' => $removeOwnedEntityScope,
            ]);
        }
        $this->registrationSet->load(['registrations' => function (Builder $query) use ($signed, $removeOwnedEntityScope) {
            if ($signed) $removeOwnedEntityScope($query);
            $query->withCount('realDispositionRegisters');
        }]);

        // příslušnost registrationSet k disposition měla být zkontrolováno už v routě pomocí scopeBindings(), ale ve Voltu zřejmě nefunguje
        if (!$this->disposition->registrationSets->contains($this->registrationSet)) {
            abort(404);
        }
    }

    public function rendering(View $view): void
    {
        $view->title($this->registrationSet->name);
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome')) {
            return route('dispositions.registration-sets.index', $this->disposition->slug);
        }
        return $previousUrl;
    }

    #[Computed]
    private function organDescription()
    {
        $description = $this->disposition->organ->perex ?? null;
        if (!isset($description)) {
            $description = $this->disposition->organ->description ?? null;
            if (isset($description)) $description = str($description)->limit(270);
        }
        return $description;
    }

    private function getRegistrationUrl(Registration $registration)
    {
        $fn = !Gate::allows('view', $registration) ? URL::signedRoute(...) : route(...);
        $relativeUrl = $fn(
            'dispositions.show',
            [$this->disposition->slug, 'registrationId' => $registration->id],
            absolute: false
        );
        return url($relativeUrl);
    }
    
}; ?>

<div class="registration-set-show container">
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fw-bold" @if (Auth::user()?->admin) title="ID: {{ $registrationSet->id }}" @endif>
                {{ $registrationSet->name }}
            </h3>
          
            @isset ($disposition->organ)
                <h5>{{ $disposition->organ->place }}, {{ $disposition->organ->municipality }}</h5>
            @endisset
        </div>
        
        <div class="text-center">
            <div class="position-relative d-inline-block">
                @isset ($disposition->organ->image_url)
                    <a href="{{ $disposition->organ->image_url }}" target="_blank">
                        <img class="organ-img rounded border" src="{{ $disposition->organ->image_url }}" @isset($disposition->organ->image_credits) title="{{ __('Licence obrázku') }}: {{ $disposition->organ->image_credits }}" @endisset height="200" />
                    </a>
                @endisset
            </div>
        </div>
    </div>
    
    @isset($disposition->organ)
        <div class="mb-4">
            <h4>{{ __('Informace o varhanách') }}</h4>
            
            <table class="table">
                <tr>
                    <th>{{ __('Postavil') }}</th>
                    <td>
                        <x-organomania.organ-builder-link :organBuilder="$disposition->organ->organBuilder" :yearBuilt="$disposition->organ->year_built" />
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Velikost') }}</th>
                    <td>
                        {{ $disposition->organ->manuals_count }} <small>{{ $disposition->organ->getDeclinedManuals() }}</small>
                        @if ($disposition->organ->stops_count)
                            / {{ $disposition->organ->stops_count }} <small>{{ $disposition->organ->getDeclinedStops() }}</small>
                        @endif
                    </td>
                </tr>
                @isset($this->organDescription)
                    <tr>
                        <th>{{ __('Popis') }}</th>
                        <td title="{{ $disposition->organ->perex ?? $disposition->organ->description }}">
                            {{ $this->organDescription }}
                        </td>
                    </tr>
                @endisset
            </table>
            
            {{-- TODO: z těchto odkazů odstraněno wire:navigate, protože při vrácení tlačítkem Zpět pak na serveru nefunguje přepínání registrací --}}
            <a class="btn btn-sm btn-primary" href="{{ route('organs.show', $disposition->organ->slug) }}">
                <i class="bi bi-eye"></i>
                {{ __('Zobrazit podrobnosti') }}
            </a>
            &nbsp;
            <a class="btn btn-sm btn-outline-primary" href="{{ route('dispositions.show', $disposition->slug) }}">{{ __('Dispozice varhan') }}</a>
        </div>
    @endisset

    @if ($registrationSet->registrations->isNotEmpty())
        <h4>{{ __('Registrace skladeb') }}</h4>
        <x-organomania.info-alert class="mb-2 d-print-none">
            {!! __('Registrace představuje <strong>seznam varhanních rejstříků</strong>, které jsou pro danou skladbu zapnuty.') !!}
            {!! __('Volba rejstříků určuje výslednou sílu a barvu zvuku.') !!}
        </x-organomania.info-alert>

        <div class="list-group">
            @foreach ($registrationSet->registrations as $registration)
                @php $isActive = $registration->id === $lastVisitedRegistrationId; @endphp
                <a
                    @class(['list-group-item', 'list-group-item-action', 'd-flex', 'align-items-center', 'column-gap-2', 'active' => $isActive, 'link-primary' => !$isActive])
                    href="{{ $this->getRegistrationUrl($registration) }}#registrationsList"
                    data-registration-id="{{ $registration->id }}"
                    onclick="showRegistration(event)"
                    wire:key="{{ $registration->id }}"
                >
                    <span class="me-auto">
                        {{ $registration->name }}
                    </span>
                    <small @class(['text-body-secondary' => !$isActive])>
                        {{ $registration->real_disposition_registers_count }}&nbsp;{{ __(Helpers::declineCount($registration->real_disposition_registers_count, 'rejstříků', 'rejstřík', 'rejstříky')); }}
                    </small>
                </a>
            @endforeach
        </div>
    @endif
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate>
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>
        @can('update', $registrationSet)
            <a
                class="btn btn-sm btn-outline-primary"
                wire:navigate
                href="{{ route('dispositions.registration-sets.edit', ['disposition' => $disposition->slug, 'registrationSet' => $registrationSet->id]) }}"
                data-bs-toggle="tooltip"
                data-bs-title="{{ __('Upravit dispozici') }}"
            >
                <i class="bi-pencil"></i> {{ __('Upravit') }}
            </a>
        @endcan
    </div>
</div>

@script
<script>
    window.showRegistration = function (e) {
        e.preventDefault()
        
        let url = $(e.currentTarget).attr('href')
        let registrationId = $(e.currentTarget).data('registrationId')
        $wire.$set('lastVisitedRegistrationId', registrationId)
        location.href = url
    }
</script>
@endscript
