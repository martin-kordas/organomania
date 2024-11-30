<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Route;
use App\Models\RegisterName;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public RegisterName $registerName;

    public function rendering(View $view): void
    {
        $title = $this->registerName->name;
        $title .= " - ";
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
            fn(RegisterName $registerName1) => $registerName1->id !== $this->registerName->id
        );
    }
    
}; ?>

<div class="register-show container">
    
    <h1 class="modal-title fs-5" id="registerModalLabel">
        {{ $registerName->name }}
        <span class="text-body-secondary">({{ $registerName->language }})</span>
    </h1>
    <div>
        @foreach ($this->registerNames as $registerName1)
            {{ $registerName1->name }}
            <span class="text-body-secondary">({{ $registerName1->language }})</span>
            @if (!$loop->last) <br /> @endif
        @endforeach
    </div>
  
    <hr>
    
    <x-organomania.register :$registerName :register="$this->register" :registerNames="$this->registerNames" dispositionsLimit="12" :categoriesAsLink="true" />
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>
    </div>
</div>
