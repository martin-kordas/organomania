@props(['title'])

@teleport('body')
    <div class="position-fixed bottom-0 w-100 p-3" style="z-index: 11">
        <div id="suggestRegistrationInfo" class="toast show position m-auto bg-white" data-bs-autohide="false" aria-live="assertive" aria-atomic="true" style="width: 500px;">
            <div class="toast-header">
                <strong class="me-auto">{{ $title }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            {{-- bez wire:replace by .info zůstalo odrolované i při dalším načtení infa --}}
            <div class="toast-body" wire:replace>
                <div class="info overflow-y-scroll" style="max-height: 17em;">{{ $slot }}</div>
                <div class="mt-2 pt-2 border-top text-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">{{ __('Zavřít') }}</button>
                </div>
            </div>
        </div>
    </div>
@endteleport
