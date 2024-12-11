<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Disposition;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use WithPagination;

    public $searchName = '';

    public function rendering(View $view): void
    {
        $view->title(__('Dispozice varhan'));
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
    }

    #[Computed]
    public function dispositions()
    {
        return Disposition::query()
            ->with(['organ'])
            ->withCount([
                'registrations',
                'manuals',
                'realDispositionRegisters',
            ])
            ->when($this->searchName != '', function (Builder $query) {
                $query->where('name', 'LIKE', "%{$this->searchName}%");
            })
            ->orderBy('name')
            ->paginate(15);
    }

    private function getShareUrl(Disposition $disposition)
    {
        $fn = isset($disposition->user_id) ? URL::signedRoute(...) : route(...);
        $absolute = false;      // varianta s named argumenty z neznámého důvodu nefunguje, je-li $disposition->organ_id === null
        $relativeUrl = $fn('dispositions.show', $disposition->slug, null, $absolute);
        return url($relativeUrl);
    }

}; ?>

<div class="dispositions container px-0">
    @push('meta')
        <meta name="description" content="{{ __('Prozkoumejte a srovnejte dispozice známých varhan nebo vytvořte dispozici vlastní. Zjistěte informace o jednotlivých rejstřících a uložte si registrace varhanních skladeb.') }}">
    @endpush
    
    <p class="lead">
        {{ __('Interaktivně prozkoumejte a srovnejte dispozice známých varhan, nebo vytvořte dispozici vlastní.') }}
        <br class="d-none d-sm-inline" />
        {{ __('Uložte si k dispozicím registrace konkrétních skladeb.') }}
        <br class="d-none d-sm-inline" />
        {{ __('Vše sdílejte se svými kolegy a dalšími zájemci.') }}
    </p>
    
    <x-organomania.info-alert class="mb-0 d-inline-block">
        {!! __('<strong>Varhanní dispozice</strong> je souhrnem zvukových a&nbsp;technických vlastností varhan.') !!}
        <br class="d-none d-md-inline" />
        {!! __('Kromě seznamu rejstříků a&nbsp;pomocných zařízení zahrnuje také základní technickou charakteristiku varhan.') !!}
    </x-organomania.info-alert>
    
    <div class="row mt-3 mb-2 gx-2 gy-2">
        <div class="col-9 col-sm-auto">
            <input class="form-control form-control-sm" size="35" type="search" wire:model.live="searchName" placeholder="{{ __('Hledat dispozice') }}&hellip;" />
        </div>
        <div class="col-3 col-sm-auto me-2">
            <div class="btn-group float-end">
                {{-- wire:navigate: nefunguje v nepřihlášeném stavu --}}
                <a class="btn btn-sm btn-primary" href="{{ route('dispositions.create') }}">
                    <i class="bi-plus-lg"></i> {{ __('Přidat') }}
                </a>
                @can('createPublic', Disposition::class)
                    <a class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">{{ __('Zobrazit více') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('dispositions.create', ['public' => '1']) }}" wire:navigate>{{ __('Přidat veřejně') }}</a></li>
                    </ul>
                @endcan
            </div>
        </div>
        <div class="col-auto">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('dispositions.registers.index') }}" wire:navigate>
                <i class="bi-record-circle"></i> {{ __('Encyklopedie rejstříků') }}
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('dispositions.diff') }}" wire:navigate>
                <i class="bi-arrow-left-right"></i> {{ __('Porovnání dispozic') }}
            </a>
        </div>
    </div>
    
    @if ($this->dispositions->isEmpty())
        <div class="alert alert-secondary text-center mt-4" role="alert">
            {{ __('Nebyly nalezeny žádné dispozice.') }}
        </div>
    @else
        <div>
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle w-100" style="min-width: 40vw;">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>{{ __('Název dispozice') }} <i class="bi-sort-alpha-up"></i></th>
                            <th>{{ __('Varhany') }}</th>
                            <th class="text-end">{{ __('Man.') }}</th>
                            <th class="text-end">{{ __('Rejstříků') }}</th>
                            @if (Auth::check())
                                <th class="text-end">{!! __('Uloženo<br />registrací') !!}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($this->dispositions as $disposition)
                            <tr>
                                <td>
                                    @if ($disposition->user_id)
                                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                                            <i class="bi-lock text-warning"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-semibold" style="min-width: 15em;">
                                    <a class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover" href="{{ route('dispositions.show', $disposition->slug) }}" wire:navigate>
                                        {{ $disposition->name }}
                                    </a>
                                </td>
                                <td>
                                    @isset($disposition->organ)
                                        <x-organomania.organ-organ-builder-link :organ="$disposition->organ" :showIcon="false" />
                                    @else
                                        &ndash;
                                    @endisset
                                </td>
                                <td class="text-end">{{ $disposition->keyboard_numbering ? $disposition->manuals_count : '' }}</td>
                                <td class="text-end">{{ $disposition->real_disposition_registers_count }}</td>
                                @if (Auth::check())
                                    <td class="text-end">
                                        @if ($disposition->registrations_count > 0)
                                            <span class="badge text-bg-secondary rounded-pill">
                                                {{ $disposition->registrations_count }}
                                            </span>
                                        @else
                                            <span class="badge text-bg-light rounded-pill">
                                                0
                                            </span>
                                        @endif
                                    </td>
                                @endif
                                <td class="ps-4">
                                    <div class="btn-group col-auto">
                                        <a class="btn btn-sm btn-primary" href="{{ route('dispositions.show', $disposition->slug) }}" wire:navigate data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit') }}">
                                            <i class="bi-eye"></i>
                                        </a>
                                        @can('update', $disposition)
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('dispositions.edit', $disposition->id) }}" wire:navigate data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit') }}">
                                                <i class="bi-pencil"></i>
                                            </a>
                                        @endcan
                                        <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($disposition) }}">
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
            </div>

            {{ $this->dispositions->links() }}
        </div>
    @endif
    
    <x-organomania.modals.share-modal />
        
</div>
