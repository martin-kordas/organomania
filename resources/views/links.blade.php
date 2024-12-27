<x-app-bootstrap-layout>
    <div class="about container">
        <h3>{{ __('Zajímavé odkazy') }}</h3>

        <x-organomania.link-list class="mt-3">
            <x-organomania.link-list-item url="http://www.varhany.net">
                {{ __('Varhany a varhanáři v České republice (varhany.net)') }}
                <x-slot:description>{{ __('detailní odborná databáze varhan v ČR') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item url="https://casopisvarhanik.cz/">
                {{ __('Časopis Varhaník') }}
                <x-slot:description>{{ __('časopis pro varhanickou praxi') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item url="http://www.anatomie-varhan.cz/">
                {{ __('Anatomie varhan') }}
                <x-slot:description>{{ __('podrobný web o varhanách a jejich konstrukci') }}</x-slot>
            </x-organomania.link-list-item>
        </x-organomania.link-list>

        
        <h4 class="mt-4">{{ __('Varhany v médiích') }}</h4>
        
        <x-organomania.link-list class="mt-3">
            <x-organomania.link-list-item icon="book" url="https://www.klasikaplus.cz/rubrika/serial/varhany-a-varhanici/">
                <x-slot:source>{{ __('Klasika Plus') }}</x-slot>
                {{ __('Varhany a varhaníci') }}
                <x-slot:description>{{ __('seriál článků nejen o významných varhanách v ČR') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://operaplus.cz/serialy/prazske-varhany/">
                <x-slot:source>{{ __('Opera+') }}</x-slot>
                {{ __('Pražské varhany') }}
                <x-slot:description>{{ __('seriál článků o vybraných varhanách v Praze') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="volume-up" url="https://vltava.rozhlas.cz/prazske-varhany-odhalte-s-nami-tajemstvi-techto-kralovskych-hudebnich-nastroju-8390495">
                <x-slot:source>{{ __('Český rozhlas') }}</x-slot>
                {{ __('Pražské varhany') }}
                <x-slot:description>{{ __('série krátkých audiopořadů o vybraných varhanách v Praze') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="volume-up" url="https://vltava.rozhlas.cz/drevo-a-cin-9052430">
                <x-slot:source>{{ __('Český rozhlas') }}</x-slot>
                {{ __('Dřevo a cín') }}
                <x-slot:description>{{ __('podcast o varhanách v Santiniho kostelích, prokládaný improvizačními ukázkami') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="volume-up" url="https://vltava.rozhlas.cz/znejici-poklady-varhany-moravskoslezskeho-kraje-ve-stare-hudbe-8884410">
                <x-slot:source>{{ __('Český rozhlas') }}</x-slot>
                {{ __('Znějící poklady. Varhany Moravskoslezského kraje ve staré hudbě') }}
                <x-slot:description>{{ __('pořad o zajímavých historických i nově postavených varhanách, proložený řadou nahrávek') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="camera-video" url="https://www.ceskatelevize.cz/porady/15013853311-varhanni-nej">
                <x-slot:source>{{ __('Česká televize') }}</x-slot>
                {{ __('Varhanní nej') }}
                <x-slot:description>{{ __('cyklus půlhodinových televizních pořadů o zajímavých varhanách v ČR') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="camera-video" url="https://www.youtube.com/playlist?list=PLHvFzMdel2Kgo7vhyFEORAMmQ2JV-G4mD">
                <x-slot:source>{{ __('Youtube') }}</x-slot>
                {{ __('Konference pro varhany') }}
                <x-slot:description>{{ __('záznam přednášek, které zazněly na konferencích pořádaných HAMU v Praze a spolkem PROVARHANY') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="camera-video" url="https://www.dltm.cz/dokumentarni-cyklus-putovani-za-varhanami-litomericke-dieceze2">
                <x-slot:source>{{ __('Youtube') }}</x-slot>
                {{ __('Putování za varhanami Litoměřické diecéze') }}
                <x-slot:description>{{ __('cyklus výpravných pořadů o zajímavých varhanách, uváděný organologem Litoměřické diecéze Radkem Rejškem') }}</x-slot>
            </x-organomania.link-list-item>
        </x-organomania.link-list>
        
        <h4 class="mt-4">{{ __('Literatura') }}</h4>
        
        <x-organomania.link-list class="mt-3">
            <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/recenze/nejvyznamnejsi-varhany-ceske-republiky-426600">
                Štěpán Svoboda, Jiří Krátký: Nejvýznamnější varhany v České republice
                <x-slot:description>{{ __('kniha obsahuje medailony více než 110 vzácných varhan, doplněné bohatým obrazovým materiálem') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/historicke-varhany-v-cechach-199635">
                Lubomír Tomší et al.: Historické varhany v Čechách
                <x-slot:description>{{ __('kniha dokumentující řadu historických varhan na území Čech') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.cbdb.cz/kniha-255158-barokni-varhanarstvi-na-morave-dil-1-varhanari">
                Jiří Sehnal: Barokní varhanářství na Moravě - I. Varhanáři
                <x-slot:description>{{ __('odborná publikace detailně mapující varhanářství na Moravě až do poloviny 19. století') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.cbdb.cz/kniha-255157-barokni-varhanarstvi-na-morave-dil-2-varhany">
                Jiří Sehnal: Barokní varhanářství na Moravě - II. Varhany
                <x-slot:description>{{ __('druhá část publikace věnující se konkrétním varhanám') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/varhany-kralovehradecke-dieceze-82780">
                Václav Uhlíř: Varhany Královéhradecké diecéze
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/varhany-a-jejich-osudy-81909">
                Jan Tomíček: Varhany a jejich osudy
                <x-slot:description>{{ __('sbírka čtivých pojednání o vybraných domácích varhanách a varhanářích') }}</x-slot>
            </x-organomania.link-list-item>
            
            <x-organomania.link-list-item icon="book" url="https://www.baerenreiter.cz/cs/dilo/belsky-vratislav/4518-nauka-o-varhanach">
                Vratislav Bělský: Nauka o varhanách
                <x-slot:description>{{ __('osvědčená publikace o historii, stavbě a zvukové podstatě varhan') }}</x-slot>
            </x-organomania.link-list-item>
        </x-organomania.link-list>
        
    </div>
</x-app-bootstrap-layout>