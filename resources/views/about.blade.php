<x-app-bootstrap-layout>
    <div class="about container">
        <h3>{{ __('O webu') }}</h3>

        <p class="alert alert-primary mt-3">
            {!! __('Snahou <em>Organomanie</em> je poskytnout stručný <strong>přehled o varhanní kultuře</strong> v České republice, a to atraktivní formou přístupnou i laické veřejnosti.') !!}
            {!! __('Hlavní motivací je zvýšit povědomí o <strong>velkém množství historických i moderních nástrojů</strong>, které se u nás nacházejí.') !!}
        </p>
        
        <img class="rounded float-end ms-3" src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Pfeifen_SW_ev_Kirche_OberDorstfeld_Dortmund.jpg/360px-Pfeifen_SW_ev_Kirche_OberDorstfeld_Dortmund.jpg" title="{{ __('Licence obrázku') }}: Eliasorgel, CC BY-SA 4.0, via Wikimedia Commons"  style="max-height: 310px; width: auto; max-width: 50%" />
        <h4>{{ __('Koncepce') }}</h4>
        
        <p>
            {!! __('<strong>Nápad na vznik projektu</strong> přišel během autorova studia na Akademii múzických umění v Praze, kdy bylo poměrně obtížné vytvořil si ucelený přehled o významných domácích varhanách a varhanářích, o jejich kategorizaci a vzájemných vztazích.') !!}
            {!! __('To podnítilo vznik absolventské práce na téma <em><a href="/other/prehled-vyznamnych-varhan-formou-interaktivni-mapy-diplomova-prace.pdf" target="_blank">Přehled významných varhan v českých zemích formou interaktivní mapy</a></em>, obhájené na Konzervatoři Pardubice v roce 2022.') !!}
            {!! __('Z této práce web <em>Organomania</em> obsahově vychází.') !!}
        </p>
        
        <p>
            {!! __('Nadto se <em>Organomania</em> pokouší plně využít svůj potenciál <strong>interaktivní webové aplikace</strong>.') !!}
            {{ __('Umožňuje uživatelům vlastním způsobem třídit a kategorizovat záznamy nebo i přidávat své vlastní.') }}
            {{ __('Interaktivním způsobem lze vytvářet a prohlížet také dispozice varhan.') }}
            {{ __('Zajímavým experimentem je možnost ukládat si k dispozicím registrace skladeb.') }}
        </p>
        
        <div class="text-center mb-3">
        </div>
        
        <h4>{{ __('Poznámky k obsahu') }}</h4>
        
        <p>
            {!! __('Obsah uváděný na webu má <strong>stručný, přehledový charakter</strong>.') !!}
            {{ __('Nevychází z vlastní dokumentační činnosti, nýbrž z literatury a veřejně dostupných zdrojů.') }}
            {!! __('Obrazové materiály jsou převzaty z <em>Wikimedia Commons</em> (autor a licence obrázku se zobrazí po najetí myši).') !!}
        </p>
            
        <p class="mb-0">
            {!! __('O uváděných <strong>parametrech varhan</strong> platí:') !!}
        </p>
        <ul>
            <li>{{ __('Záměrem je uvádět současný (ne historický) stav. V případech, kdy je nedostatek dostupných zdrojů, mohou být nicméně informace neaktuální.') }}</li>
            <li>{{ __('Údaj o počtu rejstříků může v některých případech započítávat i transmise a extenze.') }}</li>
            <li>{{ __('Kategorizace varhan podle traktur zohledňuje tónovou, ne rejstříkovou trakturu.') }}</li>
        </ul>
        
        <img class="rounded float-start me-3" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/2022_Byst%C5%99ice_lustr_a_varhany.jpg/360px-2022_Byst%C5%99ice_lustr_a_varhany.jpg" title="{{ __('Licence obrázku') }}: Qasinka, CC0, via Wikimedia Commons" style="max-height: 310px; width: auto; max-width: 50%" />
        <p>
            {!! __('Pro usnadnění orientace bývají záznamy seřazeny podle <strong>"významu"</strong>.') !!}
            {{ __('Je evidentní, že význam určitých varhan nebo varhanáře záleží na mnoha faktorech a nelze jej objektivně stanovit.') }}
            {{ __('Uváděná míra významu je tedy vždy nedokonalá a orientační.') }}
            {{ __('Totéž platí o kategorizaci varhan a varhanářů podle stylového období.') }}
        </p>
        
        <p>
            {!! __('Diskutabilní je i <strong>výběr varhan a varhanářů</strong>, které web pokrývá.') !!}
            {{ __('Záměrem je poskytnout přehled pouze těch "významných".') }}
            {{ __('Skupinu "významných" však opět není možné objektivně definovat, ani rozsahově pokrýt.') }}
            {{ __('V praxi se tedy při výběru varhan uplatnila řada hledisek: starobylost, velikost a konstrukční zajímavost varhan, historický význam místa, kde stojí, hodnotná varhanní skříň nebo i nedávno proběhlé restaurování varhan a jejich aktivní hudební využívání.') }}
            {{ __('Zatímco nejvýznamější varhany jsou pokryty snad kompletně, v případě těch méně významných jde jen o vybranou výseč.') }}
        </p>
        
        <p>
            {{ __('Katalog varhanářů uvádí ze soudobých dílen pouze ty věnující se restaurování a stavbám větších nástrojů.') }}
            {{ __('Aktivních varhanářů je u nás podstatně více.') }}
        </p>
        
        <h4>{{ __('O autorovi') }}</h4>
        
        <p>
            {!! __('Autorem <em>Organomanie</em> musí být zákonitě <em>organomaniak</em>. Více se o něm dozvíte na <a href="/martin-kordas" target="_blank">samostatné straně</a>.') !!}
        </p>
        
        
    </div>
</x-app-bootstrap-layout>