<div class="modal fade import-disposition-from-text-modal" id="importDispositionFromTextModal" tabindex="-1" aria-labelledby="importDispositionFromTextModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importDispositionFromTextModalLabel">{{ __('Importovat dispozici textově') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="dispositionText">{{ __('Dispozice') }}</label>
                <textarea id="dispositionText" class="disposition-text form-control w-100" wire:model="dispositionText" rows="17" required></textarea>
                <div class="form-text mt-2">
                    {{ __('Požadovaný formát dispozice') }}:
                    <ul class="mb-0">
                        <li>{{ __('stopová výška a počet píšťalových řad výhradně ve tvaru např.: Mixtura 3-4x 1 1/3\'') }}</li>
                        <li>{{ __('manuály odděleny prázdným řádkem') }}</li>
                    </ul>
                    <a class="link-primary text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#dispositionTextExampleModal">
                        {{ __('Zobrazit příklad') }}
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                <button type="button" class="btn btn-primary" @click="importDispositionFromTextModal.import($wire)">{{ __('Importovat') }}</button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    window.importDispositionFromTextModal = {}

    window.importDispositionFromTextModal.import = function ($wire, modalId = 'importDispositionFromTextModal') {
        if ($(`#${modalId} .disposition-text`).val().trim() !== '') {
            $wire.importDispositionFromText()
            bootstrap.Modal.getOrCreateInstance(`#${modalId}`).hide();
        }
    }
    
    $('.import-disposition-from-text-modal').each(function () {
        this.addEventListener('shown.bs.modal', (e) => {
            $(this).find('.disposition-text').focus()
        })
    })
</script>
@endscript
