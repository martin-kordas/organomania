<?php

use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\RegisterName;
use App\Enums\DispositionLanguage;
use App\Enums\RegisterCategory;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use WithPagination;

    #[Url(keep: true)]
    public $filterName = '';
    #[Url(keep: true)]
    public $filterLanguage;
    #[Url(keep: true)]
    public $filterCategories;

    public ?RegisterName $registerName = null;

    public function rendering(View $view): void
    {
        $view->title(__('Encyklopedie rejstříků'));
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->filterLanguage = $this->getDefaultLanguage();
    }

    private function getDefaultLanguage()
    {
        return DispositionLanguage::getDefault();
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
        $this->dispatch('select2-rendered');
    }

    public function setFilterCategory($categoryId)
    {
        $this->filterCategories = [$categoryId];
        $this->resetPage();
    }

    #[Computed]
    public function registerNames()
    {
        $records = RegisterName::query()
            ->with([
                'register',
                'register.registerCategories',
                'register.registerPitches',
            ])
            ->when($this->filterName != '', function (Builder $query) {
                $query->whereHas('register.registerNames', function (Builder $query) {
                    $query->where('name', 'LIKE', "%{$this->filterName}%");
                });
            })
            ->when($this->filterLanguage != '', function (Builder $query) {
                $query->where('language', $this->filterLanguage);
            })
            ->when(!empty($this->filterCategories), function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query
                        ->whereHas(
                            'register',
                            fn(Builder $query) => $query->whereIn('register_category_id', $this->filterCategories)
                        )
                        ->orWhereHas(
                            'register.registerCategories',
                            fn(Builder $query) => $query->whereIn('register_category_id', $this->filterCategories)
                        );
                });
            })
            ->orderBy('name')
            ->get()
            ->unique(
                // pro každý jazyk zobrazujeme jen 1 variantu názvu rejstříku, aby se nekupily podobné názvy (Prinzipal, Principal...)
                // TODO: řadí se dle name, proto se upřednostní abecedně první rejstřík, raději bychom zvolili první uvedení (řazení dle id)
                //  - např. místo "Copl minor" upřednostni "Copula minor"
                fn(RegisterName $registerName) => "{$registerName->register_id}"
            );
        
        // https://stackoverflow.com/a/63625165/14967413
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 12;
        return new LengthAwarePaginator(
            $records->forPage($page, $perPage), $records->count(), $perPage, $page, ['path' => Paginator::resolveCurrentPath()]
        );
    }

    #[Computed]
    public function registerCategoriesGroups()
    {
        return RegisterCategory::getCategoryGroups();
    }

    public function setRegisterName($id)
    {
        if (config('custom.simulate_loading')) usleep(300_000);
        $this->registerName = RegisterName::find($id);
    }

    private function getShareUrl(RegisterName $registerName)
    {
        return route('dispositions.registers.show', $registerName->slug);
    }

}; ?>

<div class="dispositions container">
  
    @push('meta')
        <meta name="description" content="{{ __('Prozkoumejte názvy, typy a zvukovou charakteristiku varhanních rejstříků. Prohlédněte si dispozice varhan, ve kterých jsou rejstříky obsaženy.') }}">
    @endpush
    
    <h3>{{ __('Encyklopedie rejstříků') }}</h3>
    
    <div class="row mt-3 mb-2 gx-2 gy-2 align-items-center">
        <div class="col-9 col-sm-auto">
            <input class="form-control form-control-sm" size="28" type="search" wire:model.live="filterName" placeholder="{{ __('Hledat podle názvu') }}&hellip;" />
        </div>
        <div class="col-9 col-sm-auto">
            <select id="language" class="form-select form-select-sm" wire:model.change="filterLanguage">
                <option value="">({{ __('jakýkoli jazyk') }})</option>
                @foreach (DispositionLanguage::cases() as $language)
                    <option value="{{ $language->value }}">{{ $language->getName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-9 col-sm-auto">
            <x-organomania.selects.register-category-select
                :categoriesGroups="$this->registerCategoriesGroups"
                :allowClear="true"
                model="filterCategories"
            />
        </div>
        <div class="col-9 col-sm-auto">
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#categoriesModal">{{ __('Přehled kategorií rejstříků') }}</button>
        </div>
    </div>
    
    <div class="w-100">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mt-2">
                <thead>
                    <tr>
                        <th>{{ __('Název') }} <i class="bi-sort-alpha-up"></i></th>
                        <th>{{ __('Jazyk') }}</th>
                        <th>{{ __('Kategorie') }}</th>
                        <th>{{ __('Běžné polohy') }}</th>
                        <th class="d-none d-md-table-cell">{{ __('Popis') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @if ($this->registerNames->isEmpty())
                        <tr>
                            <td colspan="6" class="text-body-secondary text-center">
                                <small>{{ __('Nebyly nalezeny žádné rejstříky.') }}</small>
                            </td>
                        </tr>
                    @endif
                    @foreach ($this->registerNames as $registerName)
                        <tr>
                            <td class="fw-bold">
                                <a
                                    class="link-dark link-underline-opacity-25 link-underline-opacity-75-hover"
                                    href="{{ route('dispositions.registers.show', $registerName->slug) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#registerModal"
                                    wire:click="setRegisterName({{ $registerName->id }})"
                                >
                                    {{ $registerName->name }}
                                </a>
                            </td>
                            <td>{{ $registerName->language->value }}</td>
                            <td>
                                @php $registerCategory = $registerName->register->registerCategory @endphp
                                <span
                                    class="badge text-bg-primary"
                                    wire:click="setFilterCategory({{ $registerCategory->value }})"
                                    style="cursor: pointer;"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $registerCategory->getDescription() }}"
                                >{{ $registerCategory->getName() }}</span>
                                @foreach ($registerName->register->registerCategories as $category)
                                    @php $categoryDescription = $category->getEnum()->getDescription(); @endphp
                                    <span
                                        class="badge text-bg-secondary"
                                        wire:click="setFilterCategory({{ $category->id }})"
                                        style="cursor: pointer;"
                                        @isset($categoryDescription)
                                            data-bs-toggle="tooltip"
                                            data-bs-title="{{ $categoryDescription }}"
                                        @endisset
                                    >{{ $category->getEnum()->getName() }}</span>
                                @endforeach
                            </td>
                            <td class="fst-italic">
                                {{ $registerName->register->getPitchesLabels($this->getDefaultLanguage())->implode(', ') }}
                            </td>
                            <td title="{{ $registerName->register->description }}" class="d-none d-md-table-cell">
                                <small>{{ str($registerName->register->description)->limit(80) }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group col-auto">
                                    <a
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        wire:click="setRegisterName({{ $registerName->id }})"
                                        href="{{ route('dispositions.registers.show', $registerName->slug) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#registerModal"
                                    >
                                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit') }}">
                                            <i class="bi-eye"></i>
                                        </span>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($registerName) }}">
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

        {{ $this->registerNames->links() }}
    </div>
    
    <a class="link-primary text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#registersReferencesModal">
        <small>{{ __('Použitá literatura') }}</small>
    </a>
    
    <x-organomania.modals.categories-modal :categoriesGroups="$this->registerCategoriesGroups" :categoryClass="RegisterCategory::class" />
    <x-organomania.modals.register-modal :registerName="$this->registerName" :categoriesAsLink="true" />
    <x-organomania.modals.registers-references-modal />
    <x-organomania.modals.share-modal />
        
</div>
