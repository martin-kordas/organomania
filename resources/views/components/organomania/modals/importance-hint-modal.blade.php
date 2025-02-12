@props(['title'])

<div class="modal fade" id="importanceHintModal" tabindex="-1" data-focus="false" aria-labelledby="importanceHintLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importanceHintLabel">{{ $title }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light mb-0">
                    <p>
                        <i class="bi bi-info-circle"></i>
                        {{ $slot }}
                    </p>
                    <p class="mb-0">
                        {{ __('Více viz strana') }} <a href="{{ route('about') }}" class="text-decoration-none" wire:navigate>{{ __('O webu') }}</a>.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>