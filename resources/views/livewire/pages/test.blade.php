<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// PŘÍKLAD JEDNODUCHÉ LIVEWIRE KOMPONENTY
//  - nahoře jsou PHP vlastnosti a metody
//    dole je HTML markup
//  - metody pracují s vlastnostmi a automaticky mění markup, HTTP požadavky a odpovědi po každé interakci probíhají skrytě na pozadí
new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    // $count je vlastnost, kterou zobrazujeme v HTML
    public $count = 1;

    public $isEdit = false;
    public $firstName = 'Jan';
    public $lastName = 'Novák';

    // funkce plus() a minus() se volají z HTML, v tomto případě atributem wire:click
    public function plus()
    {
        $this->count += 1;
    }

    public function minus()
    {
        $this->count -= 1;
    }

    public function open()
    {
        $this->isEdit = true;
    }

    public function close()
    {
        $this->isEdit = false;
    }

    // triviální práci s daty lze nahradit za jakýkoli jiný kód, čtení a ukládání z db. atd.

}; ?>

<div class="organs container">
    
    <h4>{{ $count }}</h4>
    
    <button class="btn btn-primary" wire:click="plus">Plus</button>
    <button class="btn btn-primary" wire:click="minus">Minus</button>
    
    <br />
    <br />
    
    <div>
        @if ($this->isEdit)
            <input wire:model="firstName" />
            <input wire:model="lastName" />
            <a class="btn btn-primary" wire:click="close">Zavřít</a>
        @else
            <h4>{{ $this->firstName }} {{ $this->lastName }}</h4>
            <a class="btn btn-primary" wire:click="open">Upravit</a>
        @endif
    </div>
    
    
</div>
