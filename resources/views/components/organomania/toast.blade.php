@props(['toastId', 'color' => null])

@teleport('body')
    <div class="toast-container position-fixed bottom-0 w-100 p-3" wire:ignore>
        <div class="toast align-items-center z-10 mx-auto @isset($color) text-bg-{{ $color }} @endisset" id="{{ $toastId }}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ $slot }}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ __('Zavřít') }}"></button>
            </div>
        </div>
    </div>
@endteleport
