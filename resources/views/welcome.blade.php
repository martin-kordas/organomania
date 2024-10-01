@php
    use Carbon\Carbon;
@endphp

<x-app-bootstrap-layout>
    @php
        $runtimeStats = $component->runtimeStats;
        $lastUpdate = $runtimeStats->getLastUpdate();
    @endphp

    <div class="welcome">
        <div class="welcome-text p-3 pb-md-4 mx-auto text-center">
            <h1 class="display-5 fw-normal text-body-emphasis">{{ __('Vítejte ve světě varhan!') }}</h1>
            <p class="fs-5 text-body-secondary">
                {!! __('Organomania vám atraktivním způsobem představí varhany v&nbsp;České&nbsp;republice, a&nbsp;to jako součást kulturního dědictví, jako technickou památku, i&nbsp;jako živý hudební nástroj.') !!}
            </p>
        </div>
        
        <div class="row text-center g-4 align-items-stretch">
            <x-organomania.welcome-card
                title="{{ __('Varhany') }}"
                url="{{ route('organs.index') }}"
                imageUrl="https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Kostel_svateho_Morice_varhany_%28retouched%29.jpg/640px-Kostel_svateho_Morice_varhany_%28retouched%29.jpg"
            >
                {!! __('Prozkoumejte nejvýznamnější a&nbsp;nejvzácnější nástroje u&nbsp;nás.') !!}
                {!! __('Zjistěte více o&nbsp;jejich historii a&nbsp;zvukové povaze.') !!}
                <x-slot:list>
                    <li>{{ __('medailony') }} <span class="fs-5">{{ $runtimeStats->getOrganCount() }}</span> {{ __('vybraných varhan') }}</li>
                    <li>{{ __('možnost přidat vlastní') }} <span class="text-secondary">({{ __('pro přihlášené') }})</span></li>
                    <li>{{ __('přehledné zobrazení na mapě') }}</li>
                </x-slot:list>
                <x-slot:footer>
                    <p class="mt-2 mb-0 position-relative z-5">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('dispositions.index') }}" wire:navigate @click.stop>
                            <i class="bi-card-list"></i> {{ __('Dispozice varhan') }} »
                        </a>
                    </p>
                    <p class="mt-1 position-relative z-5">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('dispositions.registers.index') }}" wire:navigate @click.stop>
                            <i class="bi-globe"></i> {{ __('Encyklopedie rejstříků') }} »
                        </a>
                    </p>
                </x-slot:footer>
            </x-organomania.welcome-card>
            
            <x-organomania.welcome-card
                title="{{ __('Varhanáři') }}"
                url="{{ route('organ-builders.index') }}"
                imageUrl="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bundesarchiv_B_145_Bild-F023327-0012%2C_Bonn%2C_Orgelbau_in_der_Werkstatt_Klais.jpg/494px-Bundesarchiv_B_145_Bild-F023327-0012%2C_Bonn%2C_Orgelbau_in_der_Werkstatt_Klais.jpg"
            >
                {!! __('Seznamte se se staviteli varhan, ať už jde o&nbsp;starobylé varhanářské rody, nebo o&nbsp;novodobé varhanářské dílny a&nbsp;továrny.') !!}
                <x-slot:list>
                    <li>{{ __('medailony') }} <span class="fs-5">{{ $runtimeStats->getOrganBuilderCount() }}</span> {{ __('vybraných varhanářů') }}</li>
                    <li>{{ __('možnost přidat vlastní') }} <span class="text-secondary">({{ __('pro přihlášené') }})</span></li>
                    <li>{{ __('přehledné zobrazení na mapě') }}</li>
                </x-slot:list>
            </x-organomania.welcome-card>
            
            <x-organomania.welcome-card
                title="{{ __('Festivaly') }}"
                url="{{ route('festivals.index') }}"
                imageUrl="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Hammond_C2_manuals_-_waterfall_style_keyboard_%28flat-front_profile%29_%28Supernatural%29.jpg/640px-Hammond_C2_manuals_-_waterfall_style_keyboard_%28flat-front_profile%29_%28Supernatural%29.jpg"
            >
                {{ __('Objevte prestižní hudební festivaly zaměřené na koncerty varhanní hudby.') }}
            </x-organomania.welcome-card>
        </div>
      
        <div class="py-3 px-1 mt-4 pb-md-4 mx-auto text-center">
            <p class="fs-5 text-body-secondary">
                {{ __('Stránky jsou určeny pro aktivní varhaníky,') }}
                <br />
                {{ __('studenty hudebních škol i pro širokou veřejnost.') }}
            </p>
            <p class="text-body-tertiary">
                {{ __('Připomínky a podněty k obsahu webu můžete zasílat na e-mail') }}
                <br />
                <a href="mailto:{{ config('custom.app_admin_email') }}">{{ config('custom.app_admin_email') }}</a>
            </p>
            <p class="text-body-tertiary">
                {{ __('Poslední aktualizace') }}: {{ Carbon::instance($lastUpdate)->isoFormat('LLL') }}
            </p>
        </div>
    </div>
</x-app-bootstrap-layout>