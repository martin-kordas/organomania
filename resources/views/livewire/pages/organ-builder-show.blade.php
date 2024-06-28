<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\OrganBuilder;
use App\Models\OrganBuilderCategory;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    public OrganBuilder $organBuilder;
    
}; ?>

<div class="organ-builder-show container">
    <img class="float-end" src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" width="110" />
    <h3>{{ $organBuilder->name }}</h3>
    
    <table class="table">
        <tr>
            <th>{{ __('Kategorie') }}</th>
            <td>
                @foreach ($organBuilder->organBuilderCategories as $category)
                    <x-organomania.category-badge :category="$category->getEnum()" />
                @endforeach
            </td>
        </tr>
        <tr>
            <th>{{ __('Lokalita dílny') }}</th>
            <td>{{ $organBuilder->municipality }}</td>
        </tr>
        @if (isset($organBuilder->place_of_birth))
            <tr>
                <th>{{ __('Místo narození') }}</th>
                <td>{{ $organBuilder->place_of_birth }}</td>
            </tr>
        @endif
        @if (isset($organBuilder->place_of_death))
        <tr>
            <th>{{ __('Místo úmrtí') }}</th>
            <td>{{ $organBuilder->place_of_death }}</td>
        </tr>
        @endif
        <tr>
            <th>{{ __('Kraj') }}</th>
            <td>{{ $organBuilder->region->name }}</td>
        </tr>
        @if (isset($organBuilder->active_period))
        <tr>
            <th>{{ __('Období působení') }}</th>
            <td>{{ $organBuilder->active_period }}</td>
        </tr>
        @endif
        <tr>
            <th>{{ __('Význam') }}</th>
            <td>
                <x-organomania.stars :count="round($organBuilder->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @if (isset($organBuilder->description))
        <tr>
            <th>{{ __('Popis') }}</th>
            <td class="pre">{{ $organBuilder->description }}</td>
        </tr>
        @endif
    </table>
    
    <div class="text-end">
        <a class="btn btn-sm btn-secondary" href="{{ route('organ-builders.index') }}"><i class="bi-arrow-return-left"></i> Zpět</a>&nbsp;
        <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}"><i class="bi-pencil"></i> Upravit</a>
    </div>
</div>
