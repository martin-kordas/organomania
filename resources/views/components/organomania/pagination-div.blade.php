<div class="pagination-div d-flex d-md-block">
    {{ $this->organs->links() }}

    @if ($this->hasSortRandom && $this->sortColumn !== 'random')
        <div class="ms-auto d-md-none">
            <a class="btn btn-outline-primary border border-secondary-subtle" wire:click="$parent.sort('random')">
                {{ __('Řadit náhodně') }}
            </a>
        </div>
    @endif
</div>