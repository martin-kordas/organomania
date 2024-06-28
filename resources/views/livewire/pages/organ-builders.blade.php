<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use App\Models\OrganBuilder;
use Livewire\WithPagination;

new #[Layout('layouts.app-bootstrap')] class extends Component {
    
    use WithPagination;

    #[Computed]
    public function organBuilders()
    {
        return OrganBuilder::with('region')->orderBy('active_from_year')->paginate(6);
    }

    public function updatedPage()
    {
        $this->dispatch("bootstrap-rendered");
    }

}; ?>

<div class="organs container">
    <div class="buttons float-end z-2 position-relative">
        <div class="position-fixed ms-4">
            <div class="position-absolute text-center">
                <a type="button" class="btn btn-sm btn-primary mb-3" href="{{ route('organ-builders.create') }}"><i class="bi-plus-lg"></i> Přidat</a>
            </div>
        </div>
    </div>
    
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>{{ __('Varhanář') }}/{{ __('dílna') }}</th>
                <th>{{ __('Lokalita') }}</th>
                <th>{{ __('Kraj') }}</th>
                <th>{{ __('Období') }} <i class="bi-sort-up"></i></th>
                <th>{{ __('Kategorie') }}</th>
                <th>{{ __('Význam') }}</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            @foreach ($this->organBuilders as $organBuilder)
                <tr>
                    <td>
                        @if ($organBuilder->user_id)
                            <span data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}">
                                <i class="bi-file-lock text-warning"></i>
                            </span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $organBuilder->name }}</td>
                    <td>{{ $organBuilder->municipality }}</td>
                    <td>{{ $organBuilder->region->name }}</td>
                    <td>{{ $organBuilder->active_period }}</td>
                    <td>
                        @foreach ($organBuilder->getGeneralCategories() as $category)
                            <x-organomania.category-badge :category="$category->getEnum()" />
                        @endforeach
                    </td>
                    <td>
                        <x-organomania.stars :count="round($organBuilder->importance / 2)" :showCount="true" />
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-primary" href="{{ route('organ-builders.show', ['organBuilder' => $organBuilder->id]) }}"><i class="bi-eye"></i> Zobrazit</a>
                        &nbsp;
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}"><i class="bi-pencil"></i> Upravit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $this->organBuilders->links() }}
</div>
