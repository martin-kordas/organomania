<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Organ;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    public Organ $organ;
    
}; ?>

<div class="organ-show container">
    <img class="float-end" src="{{ Vite::asset("resources/images/regions/{$organ->region_id}.png") }}" width="110" />
    <h3>{{ $organ->municipality }}, {{ $organ->place }}</h3>
    
    <table class="table">
        <tr>
            <th>{{ __('Kraj') }}</th>
            <td>{{ $organ->region->name }}</td>
        </tr>
        <tr>
            <th>{{ __('Varhanář') }}</th>
            <td>{{ $organ->organBuilder->name }}</td>
        </tr>
        <tr>
            <th>{{ __('Rok stavby') }}</th>
            <td>{{ $organ->year_built }}</td>
        </tr>
        <tr>
            <th>{{ __('Počet rejstříků') }}</th>
            <td>{{ $organ->stops_count }}</td>
        </tr>
        <tr>
            <th>{{ __('Počet manuálů') }}</th>
            <td>{{ $organ->manuals_count }}</td>
        </tr>
        <tr>
            <th>{{ __('Kategorie') }}</th>
            <td>
                @foreach ($organ->organCategories as $category)
                    <x-organomania.category-badge :category="$category->getEnum()" />
                @endforeach
            </td>
        </tr>
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="round($organ->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @if (isset($organ->description))
        <tr>
            <th>{{ __('Popis') }}</th>
            <td class="pre">{{ $organ->description }}</td>
        </tr>
        @endif
    </table>
    
    <div class="text-end">
        <a class="btn btn-sm btn-secondary" href="{{ route('organs.index') }}"><i class="bi-arrow-return-left"></i> Zpět</a>&nbsp;
        <a class="btn btn-sm btn-outline-primary disabled" href="#"><i class="bi-pencil"></i> Upravit</a>
    </div>
</div>
