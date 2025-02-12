@use(App\Enums\RegisterCategory)
@use(App\Enums\OrganCategory)
@use(App\Enums\OrganBuilderCategory)

<div class="suggest-registration-modal modal fade" id="suggestRegistrationModal" tabindex="-1" data-focus="false" aria-labelledby="suggestRegistrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" onsubmit="return suggestRegistrationModal.suggestRegistration()">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="suggestRegistrationModalLabel">
                     <i class="bi bi-magic"></i> {{ __('Naregistrovat pomocí AI') }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                <x-organomania.warning-alert class="mb-2 d-print-none">
                    {{ __('Buďte obezřetní – umělá inteligence poskytuje nepřesné výsledky.') }}
                </x-organomania.warning-alert>
                
                <div class="mb-3">
                    <label for="piece" class="form-label">{{ __('Skladba (část skladby, typ skladby)') }}</label>
                    <input id="piece" type="text" class="piece form-control" id="piece" placeholder="{{ __('např.') }} {{ __('J. S. Bach: Toccata a fuga d-moll, BWV 565') }}">
                    <div class="form-text">
                        {{ __('Obvyklé typy skladeb') }}:
                        <ul>
                            @foreach (['doprovod kostelní písně', 'slavnostní předehra', 'meditativní mezihra'] as $pieceType)
                                <li>
                                    <a class="text-decoration-none" href="#" onclick="return suggestRegistrationModal.pieceTypeOnclick(this)">{{ __($pieceType) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
              
                <div class="form-text">
                    {{ __('Doporučení') }}:
                    <ul class="mb-0">
                        <li>{{ __('Registrování je individuální umělecká činnost, kterou varhaník provádí na základě kontaktu s nástrojem.') }}</li>
                        <li style="word-break: keep-all">{{ __('Umělou inteligenci proto používejte jen pro inspiraci nebo potřebujete-li nutně poradit.') }}</li>
                        <li>{{ __('Naregistrování opakujte, dokud umělá inteligence neposkytne optimální výsledek.') }}</li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-magic"></i> {{ __('Naregistrovat') }}</button>
            </div>
        </form>
    </div>
</div>

@script
<script>
    window.suggestRegistrationModal = {}
    
    window.suggestRegistrationModal.suggestRegistration = function (modalId = 'suggestRegistrationModal') {
        let piece = $(`#${modalId} .piece`).val().trim()
        if (piece !== '') {
            $wire.suggestRegistration(piece)
            bootstrap.Modal.getOrCreateInstance(`#${modalId}`).hide()
        }
        return false
    }
        
    window.suggestRegistrationModal.pieceTypeOnclick = function (elem) {
        $('#piece').val(elem.textContent).focus()
        return false
    }
    
    $('.suggest-registration-modal').each(function () {
        this.addEventListener('shown.bs.modal', (e) => {
            $(this).find('.piece').focus()
        })
    })
</script>
@endscript
