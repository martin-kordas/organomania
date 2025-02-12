@php
    $images = [
        ['/images/ai-funkce2.png', null],
        ['/images/ai-funkce.png', null],
    ];
@endphp

<div class="premium-modal modal fade" id="premiumModal" tabindex="-1" data-focus="false" aria-labelledby="premiumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="premiumModalLabel">{{ __('Podpořte web') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>
            <div class="modal-body">
                {{ __('Funkce umělé inteligence jsou dostupné jako bonus pro uživatele, kteří podpořili provoz webu Organomania libovolným finančním příspěvkem.') }}
                <p class="text-center my-3">
                    <a class="btn btn-primary" href="{{ route('donate') }}" target="_blank">{{ __('Podpořit web') }}</a>
                </p>
                <p class="small text-body-secondary">
                    {{ __('Funkce umožňují slovně charakterizovat varhanní dispozici, naregistrovat zadanou varhanní skladbu a přečíst při editaci varhan dispozici z fotografií hracího stolu.') }}
                    {{ __('Funkce zatím fungují v experimentálním režimu.') }}
                </p>
                <x-organomania.gallery-carousel :images="$images" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>