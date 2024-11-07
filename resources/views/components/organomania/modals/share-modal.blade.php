@props(['hintAppend' => null])

<div>
    <div class="share-modal modal fade" id="shareModal" tabindex="-1" data-focus="false" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="shareModalLabel">{{ __('Sdílet odkaz') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <input class="link form-control" type="text" readonly>
                        <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-title="{{ __('Kopírovat odkaz do schránky') }}" onclick="shareModal.copyLinkToClipboard()">
                            <i class="copy-icon bi-clipboard"></i> {{ __('Kopírovat') }}
                        </button>
                        <a class="btn btn-outline-primary open-link" data-bs-toggle="tooltip" data-bs-title="{{ __('Přejít na odkaz') }}" href="#" target="_blank">
                            <i class="bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                    <div class="form-text">
                        {{ __('Na odkaz může přistoupit i nepřihlášený uživatel.') }}
                        @isset($hintAppend) {{ $hintAppend }} @endisset
                    </div>
                    <div class="text-center mt-3">
                        <img width="200" height="200" class="qr" src="" alt="{{ __('QR kód') }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                </div>
            </div>
        </div>
    </div>

    <x-organomania.toast toastId="copiedToast">
        {{ __('Odkaz byl úspěšně zkopírován.') }}
    </x-organomania.toast>
</div>

@script
<script>
    window.shareModal = {}
    
    window.shareModal.copyLinkToClipboard = async function () {
        var modal = $('#shareModal')
        var link = modal.find('input.link').val()
        
        await copyToClipboard(link)
        modal
            .find('.copy-icon')
            .removeClass('bi-clipboard')
            .addClass('bi-clipboard-check')
        modal
            .find('.copied-alert')
            .removeClass('d-none')

        var toast = $('#copiedToast')[0]
        var bootstrapToast = bootstrap.Toast.getOrCreateInstance(toast)
        bootstrapToast.show()
    }
        
    //document.addEventListener('livewire:navigated', () => {
    $wire.on('bootstrap-rendered', function () {
        setTimeout(() => {      // setTimeout nutný, protože entity-page-view je lazy
            $('.share-modal').each(function () {
                this.addEventListener('show.bs.modal', (e) => {
                    // na základě tlačítka, které vyvolalo zobrazení dialogu, určíme URL, která se má v dialogu zobrazit
                    var shareBtn = e.relatedTarget
                    var url = shareBtn.dataset.shareUrl
                    var modal = $('#shareModal')
                    modal.find('input.link').val(url)
                    modal.find('img.qr').attr('src', `/qr?string=${encodeURIComponent(url)}`)
                    modal.find('a.open-link').attr('href', url)
                })
                this.addEventListener('shown.bs.modal', () => $(this).find('input.link').select())
                this.addEventListener('hidden.bs.modal', () => {
                    $('#shareModal')
                        .find('.copy-icon')
                        .removeClass('bi-clipboard-check')
                        .addClass('bi-clipboard')
                })
            })
        })
    })
</script>
@endscript
