<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use App\Models\Disposition;
use App\Models\RegistrationSet;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public Disposition $disposition;

    public function boot()
    {
        $this->disposition->load(['registrationSets' => function (Builder $query) {
            $query->withCount('registrations');
        }]);
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome')) {
            return route('dispositions.show', $this->disposition->slug);
        }
        return $previousUrl;
    }

    public function rendering(View $view): void
    {
        $view->title($this->getTitle());
    }

    public function rendered(View $view): void
    {
        $this->dispatch('bootstrap-rendered');
    }

    private function getTitle()
    {
        $title = __('Sady registrací');
        $title .= " – {$this->disposition->name}";
        return $title;
    }

    private function getRouteUrl(RegistrationSet $registrationSet, $signed = false)
    {
        $parameters = [
            'disposition' => $this->disposition->slug,
            'registrationSet' => $registrationSet->slug,
        ];
        $route = 'dispositions.registration-sets.show';

        if ($signed) $relativeUrl = URL::signedRoute($route, $parameters, absolute: false);
        else $relativeUrl = route($route, $parameters, absolute: false);
        return url($relativeUrl);
    }

    private function getViewUrl(RegistrationSet $registrationSet)
    {
        $signed = !Gate::allows('view', $registrationSet);
        return $this->getRouteUrl($registrationSet, $signed);
    }

    private function getShareUrl(RegistrationSet $registrationSet)
    {
        $signed = isset($registrationSet->user_id);
        return $this->getRouteUrl($registrationSet, $signed);
    }
    
}; ?>

<div class="registration-sets container">
    <h3>
        {{ $this->getTitle() }}
    </h3>
    
    <p class="lead mb-3">
        {{ __('Uspořádejte si uložené registrace do sad určených pro konkrétní příležitost. Mezi registracemi se pak můžete snadno přepínat nebo je nasdílet dalším lidem (např. posluchačům koncertu).') }}
    </p>
    
    <a class="btn btn-sm btn-primary mb-3" href="{{ route('dispositions.registration-sets.create', ['disposition' => $disposition->slug]) }}">
        <i class="bi bi-plus-lg"></i>
        {{ __('Přidat') }}
    </a>
    
    @if ($disposition->registrationSets->isEmpty())
        <div class="alert alert-secondary text-center" role="alert">
            {{ __('Nebyly nalezeny žádné sady registrací.') }}
        </div>
    @else
        <table class="table table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>{{ __('Název') }}</th>
                    <th class="text-end">{!! __('Počet registrací') !!}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($disposition->registrationSets as $registrationSet)
                    <tr>
                        <td>
                            <i class="bi bi-lock text-warning"></i>
                        </td>
                        <td class="fw-semibold">
                            <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ $this->getViewUrl($registrationSet) }}" wire:navigate>
                                {{ $registrationSet->name }}
                            </a>
                        </td>
                        <td class="text-end">
                            @if ($registrationSet->registrations_count > 0)
                                <span class="badge text-bg-secondary rounded-pill">
                                    {{ $registrationSet->registrations_count }}
                                </span>
                            @else
                                <span class="badge text-bg-light rounded-pill">
                                    0
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group col-auto">
                                <a class="btn btn-sm btn-primary" href="{{ $this->getViewUrl($registrationSet) }}" wire:navigate data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit') }}">
                                    <i class="bi-eye"></i>
                                </a>
                                @can('update', $registrationSet)
                                    <a
                                        class="btn btn-sm btn-outline-primary"
                                        href="{{ route('dispositions.registration-sets.edit', ['disposition' => $disposition->slug, 'registrationSet' => $registrationSet->id]) }}"
                                        wire:navigate
                                        data-bs-toggle="tooltip"
                                        data-bs-title="{{ __('Upravit') }}"
                                    >
                                        <i class="bi-pencil"></i>
                                    </a>
                                @endcan
                                <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($registrationSet) }}">
                                    <span data-bs-toggle="tooltip" data-bs-title="{{ __('Sdílet') }}">
                                        <i class="bi-share"></i>
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <x-organomania.modals.share-modal />
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate>
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>
    </div>
</div>
