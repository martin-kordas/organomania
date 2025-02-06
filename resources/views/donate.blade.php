@use(App\Helpers)
@use(App\Models\Donation)

@php
    $donations = Donation::orderBy('date', 'desc')->get();
@endphp

<x-app-bootstrap-layout>
    <div class="about container">        
        <h3>{{ __('Podpořte web') }}</h3>
        
        <div class="text-center float-sm-end ms-3 mb-2 mb-sm-0">
            <img class="rounded" src="/images/programator.jpg" style="width: 280px" />
            <br />
            <em>{{ __('organomaniak při práci') }}</em>
        </div>

        <p>
            {{ __('Vážení příznivci Organomanie') }},
        </p>
        <p>
            {{ __('web, který si nyní prohlížíte, je ve vývoji již od června 2024.') }}
            {!! __('Kromě značného množství času stráveného <a href="https://github.com/martin-kordas/organomania" target="_blank">programováním</a> a tvorbou obsahu spotřebovává Organomania i finanční náklady, které jsou spojené s provozem serveru (momentálně asi 200 Kč / měsíc).') !!}
        </p>
        <p>
            {!! __('Přispět na další fungování webu můžete <strong>zasláním libovolné částky</strong> na účet uvedený níže.') !!}
            {!! __('Každému přispěvateli budou navíc v Organomanii zpřístupněny <a href="#ai" class="text-decoration-none">prémiové funkce</a> (jejich zapnutí si lze posléze vyžádat na <a href="mailto:info@organomania.cz" class="text-decoration-none">info@organomania.cz</a>).') !!}
        </p>
        
        <p>
            {{ __('Za veškerou podporu předem děkuji.') }}
            <br />
            <a class="text-decoration-none" href="/martin-kordas">{{ __('Autor') }}</a>
        </p>
        
        <div class="d-md-flex column-gap-5">
            <div>
                <h4>{{ __('Platební údaje') }}</h4>

                <p>
                    {{ __('Číslo účtu') }}: 8393310005/2250
                    <br />
                    ({{ __('banka') }}: Creditas)
                </p>
                <p>
                    IBAN: CZ56 2250 0000 0083 9331 0005
                    <br />
                    SWIFT BIC: CTASCZ22
                </p>

                <p>
                    {{ __('QR kód platby (předvolená částka 1000 Kč)') }}:
                    <br />
                    <img src="/images/donate.png" width="200" />
                </p>
            </div>

            @if ($donations->isNotEmpty())
                <div>
                    <h4>{{ __('Přijaté dary') }}</h4>

                    <div style="max-height: 22.9em; overflow-y: scroll">
                        <table class="table table-sm">
                            @foreach ($donations as $donation)
                                <tr>
                                    <td class="pe-3">{{ Helpers::formatDate($donation->date) }}</td>
                                    <td class="pe-3 text-end">{!! Helpers::formatCurrency($donation->amount) !!}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif
        </div>
        
        <h4 id="ai"><i class="bi bi-magic"></i> {{ __('Prémiové funkce') }}</h4>
        <p>
            {{ __('Všem, kdo přispějí na provoz Organomanie, budou zpřístupněny prémiové experimentální funkce, využívající umělou inteligenci (AI).') }}
            {{ __('Jedná se o následující funkce, které lze využít u každých (i soukromých) varhan v Organomanii') }}:
        </p>
        <ul>
            <li>
                <strong>{{ __('Popis dispozice') }}</strong> &ndash; {{ __('popíše ve větách charakter rejstříkové dispozice daného nástroje a navrhne pro něj vhodný typ varhanní literatury') }}
            </li>
            <li>
                <strong>{{ __('Registrace') }}</strong> &ndash; {{ __('naregistruje na varhanách zadanou skladbu (výsledné rejstříky přímo zvýrazní v dispozici)') }}
            </li>
        </ul>
        
        <div class="text-center my-3">
            <a href="/images/ai-funkce.png" target="_blank">
                <img class="rounded" src="/images/ai-funkce.png" style="width: 500px; max-width: 100%;" />
            </a>
        </div>
        
        <x-organomania.warning-alert class="mb-2 d-print-none">
            <strong>{{ __('UPOZORNĚNÍ') }}</strong>:
            {{ __('Prémiové funkce jsou experimentální a neposkytují vždy spolehlivé výsledky.') }}
            {{ __('Zatím je vhodné využívat je spíše pro zajímavost a spoléhat se primárně na své vlastní znalosti.') }}
        </x-organomania.warning-alert>
    </div>  
</x-app-bootstrap-layout>