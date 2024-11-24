@props(['organOfDay'])

@php
    use App\Helpers;
    use Carbon\Carbon;
@endphp

<x-app-bootstrap-layout>
    @php
        $runtimeStats = $component->runtimeStats;
        $lastUpdate = $runtimeStats->getLastUpdate();
        $description = __('Organomania vám atraktivním způsobem představí varhany v&nbsp;České&nbsp;republice, a&nbsp;to jako součást kulturního dědictví, jako technickou památku, i&nbsp;jako živý hudební nástroj.');
    @endphp
  
    @push('meta')
        <meta name="description" content="{{ html_entity_decode($description) }}">
    @endpush

    <div class="welcome">
        <div class="welcome-text p-3 pb-md-4 mx-auto text-center">
            <h1 class="display-5 fw-normal text-body-emphasis">{{ __('Vítejte ve světě varhan!') }}</h1>
            <p class="fs-5 text-body-secondary">
                {!! $description !!}
            </p>
        </div>
        
        @isset($organOfDay)
            <div class="row justify-content-center mb-5">
                <div
                    class="organ-of-day col col-lg-9 text-center"
                    href="{{ route('organs.show', $organOfDay->slug) }}"
                    wire:navigate
                    style="cursor: pointer;"
                >
                    <div class="border border-tertiary rounded p-2">
                        <h2 class="fs-5">{{ __('Varhany dne') }}</h2>
                        <figure class="mb-0">
                            <div class="position-relative mb-1">
                                <img class="organ-of-day-image rounded" src="{{ $organOfDay->image_url }}" @isset($organOfDay->image_credits) title="{{ __('Licence obrázku') }}: {{ $organOfDay->image_credits }}" @endisset />
                                <img width="125" class="region end-0 m-2 bottom-0 position-absolute" src="{{ Vite::asset("resources/images/regions/{$organOfDay->region_id}.png") }}" />
                            </div>
                            <figcaption>
                                <strong>{{ $organOfDay->municipality }}</strong> &nbsp;|&nbsp; {{ $organOfDay->place }}
                                <br />
                                <x-organomania.organ-builder-link :organBuilder="$organOfDay->organBuilder" :yearBuilt="$organOfDay->year_built" />
                                &nbsp;|&nbsp;
                                <span class="text-body-secondary">
                                    {{ $organOfDay->manuals_count }} {{ __(Helpers::declineCount($organOfDay->manuals_count, 'manuálů', 'manuál', 'manuály')) }}
                                    @if ($organOfDay->stops_count)
                                        / {{ $organOfDay->stops_count }} {{ __(Helpers::declineCount($organOfDay->stops_count, 'rejstříků', 'rejstřík', 'rejstříky')) }}
                                    @endif
                                </span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        @endisset
        
        <div class="row text-center g-4 align-items-stretch">
            <x-organomania.welcome-card
                title="{{ __('Varhany') }}"
                url="{{ route('organs.index') }}"
                imageUrl="https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Kostel_svateho_Morice_varhany_%28retouched%29.jpg/640px-Kostel_svateho_Morice_varhany_%28retouched%29.jpg"
                imageCredit="Michal Maňas, Public domain, via Wikimedia Commons"
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
                imageCredit="Bundesarchiv, B 145 Bild-F023327-0012 / Gathmann, Jens / CC-BY-SA 3.0, CC BY-SA 3.0 DE, via Wikimedia Commons"
            >
                {!! __('Seznamte se se staviteli varhan, ať už jde o&nbsp;starobylé varhanářské rody, nebo o&nbsp;novodobé varhanářské dílny a&nbsp;továrny.') !!}
                <x-slot:list>
                    <li>{{ __('medailony') }} <span class="fs-5">{{ $runtimeStats->getOrganBuilderCount() }}</span> {{ __('vybraných varhanářů') }}</li>
                    <li>{{ __('možnost přidat vlastní') }} <span class="text-secondary">({{ __('pro přihlášené') }})</span></li>
                    <li>{{ __('přehledné zobrazení na mapě') }}</li>
                </x-slot:list>
            </x-organomania.welcome-card>
            
            <x-organomania.welcome-card
                title="{{ __('Festivaly a soutěže') }}"
                url="{{ route('festivals.index') }}"
                buttonLabel="Zobrazit festivaly"
                imageUrl="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Hammond_C2_manuals_-_waterfall_style_keyboard_%28flat-front_profile%29_%28Supernatural%29.jpg/640px-Hammond_C2_manuals_-_waterfall_style_keyboard_%28flat-front_profile%29_%28Supernatural%29.jpg"
                imageCredit="eyeliam, CC BY 2.0, via Wikimedia Commons"
            >
                {{ __('Objevte prestižní hudební festivaly zaměřené na koncerty varhanní hudby a připravované interpretační soutěže.') }}
                <x-slot:footer>
                    <p class="mt-2">
                        <a class="btn btn-secondary" href="{{ route('competitions.index') }}">{{ __('Zobrazit soutěže') }} »</a>
                    </p>
                </x-slot:footer>
            </x-organomania.welcome-card>
        </div>
      
        <div class="py-3 px-1 mt-4 pb-2 mx-auto text-center">
            <p class="fs-5 text-body-secondary">
                {{ __('Stránky jsou určeny pro aktivní varhaníky,') }}
                <br />
                {{ __('studenty hudebních škol i pro širokou veřejnost.') }}
            </p>
            <p class="text-body-tertiary">
                {{ __('Záměrem je poskytnout rámcový přehled, ne kompletní rejstřík varhan.') }}
                <br class="d-none d-md-inline" />
                {{ __('Připomínky a podněty k obsahu zasílejte na') }}
                <a href="mailto:{{ config('custom.app_admin_email') }}">{{ config('custom.app_admin_email') }}</a>.
            </p>
            <p class="text-body-tertiary">
                {{ __('Poslední aktualizace') }}: {{ Carbon::instance($lastUpdate)->isoFormat('LLL') }}
            </p>
        </div>
    </div>
</x-app-bootstrap-layout>