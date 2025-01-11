@props(['organOfDay'])

@php
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
        <div class="welcome-text p-3 pb-4 pt-0 pt-md-3 pb-md-5 mx-auto text-center">
            <h1 class="display-5 fw-normal text-body-emphasis">{!! __('Vítejte ve světě varhan!') !!}</h1>
            <p class="text-body-secondary mb-0">
                <span class="fs-5 d-none d-md-inline">{!! $description !!}</span>
                <span class="fs-6 d-md-none">{!! $description !!}</span>
            </p>
        </div>
        
        <div class="mb-4 mb-md-5 m-auto" style="max-width: 600px">
            <livewire:search id="welcomeSearch" placeholder="{{__('Hledejte varhany, varhanáře, varhanní rejstříky...') }}" />
            <div class="form-text text-center">{{ __('např. Kladruby, Rieger-Kloss, Flétna trubicová') }}</div>
        </div>
        
        @isset($organOfDay)
            <div class="row justify-content-center mb-4 mb-md-5">
                <div
                    class="organ-of-day col col-lg-9 text-center"
                    data-href="{{ route('organs.show', $organOfDay->slug) }}"
                    onclick="openOrganOfDay(event)"
                    style="cursor: pointer;"
                >
                    <div class="border border-tertiary rounded p-2">
                        <h3 class="fs-5">{{ __('Varhany dne') }}</h3>
                        <figure class="mb-0">
                            <div class="position-relative mb-1">
                                <img class="organ-of-day-image rounded border" src="{{ $organOfDay->image_url }}" @isset($organOfDay->image_credits) title="{{ __('Licence obrázku') }}: {{ $organOfDay->image_credits }}" @endisset />
                            </div>
                            <figcaption>
                                <strong>{{ $organOfDay->municipality }}</strong> &nbsp;|&nbsp; {{ $organOfDay->place }}
                                <br />
                                <x-organomania.organ-builder-link :organBuilder="$organOfDay->organBuilder" :yearBuilt="$organOfDay->year_built" :showIcon="false" />
                                &nbsp;|&nbsp;
                                <span class="text-body-secondary">
                                    {{ $organOfDay->manuals_count }} <small>{{ $organOfDay->getDeclinedManuals() }}</small>@if ($organOfDay->stops_count),
                                        {{ $organOfDay->stops_count }} <small>{{ $organOfDay->getDeclinedStops() }}</small>
                                    @endif
                                </span>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        @endisset
        
        <h2 class="text-center fs-3 mb-3">{{ __('Kam dál?') }}</h2>
        
        <div class="row g-4 align-items-stretch mb-3">
            <div class="organ col-lg-4 mx-auto" style="cursor: pointer;" data-target-url="{{ route('about-organ') }}" onclick="location.href = this.dataset.targetUrl">
                <div class="position-relative p-2 border border-tertiary rounded h-100 w-100">
                    <div class="d-flex">
                        <h5 class="me-2 mb-0 mt-0 me-auto align-self-center">
                            <i class="bi bi-file-text"></i>
                            {{ __('O varhanách') }}
                        </h5>
                        <p class="mb-0">
                            <a class="btn btn-sm btn-secondary" href="{{ route('about-organ') }}" wire:navigate>{{ __('Zobrazit') }} »</a>
                        </p>
                    </div>
                    <small>
                        {{ __('Varhany a jejich stylový vývoj v českých zemích.') }}
                    </small>
                </div>
            </div>
        </div>
        
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
                            <i class="bi-record-circle"></i> {{ __('Encyklopedie rejstříků') }} »
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
                {{ __('Záměrem je poskytnout rámcový přehled, ne kompletní rejstřík varhan, viz sekci') }}
                <a href="{{ route('about') }}" class="link-primary text-decoration-none" wire:navigate>{{ __('O webu') }}</a>.
                <br class="d-none d-md-inline" />
                {{ __('Připomínky a podněty k obsahu zasílejte na') }}
                <a href="mailto:{{ config('custom.app_admin_email') }}">{{ config('custom.app_admin_email') }}</a>.
            </p>
            <p class="text-body-tertiary">
                {{ __('Poslední aktualizace') }}: {{ Carbon::instance($lastUpdate)->isoFormat('LLL') }}
            </p>
        </div>
        
        <div class="text-center mb-4" style="height: 130px">
            <div class="fb-page" data-href="https://www.facebook.com/organomania.varhany/" data-tabs="" data-width="500" data-height="70" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/organomania.varhany/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/organomania.varhany/">Organomania: varhany v České republice</a></blockquote></div>
        </div>
    </div>
        
    <script>
        function openOrganOfDay(e) {
            if (!$(event.target).closest('.organ-builder-link').length)
                Livewire.navigate(e.currentTarget.dataset.href)
        }
    </script>
</x-app-bootstrap-layout>