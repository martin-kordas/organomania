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

}; ?>

<div class="dispositions container">
  
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
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle w-auto mt-2" style="min-width: 40vw;">
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
                            <a class="link-dark link-underline-opacity-10 link-underline-opacity-50-hover" href="#" data-bs-toggle="modal" data-bs-target="#registerModal" wire:click="setRegisterName({{ $registerName->id }})">
                                {{ $registerName->name }}
                            </a>
                        </td>
                        <td>{{ $registerName->language->value }}</td>
                        <td>
                            <span
                                class="badge text-bg-primary"
                                data-bs-toggle="tooltip"
                                data-bs-title="{{ $registerName->register->registerCategory->getDescription() }}"
                            >{{ $registerName->register->registerCategory->getName() }}</span>
                            @foreach ($registerName->register->registerCategories as $category)
                                @php $categoryDescription = $category->getEnum()->getDescription(); @endphp
                                <span
                                    class="badge text-bg-secondary"
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
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" wire:click="setRegisterName({{ $registerName->id }})" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit podrobnosti') }}">
                                    <i class="bi-eye"></i>
                                </span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{ $this->registerNames->links() }}
    
    <h6>{{ __('Použitá literatura') }}</h6>
    <small>
        <ul>
            <li>BĚLSKÝ, Vratislav. Nauka o varhanách. 4. vyd., (V Editio Bärenreiter Praha vyd. 1.). Praha: Editio Bärenreiter, 2000. ISBN 80-86385-04-3.</li>
            <li>SYROVÝ, Václav. Kapitoly o varhanách. Vyd. 2., dopl., přeprac. Akustická knihovna Zvukového studia Hudební fakulty AMU. V Praze: Akademie múzických umění, 2004. ISBN 80-7331-009-0.</li>
            <li>Encyclopedia of Organ Stops. Online. 2024. Dostupné z: http://www.organstops.org/.</li>
        </ul>
    </small>
    
    <x-organomania.modals.register-modal :registerName="$this->registerName" />
        
</div>