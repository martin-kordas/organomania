<x-app-bootstrap-layout>
    <div class="about container">
        <h3>{{ __('O webu') }}</h3>

        <p>
            {{ __('Snahou webu je poskytnout stručný přehled o varhanní kultuře v České republice, a to atraktivní formou přístupnou i laické veřejnosti.') }}
            {{ __('Nápad se zrodil během autorova studia na Akademii múzických umění v Praze, kdy bylo poměrně obtížné vytvořil si ucelený přehled o významných domácích varhanách a varhanářích, o jejich kategorizaci a vzájemných vztazích.') }}
            {!! __('To podnítilo vznik absolventské práce na téma <em><a href="/other/prehled-vyznamnych-varhan-formou-interaktivni-mapy-diplomova-prace.pdf" target="_blank">Přehled významných varhan v českých zemích formou interaktivní mapy</a></em>, obhájené na Konzervatoři Pardubice v roce 2022.') !!}
            {!! __('Z této práce web <em>Organomania</em> obsahově vychází.') !!}
        </p>
        
        <p>
            {!! __('Nadto se <em>Organomania</em> pokouší plně využít svůj potenciál interaktivní webové aplikace.') !!}
            {{ __('Umožňuje uživatelům vlastním způsobem třídit a kategorizovat záznamy nebo i přidávat své vlastní.') }}
            {{ __('Interaktivním způsobem lze vytvářet a prohlížet také dispozice varhan.') }}
            {{ __('Zajímavým experimentem je možnost ukládat si k dispozicím registrace skladeb.') }}
        </p>
        
        <p>
            {{ __('Obsah uváděný na webu má stručný, přehledový charakter.') }}
            {{ __('Nevychází z vlastního výzkumu, ale z literatury a veřejně dostupných zdrojů.') }}
            {!! __('Obrazové materiály jsou převzaty z Wikimedia Commons (autor a licence obrázku se zobrazí po najetí myši).') !!}
            {{ __('Uváděné parametry varhan se snaží reflektovat jejich současný (ne historický) stav.') }}
            {{ __('Údaj o počtu rejstříků může v některých případech započítávat i transmise a extenze.') }}
        </p>
        
        <p>
            {{ __('Pro usnadnění orientace bývají záznamy seřazeny podle "významu".') }}
            {{ __('Je evidentní, že význam určitých varhan nebo varhanáře záleží na mnoha faktorech a nelze jej objektivně stanovit.') }}
            {{ __('Uváděná míra významu je tedy vždy nedokonalá a orientační.') }}
            {{ __('Totéž platí o kategorizaci varhan a varhanářů podle stylového období.') }}
        </p>
        
        <p>
            {!! __('Více informací o autorovi se dozvíte na <a href="/martin-kordas" target="_blank">samostatné straně</a>.') !!}
        </p>
    </div>
</x-app-bootstrap-layout>