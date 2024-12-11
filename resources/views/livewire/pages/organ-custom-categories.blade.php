<?php

use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use App\Models\OrganBuilderCustomCategory;
use App\Models\OrganCustomCategory;
use App\Models\CustomCategory;
use App\Repositories\AbstractRepository;
use App\Repositories\OrganRepository;
use App\Repositories\OrganBuilderRepository;
use App\Traits\ConvertEmptyStringsToNull;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use ConvertEmptyStringsToNull;

    private CustomCategory $genericCategory;
    private ?CustomCategory $category;
    public bool $isEdit = false;
    #[Locked]
    public $id;

    #[Validate]
    public $name;
    #[Validate]
    public $description;

    private AbstractRepository $repository;

    public $route;

    private string $title;
    private string $recordsRoute;
    private string $backRoute;
    private string $recordsCountProp;
    private string $recordEntityName;
    private string $recordEntityIcon;
    private string $showRecordsText;
    private string $shareModalHint;
    private string $createCategoryMessage;
    private string $addItemsMessage;

    public function boot()
    {
        $this->route ??= request()->route()->getName();

        switch ($this->route) {
            case 'organs.organ-custom-categories':
                $this->repository = new OrganRepository;
                $this->title = __('Vlastní kategorie varhan');
                $this->genericCategory = new OrganCustomCategory;
                $this->recordsRoute = 'organs.organ-custom-categories.organs';
                $this->backRoute = 'organs.index';
                $this->recordsCountProp = 'organs_count';
                $this->recordEntityName = __('Varhany');
                $this->recordEntityIcon = 'music-note-list';
                $this->showRecordsText = __('Zobrazit varhany v této kategorii');
                $this->shareModalHint = __('Sdílením kategorie sdílíte všechny varhany v ní obsažené.');
                $this->createCategoryMessage = __('Vytvořte si vlastní pojmenovanou kategorii (skupinu) varhan. Přidejte do ní varhany ve vašem okolí, varhany, které jste navštívili, nebo varhany, na kterých probíhá váš varhanní festival. Tlačítkem Sdílet zašlete skupinu varhan svým známým.');
                $this->addItemsMessage = __('Pro přidání varhan do kategorie jděte na seznam varhan a použijte tlačítko Kategorie.');
                break;

            case 'organ-builders.organ-builder-custom-categories':
                $this->repository = new OrganBuilderRepository;
                $this->title = __('Vlastní kategorie varhanářů');
                $this->genericCategory = new OrganBuilderCustomCategory;
                $this->recordsRoute = 'organ-builders.organ-builder-custom-categories.organ-builders';
                $this->backRoute = 'organ-builders.index';
                $this->recordsCountProp = 'organ_builders_count';
                $this->recordEntityName = __('Varhanáři');
                $this->recordEntityIcon = 'person-circle';
                $this->showRecordsText = __('Zobrazit varhanáře v této kategorii');
                $this->shareModalHint = __('Sdílením kategorie sdílíte všechny varhanáře v ní obsažené.');
                $this->createCategoryMessage = __('Vytvořte si vlastní pojmenovanou kategorii (skupinu) varhanářů. Může obsahovat např. varhanáře působící ve vašem okolí. Tlačítkem Sdílet zašlete skupinu varhanářů svým známým.');
                $this->addItemsMessage = __('Pro přidání varhanářů do kategorie jděte na seznam varhanářů a použijte tlačítko Kategorie.');
                break;

            default:
                throw new \LogicException;
        }

        if (isset($this->id) && !isset($this->category)) {
            $this->category = $this->getCategory($this->id);
        }
    }

    public function rendering(View $view): void
    {
        $view->title($this->title);
    }

    public function rendered()
    {
        $this->dispatch('bootstrap-rendered');
    }
    
    private function getCategory($id)
    {
        return $this->genericCategory->findOrFail($id);
    }

    #[Computed]
    public function categories()
    {
        return $this->repository->getCustomCategories();
    }

    public function cancel()
    {
        $this->isEdit = false;
        $this->dispatch('bootstrap-rendered');
    }

    public function edit($id = null)
    {
        $this->id = $id;
        if ($id !== null) {
            $this->category = $this->getCategory($this->id);
            $this->name = $this->category->name;
            $this->description = $this->category->description;
        }
        else $this->name = $this->description = '';

        $this->resetValidation();
        $this->isEdit = true;
        $this->js('$(focusName)');

        $this->dispatch('bootstrap-rendered');
    }

    public function delete($id)
    {
        $this->genericCategory->findOrFail($id)->delete();
        session()->flash('status-success', __('Kategorie byla úspěšně smazána.'));
        $this->redirectRoute($this->route, navigate: true);
    }

    public function rules()
    {
        $uniqueRule = Rule::unique($this->genericCategory->getTable())->where('user_id', Auth::id());
        if (isset($this->category)) {
            $uniqueRule->ignoreModel($this->category);
        }
        return [
            'name' => ['required', $uniqueRule]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Název kategorie musí být vyplněn.',
            'name.unique' => 'Kategorie se zadaným názvem již existuje.',
        ];
    }

    public function save()
    {
        $this->validate();

        if (!isset($this->id)) {
            $category = $this->genericCategory->newInstance([
                'name' => $this->name,
                'description' => $this->description,
                'user_id' => Auth::id(),
            ]);
            $category->save();
            $savedId = $category->id;
        }
        else {
            $this->category->name = $this->name;
            $this->category->description = $this->description;
            $this->category->save();
            $savedId = $this->category->id;
        }
        session()->flash('organ-custom-categories.saved-id', $savedId);
        session()->flash('status-success', __('Kategorie byla úspěšně uložena.'));
        $this->redirectRoute($this->route, navigate: true);
    }

    private function getShareUrl($category)
    {
        $relativeUrl = URL::signedRoute($this->recordsRoute, ['id' => $category->id], absolute: false);
        return url($relativeUrl);
    }
    
}; ?>

