<div class="modal fade" id="registersReferencesModal" tabindex="-1" data-focus="false" aria-labelledby="registersReferencesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="registersReferencesModalLabel">
                    {{ __('Použitá literatura') }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Zavřít') }}"></button>
            </div>

            <div class="modal-body">
                <x-organomania.link-list>
                    <x-organomania.link-list-item icon="book" url="https://www.baerenreiter.cz/cs/dilo/belsky-vratislav/4518-nauka-o-varhanach">
                        Vratislav Bělský: Nauka o varhanách
                        <x-slot:description>BĚLSKÝ, Vratislav. <em>Nauka o varhanách</em>. 4. vyd., (V Editio Bärenreiter Praha vyd. 1.). Praha: Editio Bärenreiter, 2000. ISBN 80-86385-04-3.</x-slot>
                    </x-organomania.link-list-item>
                    
                    <x-organomania.link-list-item icon="book" url="https://namu.cz/kapitoly-o-varhanach">
                        Václav Syrový: Kapitoly o varhanách
                        <x-slot:description>SYROVÝ, Václav. <em>Kapitoly o varhanách</em>. Vyd. 2., dopl., přeprac. Akustická knihovna Zvukového studia Hudební fakulty AMU. V Praze: Akademie múzických umění, 2004. ISBN 80-7331-009-0.</x-slot>
                    </x-organomania.link-list-item>
                    
                    <x-organomania.link-list-item url="http://www.organstops.org">
                        Encyclopedia of Organ Stops
                        <x-slot:description><em>Encyclopedia of Organ Stops</em>. Online. 2024. Dostupné z: http://www.organstops.org.</x-slot>
                    </x-organomania.link-list-item>
                </x-organomania.link-list>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Zavřít') }}</button>
            </div>
        </div>
    </div>
</div>
