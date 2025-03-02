<x-app-bootstrap-layout title="{{ __('Písně při bohoslužbě') }}">
    @push('meta')
        <meta name="description" content="{{ __('TODO') }}">
    @endpush
    
    <div class="about container">
        <div class="p-4 p-md-5 text-center bg-body-tertiary rounded-3">
            <h1 class="text-body-emphasis">{{ __('Písně při bohoslužbě') }}</h1>
            <p class="col-lg-8 mx-auto fs-5 text-muted">
                přehledná online evidence kancionálových písní zpívaných na bohoslužbách
            </p>
            <div class="d-inline-flex gap-2 mb-3">
                <a href="{{ route('demo-songs') }}" class="d-inline-flex align-items-center btn btn-primary btn px-4 rounded-pill" type="button" wire:navigate>
                    Vyzkoušet demo
                    &nbsp;<i class="bi bi-arrow-right"></i>
                </a>
                <button class="btn btn-outline-secondary btn px-4 rounded-pill" type="button" onclick="scrollToElement('#startUse')">
                    Začít používat
                </button>
            </div>
            
            <div class="mt-3">
                <a href="/images/worship-songs1.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs1.png" style="max-width: 100%" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7">
                <h2 class="featurette-heading fw-normal lh-1">Přehledná evidence písní</h2>
                <p class="lead">Písně zpívané na jednotlivých bohoslužbách zapisujete do kalendáře s přehledně vyznačenými liturgickými svátky. Výběr písní pro další bohoslužbu provedete díky online evidenci z pohodlí domova.</p>
            </div>
            <div class="col-md-5 text-center">
                <a href="/images/worship-songs2.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs2.png" style="max-width: 300px; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7 order-md-2">
                <h2 class="featurette-heading fw-normal lh-1">Statistika písní</h2>
                <p class="lead">S výběrem vhodné písně pomůže statistika – zjistíte, které písně se ve farnosti nezpívají a které se naopak v posledním období zpívaly již velmi často.</p>
            </div>
            <div class="col-md-5 text-center order-md-1">
                <a href="/images/worship-songs3.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs3.png" style="max-width: 100%; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7">
                <h2 class="featurette-heading fw-normal lh-1">Spolupráce s dalšími varhaníky</h2>
                <p class="lead">Evidenci písní lze formou odkazu snadno nasdílet dalším varhaníkům ve farnosti – ti pak mohou zapisovat písně, aniž by se museli složitě přihlašovat.</p>
            </div>
            <div class="col-md-5 text-center">
                <a href="/images/worship-songs4.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs4.png" style="max-width: 300px; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7 order-md-2">
                <h2 class="featurette-heading fw-normal lh-1">Plánování</h2>
                <p class="lead">Rozplánujte si výběr písní a ordinarií na několik týdnů dopředu. U každé bohoslužby je uvedeno jméno varhaníka, takže plánovat lze i varhanické služby.</p>
                <p class="lead" style="font-size: 95%">Není-li varhaník na bohoslužbu předem určený, nechejte varhaníky, ať se sami na termíny bohoslužeb zapíšou. S předstihem stačí na daný termín zapsat libovolnou 1 píseň (např. ordinarium), zbylé písně lze doplnit dodatečně.</p>
            </div>
            <div class="col-md-5 text-center order-md-1">
                <a href="/images/worship-songs5.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs5.png" style="max-width: 100%; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7">
                <h2 class="featurette-heading fw-normal lh-1">Přehled o liturgickém slavení</h2>
                <p class="lead">Vestavěný liturgický kalendář informuje o liturgických svátcích a jim příslušných čteních a pomůže i s výběrem responsoriálního žalmu.</p>
            </div>
            <div class="col-md-5 text-center">
                <a href="/images/worship-songs6.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs6.png" style="max-width: 300px; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7 order-md-2">
                <h2 class="featurette-heading fw-normal lh-1">Detailní zobrazení písní</h2>
                <p class="lead">Zaznačené písně si lze zobrazit včetně jejich názvu a s proklikem na varhanní doprovod. Tlačítko pro smazání zobrazí potvrzovací dialog s podrobnými informacemi, kdo a kdy píseň uložit.</p>
            </div>
            <div class="col-md-5 text-center order-md-1">
                <a href="/images/worship-songs7.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs7.png" style="max-width: 100%; max-height: 500px" />
                </a>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center">
            <div class="col-md-7">
                <h2 class="featurette-heading fw-normal lh-1">Snadný tisk a export</h2>
                <p class="lead">Evidenci písní lze snadno vytisknout nebo veškerá data vyexportovat do tabulkového CSV formátu.</p>
            </div>
            <div class="col-md-5 text-center">
                <a href="/images/worship-songs8.png" target="_blank">
                    <img class="rounded shadow" src="/images/worship-songs8.png" style="max-width: 300px; max-height: 500px" />
                </a>
            </div>
        </div>
        
        @php $number = 1 @endphp
        <div id="startUse" class="p-4 p-md-5 my-5 text-center bg-body-tertiary rounded-3">
            <h2 class="text-body-emphasis">Jak evidenci písní začít používat</h2>
            
            <div class="row g-4 mt-1 row-cols-1 row-cols-lg-3 justify-content-center">
                @if (!Auth::user())
                    <div class="col-auto d-flex align-items-start">
                        <div>
                            <div class="fs-2">{{ $number++ }}.</div>
                            <h3 class="fs-5 text-body-emphasis lh-base">Zaregistrujte/přihlaste se do Organomanie</h3>
                            <div>
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    Registrovat se
                                </a>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="bi-box-arrow-in-right"></i> Přihlásit se
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-auto d-flex align-items-start">
                    <div>
                        <div class="fs-2">{{ $number++ }}.</div>
                        <h3 class="fs-5 text-body-emphasis lh-base">Přidejte do Organomanie varhany Vašeho kostela</h3>
                        <a href="{{ route('organs.create') }}" class="btn btn-primary">
                            Přidat varhany
                        </a>
                    </div>
                </div>
                <div class="col-auto d-flex align-items-start">
                    <div>
                        <div class="fs-2">{{ $number++ }}.</div>
                        <h3 class="fs-5 text-body-emphasis lh-base">V detailu varhan klikněte na Zobrazit písně</h3>
                        <a href="/images/worship-songs9.png" target="_blank">
                            <img class="rounded shadow" src="/images/worship-songs9.png" style="max-width: 100%; max-height: 300px" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</x-app-bootstrap-layout>