<div class="organ-custom-categories container">
    <h3>{{ $this->title }}</h3>
    
    <p class="lead">
        {{ $this->createCategoryMessage }}
    </p>
    
    <form wire:submit="save">
        <table class="table table-sm table-hover align-middle" style="table-layout: fixed;">
            @foreach ($this->categories as $category)
                <tr @class(['table-secondary' => session('organ-custom-categories.saved-id') === $category->id])>
                    @if ($this->isEdit && $this->id === $category->id)
                        <x-organomania.organ-custom-categories-edit-row />
                    @else
                        <td>
                            <div class="row">
                                <div class="col-md-5">
                                    {{ $category->name }}
                                </div>
                                <div class="col-md-7 text-secondary">
                                    {{ $category->description }}
                                </div>
                            </div>
                        </td>
                        <td class="button-icon">
                            <button type="button" @class(['btn', 'btn-sm', 'btn-outline-primary', 'disabled' => $this->isEdit]) data-bs-toggle="tooltip" data-bs-title="{{ __('Upravit') }}" wire:click="edit({{ $category->id }})">
                                <i class="bi-pencil"></i>
                            </button>
                        </td>
                        <td class="button-icon">
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Smazat') }}">
                                <button @class(['btn', 'btn-sm', 'btn-danger', 'disabled' => $this->isEdit]) type="button" data-bs-toggle="modal" data-bs-target="#confirmModal" data-custom-category-id="{{ $category->id }}">
                                    <i class="bi-trash"></i>
                                </button>
                            </span>
                        </td>
                        @if ($category->{$this->recordsCountProp} > 0)
                            <td class="separator text-center">
                                <div class="vr"></div>
                            </td>
                            <td class="button button-organs">
                                <a
                                    class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $this->showRecordsText }}"
                                    href="{{ route($this->recordsRoute, [$category->id]) }}"
                                    wire:navigate
                                >
                                    <i class="bi-{{ $this->recordEntityIcon }}"></i>
                                    <span class="d-none d-md-inline">{{ $this->recordEntityName }}</span>
                                    <span class="badge text-bg-secondary rounded-pill">{{ $category->{$this->recordsCountProp} }}</span>
                                </a>
                            </td>
                            <td class="button-icon">
                                <span data-bs-toggle="tooltip" data-bs-title="Sdílet">
                                    <button type="button" class="btn btn-sm btn-outline-secondary z-1" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $this->getShareUrl($category) }}">
                                        <i class="bi-share"></i>
                                    </button>
                                </span>
                            </td>
                        @else
                            <td class="separator"></td>
                            <td class="button"></td>
                            <td class="button-icon"></td>
                        @endif
                    @endif
                </tr>
            @endforeach

            <tr>
                @if ($this->isEdit && !isset($this->id))
                    <x-organomania.organ-custom-categories-edit-row />
                @else
                    <td colspan="6">
                        <button type="button" @class(['btn', 'btn-sm', 'btn-primary', 'disabled' => $this->isEdit]) href="#" wire:click="edit"><i class="bi-plus-lg"></i> {{ __('Přidat kategorii') }}</button>
                    </td>
                @endif
            </tr>
        </table>
    </form>
    
    <small class="text-secondary">
        {{ $this->addItemsMessage }}
    </small>
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ route($this->backRoute) }}" wire:navigate>
            <i class="bi-arrow-return-left"></i> {{ __('Zpět') }}
        </a>
    </div>
    
    <x-organomania.modals.confirm-modal
        title="{{ __('Smazat') }}"
        buttonLabel="{{ __('Smazat') }}"
        buttonColor="danger"
        onclick="$wire.delete(confirmModal.getInvokeButton().dataset.customCategoryId)"
    >
        {{ __('Opravdu chcete kategorii smazat?') }}
    </x-organomania.modals.confirm-modal>
        
    <x-organomania.modals.share-modal hintAppend="{{ $this->shareModalHint }}" />
</div>

@script
<script>
    window.focusName = function () {
        $('#name').focus()
    }
</script>
@endscript
