<div class="container align-center ps-0">
    @if ($this->mapTooManyItems)
        <div class="alert alert-secondary text-center" role="alert">
            {!! __('Mapu není možné zobrazit, protože obsahuje <strong>příliš mnoho položek</strong>.') !!}
            <br class="d-none d-md-inline" />
            {{ __('Pro snížení počtu položek prosím') }}
            <a href="#" class="fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#filtersModal">{{ __('použijte libovolný filtr') }}</a>.
        </div>
    @elseif ($this->organs->isEmpty())
        <div class="alert alert-secondary text-center" role="alert">
            {{ $this->noResultsMessage }}
        </div>
    @else
        @if ($this->shouldPaginate && $this->viewType === 'thumbnails')
            {{ $this->organs->links() }}
        @endif
    
        <div @class(['entity-page-view-container', "view-type-{$this->viewType}"])>
            <x-dynamic-component :component="$this->viewComponent" :organs="$this->organs" :thumbnailOrgan="$this->thumbnailOrgan" />
        </div>
            
        @if ($this->shouldPaginate)
            {{ $this->organs->links() }}
        @endif
          
        <x-organomania.modals.share-modal :hintAppend="$this->shareModalHint" />

        @if ($this->isCategorizable)
            <x-organomania.modals.organ-custom-categories-modal :customCategories="$this->organCustomCategories" />
        @endif
            
        <x-organomania.toast toastId="likedToast">
            {{ $this->likedMessage }}
        </x-organomania.toast>
        <x-organomania.toast toastId="unlikedToast">
            {{ $this->unlikedMessage }}
        </x-organomania.toast>
    @endif
</div>

@script
<script>
    $(() => {
        // nutný refresh, protože komponenta je lazy
        refreshSelect2Sync($wire)
    })
</script>
@endscript
