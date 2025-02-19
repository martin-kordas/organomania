<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use App\Enums\RegisterCategory;
use App\Models\RegisterName;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public RegisterName $registerName;

    public function rendering(View $view): void
    {
        $title = $this->registerName->name;
        $title .= " – ";
        $title .= __('varhanní rejstřík');
        $view->title($title);
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome')) {
            return route('dispositions.registers.index');
        }
        return $previousUrl;
    }

    #[Computed]
    private function register()
    {
        return $this->registerName->register;
    }

    #[Computed]
    private function registerNames()
    {
        return $this->register->registerNames->filter(
            fn(RegisterName $registerName1) =>
                $registerName1->id !== $this->registerName->id
                && !$registerName1->isVisuallySameAs($this->registerName)
        )->unique(
            fn (RegisterName $registerName1) => $registerName1->getVisualIdentifier()
        );
    }

    #[Computed]
    private function registerCategoriesGroups()
    {
        return RegisterCategory::getCategoryGroups();
    }
    
}; ?>

<div class="register-show container">
    
    <h1 class="fs-3">
        <a class="link-primary text-decoration-none" href="{{ route('dispositions.registers.index') }}" wire:navigate>
            {{ __('Encyklopedie rejstříků') }}
        </a>
    </h1>
    
    <h2 class="modal-title fs-5" id="registerModalLabel" @if (Auth::user()?->admin) title="registers.id: {{ $registerName->register_id }} {{ "\n" }}register_names.id: {{ $registerName->id }}" @endif>
        {{ $registerName->name }}
        @if (!$registerName->hide_language)
            <span class="text-body-secondary">({{ $registerName->language }})</span>
        @endif
    </h2>
    <div @style(['columns: 2' => $this->registerNames->count() > 3])>
        @foreach ($this->registerNames as $registerName1)
            {{ $registerName1->name }}
            @if (!$registerName1->hide_language)
                <span class="text-body-secondary">({{ $registerName1->language }})</span>
            @endif
            @if (!$loop->last) <br /> @endif
        @endforeach
    </div>
  
    <hr>
    
    <x-organomania.register :$registerName :register="$this->register" :registerNames="$this->registerNames" dispositionsLimit="12" :categoriesAsLink="true" />
      
    <x-organomania.modals.categories-modal :categoriesGroups="$this->registerCategoriesGroups" :categoryClass="RegisterCategory::class" />
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
    </div>
</div>
