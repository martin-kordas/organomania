@props(['title', 'onclick', 'buttonLabel', 'buttonColor' => 'primary', 'id' => 'confirmModal'])

<div class="modal fade confirm-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ "{$id}Label" }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="{{ "{$id}Label" }}">{{ $title }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
                <button type="button" class="btn btn-{{ $buttonColor }} delete-btn" @click="{{ $onclick }}" data-bs-dismiss="modal"><i class="bi-trash"></i> {{ $buttonLabel }}</button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    window.confirmModal = {}

    window.confirmModal.getInvokeButton = function (modalId = 'confirmModal') {
        return $(`#${modalId}`).data('invokeButton')
    }
    
    $(() => {
        $('.confirm-modal').each(function () {
            this.addEventListener('show.bs.modal', (e) => {
                // modalu předáme odkaz na tlačítko, které jej zobrazilo
                var invokeButton = e.relatedTarget
                $(this).data('invokeButton', invokeButton)
            })
            this.addEventListener('shown.bs.modal', (e) => {
                $(this).find('.delete-btn').focus();
            })
        })
    })
</script>
@endscript
