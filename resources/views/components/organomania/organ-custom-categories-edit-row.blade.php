<td class="inputs">
    <div class="row g-3">
        <div class="col-md-5">
            <div class="form-floating">
                <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" wire:model.live="name" placeholder="{{ __('Název kategorie') }}" wire:keydown.escape="cancel" aria-describedby="workshopNameFeedback" />
                <label for="name">Název</label>
                @error('name')
                    <div id="nameFeedback" class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-floating">
                <input id="description" class="form-control" type="text" wire:model="description" placeholder="{{ __('Popis kategorie') }}" wire:keydown.escape="cancel" />
                <label for="description">Popis</label>
            </div>
        </div>
    </div>
</td>
<td class="button-icon">
    <button type="submit" class="btn btn-sm btn-primary" type="submit" data-bs-toggle="tooltip" data-bs-title="{{ __('Uložit') }}">
        <i class="bi-floppy"></i>
    </button>
</td>
<td class="button-icon">
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="{{ __('Neukládat') }}" wire:click="cancel">
        <i class="bi-x"></i>
    </button>
</td>
<td colspan="3"></td>
