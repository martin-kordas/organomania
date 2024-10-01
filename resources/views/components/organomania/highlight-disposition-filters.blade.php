<div class="highlight-disposition-filters col-12 lh-lg d-print-none mb-1">
    <label class="label">{{ __('Zvýraznit rejstříky') }}</label>
    <input type="radio" class="btn-check" name="highlightFilterIndex" wire:model.change="highlightFilterIndex" value="-1" id="highlightFilterIndexRemove">
    <label class="btn btn-sm btn-outline-secondary" for="highlightFilterIndexRemove">
        <i class="bi-x-circle"></i> {{ __('Nezvýrazňovat') }}
    </label>
    &nbsp;
    @foreach ($this->highlightDispositionFilters as $i => $filter)
        <input
            type="radio"
            class="btn-check"
            name="highlightFilterIndex"
            wire:model.change="highlightFilterIndex"
            value="{{ $i }}"
            id="highlightFilterIndex{{ $i }}"
        >
        <label
            class="btn btn-sm btn-outline-secondary"
            for="highlightFilterIndex{{ $i }}"
            @isset($filter->description)
                data-bs-toggle="tooltip"
                data-bs-title="{{ $filter->description }}"
            @endisset
        >
            {{ $filter->name }}
        </label>
    @endforeach
</div>
