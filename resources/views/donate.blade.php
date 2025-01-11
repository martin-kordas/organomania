<x-app-bootstrap-layout>
    <div class="about container">
        <h3>{{ __('Podpořte web') }}</h3>

        <p>
            {{ __('Kromě množství času stráveného programováním a tvorbou obsahu spotřebovává web Organomania i finanční náklady, které jsou spojené s provozem serveru (momentálně asi 350 Kč / měsíc).') }}
        </p>
        <p>
            {{ __('Přispět na další fungování webu můžete zasláním libovolné částky na účet vedený u banky Creditas.') }}
        </p>
        <p>
            {{ __('Číslo účtu') }}: 8393310005/2250
        </p>
        <p>
            IBAN: CZ56 2250 0000 0083 9331 0005
            <br />
            SWIFT BIC: CTASCZ22
        </p>
        
        <p>
            {{ __('QR kód platby (předvolená částka 350 Kč)') }}:
            <br />
            <img src="/images/donate.png" width="220" />
        </p>
        
        <p>
            {{ __('Předem děkuji.') }}
            <br />
            {{ __('Autor') }}
        </p>
    </div>
</x-app-bootstrap-layout>