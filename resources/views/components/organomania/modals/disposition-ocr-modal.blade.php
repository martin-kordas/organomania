@php
    $dispositionOcrResult = trim($this->dispositionOcrResult ?? '');
@endphp

<form wire:submit="doDispositionOcr">
    <div wire:ignore.self class="modal fade disposition-ocr-modal" id="dispositionOcrModal" tabindex="-1" data-focus="false" aria-labelledby="dispositionOcrLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="dispositionOcrLabel">
                        <i class="bi bi-magic"></i> {{ __('Přečíst dispozici z fotografie') }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
                </div>

                <div class="modal-body">
                    @php $isFileError = $errors->has('dispositionOcrForm.photos') || $errors->has('dispositionOcrForm.photos.*'); @endphp
                    @if (!empty($this->dispositionOcrForm->photos) && $this->uploadedOcrPhotos->isNotEmpty() && !$isFileError)
                        <x-organomania.gallery-carousel :images="$this->uploadedOcrPhotos" class="mb-2" :noAdditional="true" />
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-secondary" type="button" wire:click="resetDispositionOcr">
                                <i class="bi bi-x-lg"></i> {{ __('Nahrát jiné fotografie') }}
                            </button>
                        </div>
                    @else
                        <input id="ocrPhotos" class="form-control @if ($isFileError) is-invalid @endif" type="file" wire:model="dispositionOcrForm.photos" aria-describedby="ocrPhotosFeedback" multiple>
                        @if ($isFileError)
                            @error('dispositionOcrForm.photos')
                                <div id="ocrPhotosFeedback" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('dispositionOcrForm.photos.*')
                                <div id="ocrPhotosFeedback" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                        <div class="form-text">
                            {{ __('Můžete nahrát 1 až 5 fotografií.') }}
                            {{ __('Mělo by jít o dobře čitelné fotografie detailů rejstříkových sklopek nebo táhel.') }}
                        </div>
                    @endif

                    @isset($this->dispositionOcrResult)
                        @if ($dispositionOcrResult === '')
                            <div class="mt-3 text-danger">{{ __('Dispozici se nepodařilo z fotografie přečíst.') }}</div>
                        @else
                            <div class="mt-3">
                                <h6>{{ __('Přečtená dispozice') }}</h6>
                                <textarea id="dispositionOcrResult" class="form-control" rows="10" wire:model="dispositionOcrResult"></textarea>
                            </div>
                        @endif
                    @endisset
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="resetDispositionOcr">{{ __('Zavřít') }}</button>

                    @if ($dispositionOcrResult !== '')
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" wire:click="resetDispositionOcr" @click="appendOcrResultToDisposition($wire)">
                            <i class="bi bi-clipboard"></i> {{ __('Vložit do dispozice') }}
                        </button>
                    @else
                        <button id="filterButton" type="submit" class="btn btn-primary" @disabled(empty($this->dispositionOcrForm->photos || $isFileError))>
                            <span wire:loading.remove wire:target="doDispositionOcr">
                                <i class="bi-magic"></i>
                            </span>
                            <span wire:loading wire:target="doDispositionOcr">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                <span class="visually-hidden" role="status">{{ __('Načítání...') }}</span>
                            </span>
                            {{ __('Přečíst dispozici') }}
                        </button>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</form>

@script
<script>
    window.appendOcrResultToDisposition = function ($wire) {
        let dispositionValue = $('#disposition').val()
        if (dispositionValue.trim() !== '') dispositionValue += "\n"
        dispositionValue += $('#dispositionOcrResult').val()
        $wire.form.disposition = dispositionValue
    }
</script>
@endscript
