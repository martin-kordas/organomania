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
                    {{ __('Funkce umožňují charakterizovat varhanní dispozici a naregistrovat zadanou varhanní skladbu.') }}
                    {{ __('Zatím fungují v experimentálním režimu.') }}
                </p>
                <img class="rounded" src="/images/ai-funkce.png" style="width: 500px; max-width: 100%;" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>