@use(App\Enums\OrganBuilderCategory)
@use(App\Enums\OrganCategory)
@use(App\Enums\RegisterCategory)
@use(App\Models\Organ)
@use(App\Models\OrganBuilder)
@use(App\Models\RegisterName)

<x-app-bootstrap-layout>
    @push('meta')
        <meta name="description" content="{{ __('Poznejte varhany jako hudební nástroj, který se po zvukové i konstrukční stránce neustále vyvíjí. Prohlédněte si nejvýznamnější varhany a varhanáře jednotlivých stylových období.') }}">
    @endpush
    
    <div class="about-organ container">
        <div class="row gy-3">
            <h2 class="d-lg-none mb-0">O varhanách</h2>
            <div class="col-12 col-lg-4 col-xl-3">
                <nav id="content" class="h-100 flex-column align-items-stretch pe-lg-4 border-end">
                    <nav class="nav nav-pills flex-nowrap flex-column">
                        <a class="nav-link p-1 my-1" href="#aboutOrgan">Varhany jako hudební nástroj</a>
                        
                        <a class="nav-link p-1 my-1" href="#history">Stylový vývoj varhan v českých zemích</a>
                        <nav class="nav nav-pills flex-column">
                            <a class="nav-link p-1 ms-3 my-1" href="#renaissance">Renesanční varhanářství</a>

                            <a class="nav-link p-1 ms-3 my-1" href="#baroque">Barokní varhanářství</a>
                            <nav class="nav nav-pills flex-column small">
                                <a class="nav-link p-1 ms-5 my-1" href="#baroqueBohemia">Barokní varhanářství v Čechách</a>
                                <a class="nav-link p-1 ms-5 my-1" href="#baroqueMoravia">Barokní varhanářství na Moravě</a>
                                <a class="nav-link p-1 ms-5 my-1" href="#baroqueSilesia">Barokní varhanářství v Českém Slezsku</a>
                                <a class="nav-link p-1 ms-5 my-1" href="#baroque3Manuals">Třímanuálové barokní varhany</a>
                            </nav>

                            <a class="nav-link p-1 ms-3 my-1" href="#builtFrom1800To1859">Varhanářství v letech 1800–1859</a>
                            <nav class="nav nav-pills flex-column small">
                                <a class="nav-link p-1 ms-5 my-1" href="#builtFrom1800To1859OrganBuilders">Varhanáři a varhany</a>
                            </nav>

                            <a class="nav-link p-1 ms-3 my-1" href="#builtFrom1860To1944">Varhanářství v letech 1860–1944</a>
                            <nav class="nav nav-pills flex-column small">
                                <a class="nav-link p-1 ms-5 my-1" href="#builtFrom1860To1944Sound">Zvuková podstata varhan</a>
                                <a class="nav-link p-1 ms-5 my-1" href="#builtFrom1860To1944Construction">Konstrukce varhan</a>
                                <a class="nav-link p-1 ms-5 my-1" href="#builtFrom1860To1944OrganBuilders">Varhanáři a varhany</a>
                            </nav>

                            <a class="nav-link p-1 ms-3 my-1" href="#builtFrom1945To1989">Varhanářství v letech 1945–1989</a>

                            <a class="nav-link p-1 ms-3 my-1" href="#builtFrom1990">Varhanářství od roku 1990</a>
                            <nav class="nav nav-pills flex-column small">
                                <a class="nav-link p-1 ms-5 my-1" href="#builtFrom1990Renovation">Restaurování historických nástrojů</a>
                            </nav>

                            <a class="nav-link p-1 ms-3 my-1" href="#conclusion">Závěr</a>
                        </nav>
                    </nav>
                </nav>
            </div>
            
            <div class="col-12 col-lg-8 col-xl-9">
                <h3 id="aboutOrgan">{{ __('Varhany jako hudební nástroj') }}</h3>
                <p class="alert alert-primary mt-3">
                    {!! __('Varhany jsou starobylý <strong>klávesový nástroj</strong>, tradičně spjatý s církevním prostředím.') !!}
                    <br />
                    {!! __('Jelikož zvuk varhan vzniká chvěním vzduchu v píšťalách, řadíme varhany mezi <strong>aerofony</strong>.') !!}
                </p>

                <p>
                    {!! __('Základní přehled o varhanách a jejich stavbě získáte např. v článku <strong><a href="https://www.svatovitskevarhany.com/cs/co-jsou-to-varhany" target="_blank">Co jsou to varhany</a></strong> (web <em>Svatovítské varhany</em>).') !!}
                    <br />
                    {!! __('Podrobnější informace lze najít v <a href="/links#literatureGeneral">literatuře</a>.') !!}
                </p>

                <p>
                    V České republice se podle odhadů nachází až 10 tisíc varhan.
                    Jsou situovány nejen v kostelích, nýbrž i v koncertních sálech či školách.
                    Mnoho z nich naléhavě vyžaduje opravu.
                </p>
                
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block" title="Licence obrázku: Momimariani1962, CC BY-SA 4.0, via Wikimedia Commons">
                        <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/S.Giacomo_Maggiore_a_Praga.jpg/418px-S.Giacomo_Maggiore_a_Praga.jpg" style="max-width: 100%;" />
                        <br />
                        <em>
                            <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_JAKUBA_VETSIHO]" :showOrganBuilder="true" class="stretched-link" />
                        </em>
                    </div>
                </div>

                <h3 class="mt-4" id="history">{{ __('Stylový vývoj varhan v českých zemích') }}</h3>
        
                <div id="article" data-bs-spy="scroll" data-bs-target="#content" tabindex="0">
                    <p>
                        Varhany svojí zvukovou, konstrukční a výtvarnou povahou podávají svědectví o místě a době, kdy byly postaveny.
                        Řada historických nástrojů byla v průběhu let přestavěna nebo zcela zanikla.
                        I tak se na území České republiky náchází velký počet vzácných varhan, ojedinělý i v evropském měřítku.
                    </p>
                    
                    <p>
                        Je důležité si uvědomit, že velké množství nástrojů bylo nově postaveno do starší skříně.
                        Barokní skříň umístěná na kůru tedy nutně neznamená, že se tam nachází i barokní varhany.
                        Také podoba samotného varhanního stroje může být výsledkem přestaveb, realizovaných postupně v různých časových obdobích.
                        Varhanním skříním se v textu věnujeme spíše okrajově.
                        Můžete si nicméně zobrazit <a class="text-decoration-none" href="{{ route('organs.index', ['filterCategories' => [OrganCategory::ValuableCase]]) }}" target="_blank">přehled varhan s mimořádně cennou skříní</a>.
                    </p>
                    
                    <p>
                        Přehled o chronologii a lokalitě varhanářských dílen lze nejlépe získat pomocí <a class="text-decoration-none" href="{{ route('organ-builders.index', ['viewType' => 'timeline']) }}">časové osy varhanářů</a>.
                    </p>
                    
                    <div class="alert alert-info p-2 mx-auto terms">
                        <h6><i class="bi bi-info-circle"></i> Důležité pojmy</h6>
                        <dl class="row mb-0 small">
                            <dt class="col-md-3">Varhanní dispozice</dt>
                            <dd class="col-md-9">Dispozice je souhrnem zvukových a technických vlastností varhan &ndash; na prvním místě obsahuje seznam varhanních rejstříků</dd>
                            <dt class="col-md-3">Varhanní rejstřík</dt>
                            <dd class="col-md-9 mb-0">Sada píšťal určité zvukové barvy a výšky &ndash; pro každý manuál (klaviaturu) mají varhany samostatnou sadu rejstříků</dd>
                        </dl>
                    </div>

                    <x-organomania.about-organ-h4 id="renaissance" subtitle="nejstarší dochované nástroje" :organCategory="OrganCategory::Renaissance">
                        Renesanční varhanářství
                    </x-organomania.about-organ-h4>

                    <p>
                        Počátky varhanářství u nás sahají až do středověku.
                        Prvními dochovanými nástroji jsou však až varhany renesanční, z nichž ty nejstarší se nachází v <x-organomania.organ-link :iconLink="false" name="kostele Nejsvětější Trojice ve Smečně" :organ="$organs[Organ::ORGAN_ID_SMECNO]" :showOrganBuilder="true" :showSizeInfo="true" />.
                        Dalším mimořádně starobylým nástrojem je nástroj z kostela v Doksech, nyní umístěný v <x-organomania.organ-link :iconLink="false" name="katedrále sv. Štěpána v Litoměřicích na boční empoře" :organ="$organs[Organ::ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_BOCNI_EMPORA]" :showOrganBuilder="true" :showSizeInfo="true" />.
                    </p>

                    <p>
                        Renesanční varhanářství se kromě renesančního tvarosloví skříní vyznačuje pestrou rejstříkovou dispozicí, ve které jsou obsaženy barevně navzájem kontrastující rejstříky, imitující nejrůznější nástroje.
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Štěpán Svoboda, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Pipe_organs_in_Church_of_Holy_Trinity_in_Sme%C4%8Dno.jpg/330px-Pipe_organs_in_Church_of_Holy_Trinity_in_Sme%C4%8Dno.jpg" style="max-width: 300px;" />
                            <br />
                            <em><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_SMECNO]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" /></em>
                        </div>
                    </div>

                    <x-organomania.about-organ-h4 id="baroque" subtitle="zlatý věk varhan" :organBuilderCategory="OrganBuilderCategory::BuiltTo1799" :organCategory="OrganCategory::BuiltTo1799">
                        Barokní varhanářství
                    </x-organomania.about-organ-h4>
                    
                    <p>
                        Vzestup církve a rozvoj sakrálního umění v období baroka dává vzniknout tzv. <em>zlatému věku</em> varhan.
                        Stavbě varhan se věnují špičkové varhanářské dílny, často vícegenerační a koncentrované do varhanářských center (Praha, Brno, Loket, Králíky&hellip;)
                    </p>

                    <p>
                        V barokním období existují v jednotlivých evropských zemích stylově odlišné typy nástrojů.
                        Naše barokní varhany se řadí k tzv. <em>jihoněmeckému varhanářství</em>, což je dáno příslušností českých zemí k německojazyčným katolickým oblastem.
                        Oproti <em>severoněmeckým varhanám</em>, které se vyvinuly v kontaktu s protestantskou bohoslužbou, jsou jihoněmecké nástroje obecně méně rozsáhlé a mají menší tónový rozsah (tzv. <em>krátkou oktávu</em> &ndash; viz obrázek níže). Vykazují také výrazně menší podíl <x-organomania.category-badge :category="RegisterCategory::Reed" :newTab="true">jazykových rejstříků</x-organomania.category-badge> a jejich pedál je omezen na basovou funkci (nevede vlastní melodii).
                        Charakteristice jihoněmeckých varhan přirozeně odpovídá dobová hudba pro ně napsaná.
                        Interpretovat skladbu napsanou pro jiný typ nástroje na nich lze jen s obtížemi &ndash; např. hudba J. S. Bacha, napsaná pro severoněmecký typ varhan, často překračuje tónový rozsah a barevné možnosti jihoněmeckých varhan.
                    </p>
                    
                    <div class="text-center mb-3">
                        <img class="rounded mb-2" src="/images/kratka-oktava.jpg" style="max-width: 100%;" title="Licence obrázku: Schmeissnerro, CC BY-SA 4.0, via Wikimedia Commons" />
                        <br />
                        <em>
                            pedálová klaviatura s krátkou oktávou
                            <br />
                            <small>(ve spodní části klaviatury je tón D na místě Fis a tón E na místě Gis, ve výsledku tedy chybí tóny Cis, Dis, Fis a Gis)</small>
                        </em>
                    </div>
                    
                    <div class="ms-md-3 mb-3 float-md-end text-center mx-auto" style="width: 290px; max-width: 100%;" title="Licence obrázku: Ludek, CC BY-SA 3.0, via Wikimedia Commons">
                        <img class="rounded mb-2 w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Chram_sv_Mikulase_interier_varhany.jpg/360px-Chram_sv_Mikulase_interier_varhany.jpg" />
                        <br />
                        <em>
                            Praha, kostel sv. Mikuláše (Malá Strana), levá empora <span class="text-body-secondary">(Schwarz, 1746, II/17)</span>
                            &ndash; příklad samostatně stojícího hracího stolu
                        </em>
                    </div>
                    
                    <p>
                        Z hlediska konstrukce používá naše barokní varhanářství výhradně <x-organomania.category-badge :category="OrganCategory::ActionMechanical" :newTab="true">mechanickou trakturu</x-organomania.category-badge> a <x-organomania.category-badge :category="OrganCategory::WindchestSchleif" :newTab="true">zásuvkovou vzdušnici</x-organomania.category-badge>.
                        Častým způsobem prostorového uspořádání je u vícemanuálových varhan rozdělení nástroje do dvou skříní, které jsou umístěny po stranách západního okna nad kůrem.
                        (U varhan z pozdějších období bývá toto okno zakryto varhanním strojem, což kůr i zbytek kostela poněkud zatemňuje.)
                        Stroj slabšího manuálu může být realizován jako tzv. <em>zadní pozitiv</em>, vestavěný do zábradlí kůru.
                        Toto specifické umístění zadního pozitivu přispívá k zajímavému zvukovému kontrastu s hlavním strojem.
                        Hrací stůl často není zabudován do varhanní skříně, ale stojí samostatně.
                        Takové řešení je konstrukčně složitější, ale umožňuje varhaníkovi doprovázejícímu bohoslužbu snadno sledovat dění v lodi kostela.
                        Barokní varhany mají specifické ladění, ve kterém zní akordy běžných tónin (např. C-dur nebo G-dur) čistě, zatímco akordy tónin s větším počtem předznamenání (např. H-dur) zní mírně rozladěně.
                        Této skutečnosti dobová varhanní hudba příhodně využívá jako výrazového prostředku.
                    </p>
                    
                    <p>
                        Estetika barokních varhanních skříní prochází živým vývojem.
                        Půdorysné linie skříní začínají být během 18. století zvlněné.
                        Na konci 18. století se často objevují rokokové vázy.
                    </p>
                    
                    <p>
                        Období, kdy se při stavbě varhan uplatňují principy barokního varhanářství, je podstatně delší než epocha hudebního baroka &ndash; tradice barokního varhanářství u nás doznívají ještě v 1. polovině 19. století.
                    </p>

                    <div class="alert alert-info p-2 mx-auto terms mb-3" style="clear: both">
                        <h6><i class="bi bi-info-circle"></i> Důležité pojmy</h6>
                        <dl class="row mb-0 small">
                            @foreach ([
                                OrganCategory::ActionMechanical, OrganCategory::WindchestSchleif, RegisterCategory::Reed,
                            ] as $category)
                                <dt class="col-md-3"><x-organomania.category-badge :category="$category" :newTab="true" /></dt>
                                <dd @class(['col-md-9', 'mb-0' => $loop->last])>{{ $category->getDescription() }}</dd>
                            @endforeach
                        </dl>
                    </div>
                    
                    <div class="text-center mb-4 m-auto" style="width: 560px; max-width: 100%;">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Ondraness, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Plasy_-_varhany.jpg/640px-Plasy_-_varhany.jpg" style="max-width: 100%" />
                            <br />
                            <em>
                                <em>
                                    <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PLASY]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" /> &ndash; uspořádání skříní po stranách okna a zadní pozitiv v zábradlí
                                </em>
                            </em>
                        </div>
                    </div>

                    <h5 id="baroqueBohemia">Barokní varhanářství v Čechách</h5>

                    <p>
                        Za vrchol barokního varhanářství u nás bývá považováno dílo zakladatele <strong>loketské</strong> varhanářské školy <x-organomania.organ-builder-link :iconLink="false" name="Abraháma STARKA" activePeriod="1659–1709" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_STARKOVE]" :showActivePeriod="true" />. K jeho nejslavnějším dochovaným nástrojům patří varhany v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PLASY]" name="kostele Nanebevzetí Panny Marie v Plasích" :showSizeInfo="true" /> a o něco menší varhany v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_ZLATA_KORUNA]" name="kostele Nanebevzetí Panny Marie ve Zlaté Koruně" :showSizeInfo="true" />.
                    </p>

                    <p>
                        Dalším významným varhanářem loketské školy byl <x-organomania.organ-builder-link :iconLink="false" name="Leopold BURKHARDT" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_LEOPOLD_BURKHARDT]" :showActivePeriod="true" />.
                        Na proslulých varhanách v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_KLADRUBY]" name="kostele Nanebevzetí Panny Marie v Kladrubech" :showSizeInfo="true" /> spolupracoval se stavitelem chrámu <em>Janem Blažejem Santinim-Aichlem</em> (1677-1723), který navrhl varhanní skříň.
                    </p>

                    <p class="mb-0">
                        Řada schopných varhanářů působila v <strong>Praze</strong>. Uvádíme je spolu s příklady jejich nejznámějších děl.
                    </p>
                    <ul class="items-list">
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_HEINRICH_MUNDT]" :showActivePeriod="true" />
                            <ul>
                                <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_MATKY_BOZI_PRED_TYNEM]" :showSizeInfo="true" /> &ndash; mimořádně cenný a starobylý nástroj</li>
                            </ul>
                        </li>
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_TOMAS_SCHWARZ]" :showActivePeriod="true" />
                            <ul>
                                <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_VELKE_VARHANY]" :showSizeInfo="true" /> &ndash; nástroj bohužel poznamenaný přestavbami
                            </ul>
                        </li>
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_LEOPOLD_SPIEGEL]" :showActivePeriod="true" />
                        </li>
                    </ul>

                    <p>
                        Mnoha cenným nástrojům dala vzniknout <x-organomania.organ-builder-link :iconLink="false" name="králická varhanářská dílna" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_KRALICKA_DILNA]" :showActivePeriod="true" :showMunicipality="true" /> &ndash; varhanáři Jan HALBIG, Kašpar WELZEL, František KATZER a další.
                        Ve venkovských kostelích stavěl varhany <x-organomania.organ-builder-link :iconLink="false" name="Bedřich SEMRÁD" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_BEDRICH_SEMRAD]" :showActivePeriod="true" :showMunicipality="true" />.
                    </p>

                    <p class="mb-0">
                        K dalším střediskům barokního varhanářství v Čechách patří:
                    </p>
                    <ul class="items-list">
                        <li><strong>Čistá</strong> &ndash; rod <x-organomania.organ-builder-link name="Guthů" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_GUTHOVE]" :showActivePeriod="true" :iconLink="false" /></li>
                        <li><strong>Kutná Hora</strong> &ndash; rod <x-organomania.organ-builder-link name="Horáků" activePeriod="2. pol. 18. stol – poč. 19. stol." :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_PAVEL_FRANTISEK_HORAK]" :showActivePeriod="true" :iconLink="false" /></li>
                        <li><strong>Tachov</strong> &ndash; rod <x-organomania.organ-builder-link name="Gartnerů" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_GARTNEROVE]" :showActivePeriod="true" :iconLink="false" /></li>
                        <li><strong>Vrchlabí</strong> &ndash; rod <x-organomania.organ-builder-link name="Tauchmannů" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_TAUCHMANNOVE]" :showActivePeriod="true" :iconLink="false" /></li>
                    </ul>
                    
                    <div class="text-center mb-4 mx-auto" style="width: 560px; max-width: 100%;">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Capkova Pavlina, CC BY-SA 3.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Kostel_Nav%C5%A1t%C3%ADven%C3%AD_P._Marie_na_Sv_._Kope%C4%8Dku_u_Olomouce_-_varhany.JPG/640px-Kostel_Nav%C5%A1t%C3%ADven%C3%AD_P._Marie_na_Sv_._Kope%C4%8Dku_u_Olomouce_-_varhany.JPG" style="max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_OLOMOUC_SVATY_KOPECEK_HLAVNI_KUR]" :showOrganBuilder="false" :year="false" class="stretched-link" />
                                &ndash; skříň pochází z varhan Jana Davida Siebera a Antonína Richtera (1724)
                            </em>
                        </div>
                    </div>

                    <h5 id="baroqueMoravia">Barokní varhanářství na Moravě</h5>

                    <p>
                        Na Moravě byli nejvíce ceněni varhanáři <strong>brněnští</strong>.
                        Mistrovství nejslavnějšího z nich, <x-organomania.organ-builder-link :iconLink="false" name="Jana Davida SIEBERA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JAN_DAVID_SIEBER]" :showActivePeriod="true" />, reprezentuje největší u nás dochovaný nástroj jeho dílny &ndash; varhany v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_POLNA_KOSTEL_NANEBEVZETI_PANNY_MARIE_VELKE_VARHANY]" name="kostele Nanebevzetí Panny Marie v Polné" :showSizeInfo="true" />.
                        Další Sieberův nástroj v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_ZDAR]" name="klášterním kostele ve Žďáru nad Sázavou" :showSizeInfo="true" /> je umístěn v unikátní čtyřstranné varhanní skříni, navržené opět <em>Janem Blažejem Santinim-Aichlem</em> (1677-1723).
                    </p>
                    <p>        
                        Na Sieberovu tradici navázal pozdně barokní brněnský varhanář <x-organomania.organ-builder-link :iconLink="false" name="Jan VÝMOLA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JAN_VYMOLA]" :showActivePeriod="true" />.
                        Jeho největším dochovaným nástrojem jsou varhany v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_DUB_NAD_MORAVOU]" name="Dubu nad Moravou" :showSizeInfo="true" />.
                        Velmi ceněný je i o něco menší Výmolův nástroj v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_DOUBRAVNIK]" name="kostele Povýšení sv. Kříže v Doubravníku" :showSizeInfo="true" />.
                    </p>

                    <p>
                        Mezi další varhanářská centra patřilo na Moravě <strong>Znojmo</strong>, kde působil např. <x-organomania.organ-builder-link :iconLink="false" name="Josef SILBERBAUER" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JOSEF_SILBERBAUER]" :showActivePeriod="true" />.
                    </p>
                    
                    <p>
                        Do moravského varhanářství se nesmazatelně zapsal slezský varhanář <x-organomania.organ-builder-link :iconLink="false" name="Michael ENGLER" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_MICHAEL_ENGLER]" :showActivePeriod="true" />, a to stavbou velkých, pro naše varhanářství netypických varhan v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE]" name="kostele sv. Mořice v Olomouci" :showSizeInfo="true" />.
                    </p>
                    
                    <div class="text-center mb-4 mx-auto" style="width: 500px; max-width: 100%;">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Pohled 111, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/And%C4%9Bl%C3%A9_nesouc%C3%AD_chr%C3%A1my_07.jpg/640px-And%C4%9Bl%C3%A9_nesouc%C3%AD_chr%C3%A1my_07.jpg" style="max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_ZDAR]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                                &ndash; unikátní Santiniho skřín
                            </em>
                        </div>
                    </div>

                    <h5 id="baroqueSilesia">Barokní varhanářství v Českém Slezsku</h5>

                    <p class="mb-0">
                        V Českém Slezsku bychom našli 2 varhanářská centra:
                    </p>
                    <ul>
                        <li><strong>Andělská Hora</strong> &ndash; rod <x-organomania.organ-builder-link name="Staudingerů" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_STAUDINGEROVE]" :showActivePeriod="true" :iconLink="false" /></li>
                        <li><strong>Opava</strong> &ndash; rod <x-organomania.organ-builder-link name="Horčičků" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_HORCICKOVE]" :showActivePeriod="true" :iconLink="false" /> a další</li>
                    </ul>

                    <h5 id="baroque3Manuals">Třímanuálové barokní varhany</h5>

                    <p>
                        Pro potřeby katolické liturgie postačovaly dvoumanuálové varhany, a proto u nás byla stavba konstrukčně komplikovaných třímanuálových nástrojů spíše ojedinělá.
                    </p>
                    <p>
                        Žádné z třímanuálových barokních varhan postavených na našem území se bohužel nedochovaly v nezměněné podobě.
                        V tomto ohledu je vhodné připomenout třímanuálové varhany brněnského varhanáře <x-organomania.organ-builder-link :iconLink="false" name="Jana Davida SIEBERA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JAN_DAVID_SIEBER]" :showActivePeriod="false" />, které postavil pro <em>kostel sv. Michala ve Vídni</em> <span class="text-body-secondary">(1714, III/40)</span> a které se podařilo do původní podoby restaurovat v r. 1987.
                    </p>

                    <p class="mb-0">
                        Z alespoň částečně dochovalých třímanuálových varhan na našem území zmiňme:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE]" size="III/44" :showOrganBuilder="true" :showSizeInfo="true" :iconLink="false" /> &ndash; dispozice rozšířena ve 20. století</li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_VELKE_VARHANY]" :showOrganBuilder="true" :showSizeInfo="true" :iconLink="false" /> &ndash; přestavováno v 19. a 20. století</li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_TEPLA]" :showOrganBuilder="true" :showSizeInfo="true" :iconLink="false" /> &ndash; dispozice upravena v 19. století a 20. století</li>
                        <li>Týn nad Vltavou, kostel sv. Jakuba <span class="text-body-secondary">(Semrád, 1777, III/26)</span> &ndash; nástroj byl Bedřichem Semrádem stavěn v několika etapách a v 19. století rozšiřován</li>
                    </ul>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Ludek, CC BY-SA 3.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Chram_sv_Mikulase_interier_vstup-varhany.jpg/405px-Chram_sv_Mikulase_interier_vstup-varhany.jpg" style="max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_VELKE_VARHANY]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                            </em>
                        </div>
                    </div>


                    <x-organomania.about-organ-h4 id="builtFrom1800To1859" subtitle="na rozcestí baroka a romantismu" :organBuilderCategory="OrganBuilderCategory::BuiltFrom1800To1859" :organCategory="OrganCategory::BuiltFrom1800To1859">
                        Varhanářství v letech 1800–1859
                    </x-organomania.about-organ-h4>

                    <p>
                        V rámci josefínských reforem je na konci 18. století zrušena řada klášterů a nastává úpadek církevní hudby.
                        Začátek 19. století je navíc ve znamení napoleonských válek a na ně navazujících ekonomických problémů Habsburské monarchie.
                        Tyto skutečnosti přispívají i k omezení stavby varhan a k úpadku varhanářského řemesla.
                        Mnozí varhanáři ve svém oboru začínají jako samouci, vzdělaní pouze studiem existujících nástrojů.
                        Pozornost společnosti poutá především symfonická hudba a objevují se pokusy připodobnit zvukovost varhan orchestrálnímu ideálu. Někdy se to děje způsobem pro varhany zcela necitlivým &ndash; viz tzv. <em>simplifikační systém</em> hudebního teoretika <em>Georga Josepha Voglera</em> (1749-1814).
                    </p>

                    <p>
                        Varhany tohoto období se označují jako <em>pozdně barokní</em>, případně <em>raně romantické</em>.
                        Konstrukčně stále vycházejí z osvědčených barokních principů (použití <x-organomania.category-badge :category="OrganCategory::ActionMechanical" :newTab="true">mechanické traktury</x-organomania.category-badge> a <x-organomania.category-badge :category="OrganCategory::WindchestSchleif" :newTab="true">zásuvkové vzdušnice</x-organomania.category-badge>). Začínají však upřednostňovat temnější zvuk, docílený častým disponováním rejstříků v nižších polohách, což varhany přibližuje ke zvukovému ideálu symfonického orchestru.
                        Již od dob pozdního baroka panuje obliba smykavých rejstříků, které přímo napodobují smyčcové nástroje.
                        Postupně se rozšiřuje tónový rozsah manuálů i pedálu, naopak se ale přestává stavět zadní pozitiv v zábradlí kůru (varhanní stroj pozitivu je vsazen do hlavní skříně).
                        Zdobnost varhanních skříní se viditelně zmenšuje, mizí půdorysné zvlnění.
                    </p>
                    
                    <div class="text-center mb-4 mx-auto" style="width: 500px; max-width: 100%">
                        <div class="position-relative d-inline-block">
                            <img class="rounded mb-2" src="/images/rychnov.jpg" style="max-width: 100%" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_RYCHNOV_NAD_KNEZNOU_ZAMECKY_KOSTEL]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" /> &ndash; jedna z prvních neogotických skříní u nás
                            </em>
                        </div>
                    </div>

                    <h5 id="builtFrom1800To1859OrganBuilders">Varhanáři a varhany</h5>

                    <p>
                        Nejhodnotnějším dochovaným nástrojem tohoto období jsou varhany <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_RYCHNOV_NAD_KNEZNOU_ZAMECKY_KOSTEL]" name="zámeckého kostela Nejsvětější Trojice v Rychnově nad Kněžnou" :showSizeInfo="true" /> od <x-organomania.organ-builder-link :iconLink="false" name="Jiřího ŠPANĚLA ml." :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JIRI_SPANEL]" :showActivePeriod="true" :showMunicipality="true" />.
                    </p>
                    
                    <p>
                        Z varhanářů působících v Čechách uveďme <x-organomania.organ-builder-link :iconLink="false" name="Josefa GARTNERA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_GARTNEROVE]" activePeriod="1796–1863" :showActivePeriod="true" :showMunicipality="true" />, který pečoval o pražské barokní varhany nebo velmi schopného <x-organomania.organ-builder-link :iconLink="false" name="Josefa PREDIGERA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JOSEF_PREDIGER]" :showActivePeriod="true" :showMunicipality="true" />. Predigerův majestátní nástroj v <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_CYRILA_A_METODEJE_KARLIN]" name="kostele sv. Cyrila a Metoděje v Praze-Karlíně" size="II/34" year="1863" :showSizeInfo="true" /> byl bohužel zásadně přestavěn. Ocenit nicméně můžeme jeho dvoumanuálové nástroje, např. varhany v <x-organomania.organ-link :iconLink="false" name="kostele Naštívení Panny Marie v Bozkově" :organ="$organs[Organ::ORGAN_ID_BOZKOV]" :showSizeInfo="true" />.
                    </p>
                    
                    <p>
                        Plodnými moravskými varhanáři tohoto období byli <x-organomania.organ-builder-link :iconLink="false" name="Franz HARBICH" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_FRANZ_HARBICH]" :showActivePeriod="true" :showMunicipality="true" /> nebo <x-organomania.organ-builder-link :iconLink="false" name="Johann NEUSSER" activePeriod="1807–1878" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_NEUSSEROVE]" :showActivePeriod="true" :showMunicipality="true" />.
                        Schopným varhanářem samoukem byl <x-organomania.organ-builder-link :iconLink="false" name="František SVÍTIL" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_FRANTISEK_SVITIL]" :showActivePeriod="true" :showMunicipality="true" />.
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Tryptofan, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="/images/bozkov.jpg" style="width: 460px; max-width: 100%" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_BOZKOV]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                            </em>
                        </div>
                    </div>


                    <x-organomania.about-organ-h4 id="builtFrom1860To1944" subtitle="varhanní romantismus a tovární výroba" :organBuilderCategory="OrganBuilderCategory::BuiltFrom1860To1944" :organCategory="OrganCategory::BuiltFrom1860To1944">
                        Varhanářství v letech 1860–1944
                    </x-organomania.about-organ-h4>

                    <p>
                        S postupující průmyslovou revolucí se prosazuje výroba varhan <em>továrním způsobem</em>.
                        Vznikají velké varhanářské podniky, které staví varhany rychleji a levněji než dřívější rodinné dílny.
                        Firemní katalogy nabízí zákazníkům unifikované typy varhan, které tak již nevznikají jako nástroje postavené na míru pro konkrétní prostor.
                        Unifikace se týká varhanního stroje i návrhu skříní.
                        Tento přístup často vede k potlačení umělecké individuality nástrojů nebo i ke ztrátě jejich zvukových kvalit.
                        Na druhou stranu díky tovární výrobě vzniká nebývalé množství nástrojů, včetně velkých třímanuálových.
                    </p>
                    

                    <h5 id="builtFrom1860To1944Sound">Zvuková podstata varhan</h5>

                    <div class="ms-md-3 mt-3 mt-md-0 mb-2 float-md-end mx-auto text-center" style="width: 340px; max-width: 100%;">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Bartfloete, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2 w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Phillipsdorf-Orgel.jpg/542px-Phillipsdorf-Orgel.jpg" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_FILIPOV]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                                &ndash; neorománská skříň
                            </em>
                        </div>
                    </div>
                    
                    <p>
                        V této době už můžeme mluvit o plně se rozvíjejícím <em>varhanním romantismu</em>.
                        V dispozicích varhan nacházíme velké množství hlubších (osmistopých a šestnáctistopých) rejstříků, <x-organomania.category-badge :category="RegisterCategory::String" :newTab="true">smykavých hlasů</x-organomania.category-badge> (napodobujících smyčcové nástroje), <x-organomania.category-badge :category="RegisterCategory::Prefukujici" :newTab="true">přefukujících fléten</x-organomania.category-badge> <span class="text-nowrap">(např. <x-organomania.register-name-link :registerName="$registerNames[RegisterName::REGISTER_NAME_ID_FLETNA_HARMONICKA]" :showCategory="false" :newTab="true" />)</span>, <x-organomania.category-badge :category="RegisterCategory::Vychvevne" :newTab="true">výchvěvných rejstříků</x-organomania.category-badge> <span class="text-nowrap">(např.  <x-organomania.register-name-link :registerName="$registerNames[RegisterName::REGISTER_NAME_ID_VOX_COELESTIS]" :showCategory="false" :newTab="true" />)</span> a <x-organomania.category-badge :category="RegisterCategory::JazykovePrurazne" :newTab="true">průrazných jazykových rejstříků</x-organomania.category-badge> <span class="text-nowrap">(např. <x-organomania.register-name-link :registerName="$registerNames[RegisterName::REGISTER_NAME_ID_KLARINET]" :showCategory="false" :newTab="true" />)</span>. Jemným romantickým typem mixtury je rejstřík &nbsp;<x-organomania.register-name-link :registerName="$registerNames[RegisterName::REGISTER_NAME_ID_HARMONIA_AETHEREA]" :showCategory="false" :newTab="true" />.
                    </p>
                    
                    <p class="mb-0">
                        Tento trend ve vývoji dispozic posiluje <em>řezenská ceciliánská reforma</em>. Ta se snaží o očištění církevní hudby od světských vlivů a varhany chápe jen jako doprovodný nástroj.
                        Jako první byly u nás v duchu této reformy postaveny dva nástroje od firmy <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_STEINMEYER]" />:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_BRNO_STARE_BRNO]" size="II/24" :year="1876" :showSizeInfo="true" :iconLink="false" /> &ndash; byly nahrazeny novým nástrojem</li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_VOJTECHA]" :showSizeInfo="true" :iconLink="false" /> &ndash; dochovaly se dodnes</li>
                    </ul>

                    <p>
                        U romantických varhan také klesá zvuková samostatnost jednotlivých manuálů.
                        Zvukovou korunu (mixturu) obsahuje často jen hlavní manuál.
                        Velké množství hlubokých osmistopých rejstříků je disponováno na úkor výstavby vyšších a <x-organomania.category-badge :category="RegisterCategory::Alikvotni" :newTab="true">alikvotních hlasů</x-organomania.category-badge> (hlasů znějících v jiné než oktávové poloze).
                        V duchu orchestrálního cítění jsou manuály vzájemně dynamicky odstupňovány.
                        Namísto tradiční polyfonie se zvuk varhan přizpůsobuje homofonní akordické hře, pro kterou je výhodná <em>kuželková vzdušnice</em> (viz níže).
                        Zvuk starých barokních varhan neodpovídá soudobému cítění (je považován za příliš křiklavý) a historické nástroje jsou často přestavovány.
                    </p>

                    <h5 id="builtFrom1860To1944Construction">Konstrukce varhan</h5>

                    <p>
                        Na rozdíl od předchozího období už v oblasti konstrukce varhan dochází k výraznému odklonu od principů barokního varhanářství.
                        Nejprve se prosazuje <x-organomania.category-badge :category="OrganCategory::WindchestKegel" :newTab="true">kuželková vzdušnice</x-organomania.category-badge> jako náhrada tradiční zásuvkové.
                        Fyzická namáhavost hry na čím dál rozsáhlejších nástrojích je řešena postupným zaváděním <x-organomania.category-badge :category="OrganCategory::ActionPneumatical" :newTab="true">pneumatické traktury</x-organomania.category-badge>.
                        Ta však s sebou často nese i problém opožděného ozevu tónů a vyšší poruchovosti.
                        Určitého zlepšení rychlosti ozevu dociluje <x-organomania.category-badge :category="OrganCategory::WindchestMembran" :newTab="true">membránová vzdušnice</x-organomania.category-badge> a výpustný systém.
                        Jen u menší části nástrojů se používá sofistikované řešení pomocí tzv. <x-organomania.category-badge :category="OrganCategory::ActionBarker" :newTab="true">Barkerovy páky</x-organomania.category-badge>, která dostatečně ulehčuje hru, ale nevede přitom ke zpožděnému ozevu.
                        Toto řešení je typické především pro francouzské varhanářství.
                        Pneumatická traktura a nové typy vzdušnic se prosazují mimojiné i proto, že lépe vyhovují soudobému továrnímu způsobu výroby varhan.
                    </p>

                    <p>
                        Až v pozdějším období se začíná ojediněle používat <x-organomania.category-badge :category="OrganCategory::ActionElectrical" :newTab="true">elektrická traktura</x-organomania.category-badge> &ndash; známým příkladem jsou velmi hodnotné varhany <x-organomania.organ-link :iconLink="false" name="Obecního domu v Praze" :organ="$organs[Organ::ORGAN_ID_PRAHA_OBECNI_DUM]" :showOrganBuilder="true" :showSizeInfo="true" />.
                    </p>
                    
                    <p>
                        Nové typy vzdušnic a traktur umožňují rozvoj hracího stolu a různých pomocných zařízení.
                        Jde především o různé druhy běžných i oktávových spojek, o pevné a volné rejstříkové kombinace, rejstříkové crescendo a další.
                        Neodmyslitelnou výbavou varhan se stává žaluziové crescendo.
                        Varhany nacházejí své místo i ve světském prostředí koncertních síní (viz např. opět varhany Obecního domu).
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: © Jorge Royan / http://www.royan.com.ar">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Smetana_Hall_at_the_Municipal_House_%28Obecni_Dum%29%2C_Prague_-_8973.jpg/640px-Smetana_Hall_at_the_Municipal_House_%28Obecni_Dum%29%2C_Prague_-_8973.jpg" style="width: 550px; max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_OBECNI_DUM]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" /> &ndash; secesní skříň
                            </em>
                        </div>
                    </div>

                    <div class="alert alert-info p-2 terms mx-auto">
                        <h6><i class="bi bi-info-circle"></i> Důležité pojmy</h6>
                        <dl class="row mb-0 small">
                            @foreach ([
                                OrganCategory::ActionPneumatical, OrganCategory::ActionBarker, OrganCategory::ActionElectrical,
                                OrganCategory::WindchestKegel, OrganCategory::WindchestMembran,
                            ] as $category)
                                <dt class="col-md-3"><x-organomania.category-badge :category="$category" :newTab="true" /></dt>
                                <dd @class(['col-md-9', 'mb-0' => $loop->last])>{{ $category->getDescription() }}</dd>
                            @endforeach
                        </dl>
                    </div>

                    <p>
                        Behem 1. světové války varhany trpí rekvírováním velkých kovových píšťal pro válečnou výrobu.
                        Od roku 1914 probíhá soupis hodnotných nástrojů v rámci tzv. <em>Katastru varhan pro Čechy</em>.
                        Jedná se o jednu z prvních katalogizací varhan u nás.
                    </p>
                    
                    <p>
                        Při návrhu varhanních skříní se nově uplatňuje neogotický, neorománský, neorenesanční a později i secesní sloh.
                        Od období mezi válkami se rozšiřuje použití zjednodušených funkcionalistických skříní.
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Wolfgang Sauber, CC BY-SA 3.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/St.Veit_-_Orgel.jpg/640px-St.Veit_-_Orgel.jpg" style="width: 550px; max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_CESKY_KRUMLOV_KOSTEL_SV_VITA]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" /> &ndash; neogotická skříň
                            </em>
                        </div>
                    </div>

                    <h5 id="builtFrom1860To1944OrganBuilders">Varhanáři a varhany</h5>

                    <p>
                        Výčet nejvýznamnějších varhanářských podniků do r. 1945 je přibližně seřazen podle významnosti a doplněn příklady zachovalých reprezentativních nástrojů.
                        Konstrukční stránka varhan se u většiny firem vyvíjela &ndash; v počátcích stavěly obvykle ještě mechanickou trakturu a až později přešly na různé varianty pneumatických varhan.
                    </p>
                    <ul class="items-list">
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_RIEGER]" :showActivePeriod="true" :showMunicipality="true" />
                            &ndash; naše největší a světově nejproslulejší varhanářská firma
                            <ul>
                                <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_OLOMOUC_KATEDRALA_SV_VACLAVA]" :showSizeInfo="true" /></li>
                            </ul>
                        </li>
                        <li>
                            firmy sídlící v Praze
                            <ul>
                                <li>
                                    <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_EMANUEL_STEPAN_PETR]" :showActivePeriod="true" />
                                    <ul>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_LUDMILY]" :showSizeInfo="true" /></li>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_CYRILA_A_METODEJE_KARLIN]" :showSizeInfo="true" /> &ndash; jedny z největších romantických varhan u nás</li>
                                    </ul>
                                </li>
                                <li>
                                    <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_BRATRI_PASTIKOVE]" :showActivePeriod="true" />
                                    <ul>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_PETRA_A_PAVLA_VYSEHRAD]" :showSizeInfo="true" /></li>
                                    </ul>
                                </li>
                                <li>
                                    <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JINDRICH_SCHIFFNER]" :showActivePeriod="true" />
                                    <ul>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_CESKY_KRUMLOV_KOSTEL_SV_VITA]" :showSizeInfo="true" /></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            firmy sídlící v Kutné Hoře
                            <ul>
                                <li>
                                    <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JAN_TUCEK]" :showActivePeriod="true" />
                                    <ul>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_NYMBURK_KOSTEL_SV_JILJI]" :showSizeInfo="true" /></li>
                                    </ul>
                                </li>
                                <li>
                                    <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JOSEF_MELZER]" :showActivePeriod="true" />
                                    <ul>
                                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_WOHLMUTOVA_KRUCHTA]" :showSizeInfo="true" /></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_BRATRI_BRAUNEROVE]" :showActivePeriod="true" :showMunicipality="true" />
                            <ul>
                                <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PLZEN_VELKA_SYNAGOGA]" :showSizeInfo="true" /></li>
                            </ul>
                        </li>
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_MARTIN_ZAUS]" :showActivePeriod="true" :showMunicipality="true" />
                            <ul>
                                <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_CHEB_KOSTEL_SV_MIKULASE]" :showSizeInfo="true" /></li>
                            </ul>
                        </li>
                        <li>
                            <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_NEUSSEROVE]" name="NEUSSER, Karl" activePeriod="1844–1925" :showActivePeriod="true" :showMunicipality="true" />
                        </li>
                    </ul>

                    <p class="mb-0">
                        Na našem území působí někdy i zahraniční (zejm. německé) firmy, např.:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_A_SCHUSTER_UND_SOHN]" :showMunicipality="true" /></li>
                        <li><x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_JEHMLICH]" :showMunicipality="true" /></li>
                    </ul>

                    <p class="mb-0">
                        V období první republiky se ve varhanářství (v souladu se zahraničními trendy) pozvolna začínají prosazovat zásady tzv. <em>varhanního hnutí</em>, ačkoli zatím jen při návrhu rejstříkových dispozic.
                        (Podstata varhanního hnutí je popsána v následující kapitole.)
                        Nové vlivy lze pozorovat zejména na nástrojích postavených firmou <em>Rieger</em> a zahraničními podniky.
                        Níže uvádíme příklady nejznámějších nástrojů:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_LUDGEROVICE]" :showSizeInfo="true" :showOrganBuilder="true" /></li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_SLUKNOV]" :showSizeInfo="true" :showOrganBuilder="true" /></li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_VELKE_VARHANY]" :showSizeInfo="true" :showOrganBuilder="true" /></li>
                    </ul>
                    
                    <div class="text-center mb-4 mx-auto"style="width: 550px; max-width: 100%;">
                        <div class="position-relative d-inline-block">
                            <img class="rounded mb-2" src="/images/kutna-hora-svaty-jakub-stul.jpg" style="max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_KUTNA_HORA_SV_JAKUB_VELKE_VARHANY]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                                &ndash; hrací stůl s řadou pomocných zařízení
                            </em>
                        </div>
                    </div>


                    <x-organomania.about-organ-h4 id="builtFrom1945To1989" subtitle="varhanní hnutí za železnou oponou" :organBuilderCategory="OrganBuilderCategory::BuiltFrom1945To1989" :organCategory="OrganCategory::BuiltFrom1945To1989">
                        Varhanářství v letech 1945–1989
                    </x-organomania.about-organ-h4>

                    <div class="ms-md-3 mb-2 float-md-end mx-auto text-center" style="width: 295px; max-width: 100%;">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Petr.lhotan, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2 w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Presbyt%C3%A1%C5%99_a_hlavn%C3%AD_lo%C4%8F_kostela_sv._Ducha_v_Krnov%C4%9B.JPG/512px-Presbyt%C3%A1%C5%99_a_hlavn%C3%AD_lo%C4%8F_kostela_sv._Ducha_v_Krnov%C4%9B.JPG" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_KRNOV_KOSTEL_SV_DUCHA]" :year="false" :showSizeInfo="true" class="stretched-link" />
                            </em>
                        </div>
                    </div>
                    
                    <p>
                        Po komunistickém převratu se mohou varhanářství věnovat až na výjimky pouze velké státní podniky a varhanářská družstva.
                        Jednoznačně nejvýznamnějším výrobcem je krnovský závod <em>Rieger</em>, poznamenaný odsunem německých pracovníků a fungující nově pod názvem <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_RIEGER_KLOSS]" />.
                        Právě tento podnik staví nejvíce nových varhan a je průkopníkem v oblasti konstrukčního vývoje nástroje.
                        I v tomto období vzniká mnoho velkých nástrojů, a to jak v koncertních sálech, tak v kostelích, což vzhledem k nepřátelskému vztahu režimu vůči církvím působí až paradoxně.
                        Rieger-Kloss varhany exportuje do řady zemí, zejména ve východním bloku.
                        Velké varhany staví ještě kutnohorské družstvo <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_ORGANA]" />.
                        Varhanářská tvorba je v prvních poválečných letech značně poznamenána nedostatkem kvalitních materiálů.
                    </p>

                    <p>
                        Stylově již varhanářství tohoto období vesměs upouští od romantických dispozičních zásad a přiklání se k myšlenkám tzv. <em>varhanního hnutí</em> (Orgelbewegung).
                        Tento směr, jehož čelní osobností byl <em>Albert Schweitzer</em> (1875&ndash;1965), prosazoval od 20. let 20. století návrat k zákonitostem barokního varhanářství (reprezentovaného zejména velkými severoněmeckými nástroji) a varhany předchozí romantické epochy považoval za úpadkové.
                        Rejstříkové dispozice varhan firmy <em>Rieger-Kloss</em> se (čerpajíce již z dřívějšího odkazu firmy) varhannímu hnutí rychle přizpůsobují &ndash; ve všech manuálech nově stavěných varhan je vybudována zvuková pyramida až po vysoké alikvotní rejstříky a mixtury.
                        Po stránce konstrukční však ještě ze začátku převládá <x-organomania.category-badge :category="OrganCategory::ActionPneumatical" :newTab="true">pneumatická</x-organomania.category-badge> (eventuálně <x-organomania.category-badge :category="OrganCategory::ActionElectrical" :newTab="true">elektrická</x-organomania.category-badge>) traktura a <x-organomania.category-badge :category="OrganCategory::WindchestKegel" :newTab="true">kuželkové vzdušnice</x-organomania.category-badge>, charakteristické pro předchozí období.
                        Až od 60. let rozvíjí Rieger-Kloss konstrukci tradiční <x-organomania.category-badge :category="OrganCategory::ActionMechanical" :newTab="true">mechanické traktury</x-organomania.category-badge> a <x-organomania.category-badge :category="OrganCategory::WindchestSchleif" :newTab="true">zásuvkových vzdušnic</x-organomania.category-badge>.
                        Běžné jsou také různé kombinace traktur (typicky traktura <em>elektropneumatická</em>) a použití odlišného typu traktury pro ovládání rejstříků (např. použití elektrické rejstříkové traktury u nástrojů s mechanickou tónovou trakturou, což usnadňuje ovládání hracího stolu).
                    </p>

                    <p>
                        Syntézou neobarokních dispozičních zásad s rejstříky romantického období vzniká stylově nevyhraněný (<em>univerzální</em>) typ varhan, umožňující interpretaci skladeb všech stylových období.
                        Tyto nástroje bývají nicméně někdy kritizovány jako nedostatečně charakterní.
                    </p>

                    <p class="mb-0">
                        Z varhan postavených v tomto období zmíníme výhradně velké koncertní nástroje firmy Rieger-Kloss:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE]" :showSizeInfo="true" :year="1968" :isRebuild="true" :iconLink="false" /> &ndash; rozšíření barokního nástroje Michaela Englera</li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_JAKUBA_VETSIHO]" :showSizeInfo="true" :year="1982" :isRebuild="true" :iconLink="false" /> &ndash; přestavba koncertního nástroje na čtyřmanuálový</li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRAHA_RUDOLFINUM]" :showSizeInfo="true" :iconLink="false" /> &ndash; velký koncertní mechanický nástroj</li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_BRNO_KOSTEL_SV_AUGUSTINA]" :showSizeInfo="true" :iconLink="false"z /> &ndash; o něco menší mechanický nástroj</li>
                    </ul>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img class="rounded mb-2" src="/images/olomouc-sv-moric.jpg" style="width: 540px; max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE]" :year="1968" :showSizeInfo="true" :iconLink="false" class="stretched-link" />
                                &ndash; hrací stůl firmy Rieger-Kloss
                            </em>
                        </div>
                    </div>


                    <x-organomania.about-organ-h4 id="builtFrom1990" subtitle="nové směry vývoje" :organBuilderCategory="OrganBuilderCategory::BuiltFrom1990" :organCategory="OrganCategory::BuiltFrom1990">
                        Varhanářství od roku 1990
                    </x-organomania.about-organ-h4>

                    <p>
                        Největší varhanářský závod Rieger-Kloss prochází po r. 1990 procesem ekonomické transformace a nastává postupný útlum ve výrobě.
                        Řada nejen krnovských varhanářů nahlíží na tovární způsob výroby varhan kriticky, zakládá své vlastní dílny a v protikladu k předchozímu období staví často menší a stylově vyhraněné nástroje.
                        V řadě případů (ale ne vždy) se tyto nástroje inspirují tradicemi barokního varhanářství.
                        U větších varhan naopak přichází ke slovu elektronika, rejstříkové volné kombinace typu <em>Setzer</em>, někdy dokonce digitálně reprodukované rejstříky.
                    </p>

                    <p class="mb-0">
                        K renomovaným stavitelům nových nástrojů patří především firma <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_KANSKY_BRACHTL]" :showMunicipality="true" /> a dílna <x-organomania.organ-builder-link :iconLink="false" name="Vladimíra ŠLAJCHA" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_VLADIMIR_SLAJCH]" :showMunicipality="true" />. Z významnějších nástrojů vybíráme tyto:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MARKETY_BREVNOV]" :showOrganBuilder="true" :showSizeInfo="true" :iconLink="false" /></li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_PRIBRAM_SVATA_HORA]" :showOrganBuilder="true" :showSizeInfo="true" :iconLink="false" /></li>
                        <li><x-organomania.organ-link :organ="$organs[Organ::ORGAN_ID_KOLIN_KOSTEL_SV_BARTOLOMEJE]" :showSizeInfo="true" :iconLink="false" /> &ndash; postavily společně firmy <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_KANSKY_BRACHTL]" :showMunicipality="true" /> a <x-organomania.organ-builder-link :iconLink="false" :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_DLABAL_METTLER]" :showMunicipality="true" /></li>
                    </ul>

                    <p class="mb-0">
                        Zajímavé nástroje staví na našem území i zahraniční firmy, např.:
                    </p>
                    <ul class="items-list">
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_SV_SALVATORA]" :showOrganBuilder="true" :showSizeInfo="true" /></li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_BRNO_JEZUITSKY_KOSTEL_NANEBEVZETI_PANNY_MARIE]" :showOrganBuilder="true" :showSizeInfo="true" /></li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KAROLINUM]" :showOrganBuilder="true" :showSizeInfo="true" /></li>
                        <li><x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_ZAPADNI_KRUCHTA]" :showOrganBuilder="true" :showSizeInfo="true" /></li>
                    </ul>

                    <p>
                        Ve svobodných poměrech se rozvíjí využití varhan jako koncertního nástroje.
                        Zatímco v období komunismu byly koncerty v kostelích povoleny jen na několika vybraných místech, po změně režimu vzniká řada varhanních festivalů (viz sekce <a class="icon-link icon-link-hover align-items-start text-decoration-none" href="{{ route('festivals.index') }}" target="_blank"><i class="bi bi-calendar-date"></i> Festivaly</a>).
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Václav Štorek, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Basilica_of_the_Assumption_of_the_Virgin_Mary_in_P%C5%99%C3%ADbram_23.jpg/640px-Basilica_of_the_Assumption_of_the_Virgin_Mary_in_P%C5%99%C3%ADbram_23.jpg" style="width: 500px; max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRIBRAM_SVATA_HORA]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                            </em>
                        </div>
                    </div>

                    <h5 id="builtFrom1990Renovation">Restaurování historických nástrojů</h5>

                    <p>
                        Po letech chátrání cenných historických varhan se konečně otevírá prostor pro jejich poučené restaurování.
                        Domácí varhanářství, které se dosud obnově historických nástrojů věnovalo jen okrajově, čerpá cenné poznatky o restaurátorských postupech běžných v západní Evropě.
                        Zavádí se lepší památková ochrana varhan, která již dbá nejen o výtvarnou, ale i o zvukovou složku varhan.
                        Dochází k přehodnocení dřívějšího plošně negativního náhledu na romantické varhanářství &ndash; kvality řady romantických nástrojů jsou doceněny a upřednostňuje se jejich restaurování do původní podoby.
                    </p>
                    
                    <p>
                        Řada varhan, především v méně navštěvovaných a odlehlých kostelích, nicméně nadále zůstává v neutěšeném stavu.
                        V některých případech jsou napadeny červotočem a hrozí jejich zánik.
                    </p>

                    <p class="mb-0">
                        K nejvýznamnějším restaurátorským počinům patří:
                    </p>
                    <ul class="items-list">
                        @foreach ($renovatedOrgans as $organ)
                            <li>
                                <x-organomania.organ-link :organ="$organ" :showOrganBuilder="true" :showSizeInfo="true" />
                                <ul>
                                    <li>
                                        restaurování:
                                        @if ($organ->id === Organ::ORGAN_ID_POLNA_KOSTEL_NANEBEVZETI_PANNY_MARIE_VELKE_VARHANY)
                                            <x-organomania.organ-builder-link :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_DLABAL_METTLER]" :yearBuilt="$organ->year_renovated" :iconLink="false" />,
                                            <x-organomania.organ-builder-link :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_DALIBOR_MICHEK]" :yearBuilt="$organ->year_renovated" :iconLink="false" />,
                                            <x-organomania.organ-builder-link :organBuilder="$organBuilders[OrganBuilder::ORGAN_BUILDER_ID_MAREK_VORLICEK]" :yearBuilt="$organ->year_renovated" :iconLink="false" />
                                        @else
                                            <x-organomania.organ-builder-link :organBuilder="$organ->renovationOrganBuilder" :yearBuilt="$organ->year_renovated" :iconLink="false" />
                                        @endif
                                    </li>
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                    
                    <p>
                        Můžete si také zobrazit <a class="text-decoration-none" href="{{ route('organ-builders.index', ['filterCategories' => [OrganBuilderCategory::Restoration]]) }}" target="_blank">přehled varhanářů věnujících se restaurování</a> (přehled není úplný).
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block" title="Licence obrázku: Ricardalovesmonuments, CC BY-SA 4.0, via Wikimedia Commons">
                            <img class="rounded mb-2" src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Teynkirche_Prag_Orgel.jpg/560px-Teynkirche_Prag_Orgel.jpg" style="width: 350px; max-width: 100%;" />
                            <br />
                            <em>
                                <x-organomania.organ-link :iconLink="false" :organ="$organs[Organ::ORGAN_ID_PRAHA_KOSTEL_MATKY_BOZI_PRED_TYNEM]" :showOrganBuilder="true" :showSizeInfo="true" class="stretched-link" />
                            </em>
                        </div>
                    </div>


                    <h4 id="conclusion">Závěr</h4>

                    <p>
                        Z předchozího pojednání o varhanářství v českých zemích je patrné, že každé stylové období se většinou vymezuje vůči tomu předchozímu.
                        Moderní náhled na varhany se naštěstí snaží docenit všechny kvalitní nástroje, bez ohledu na období jejich vzniku.
                        Konstrukční a stylová rozmanitost varhan klade velké nároky na varhanáře a varhaníky, kteří musí své nástroje důkladně pochopit.
                        Zároveň však přináší o to větší potěšení všem, kdo varhany poslouchají a objevují jejich krásu &ndash; ještě i dnes, stovky let poté, co je jejich tvůrce postavil.
                    </p>
                </div>
                
                <hr>
                <a class="link-primary text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#referencesModal">
                    <small>{{ __('Použitá literatura') }}</small>
                </a>
            </div>
        </div>

        <x-organomania.modals.references-modal>
            <h6>{{ __('Varhany') }}</h6>
            <x-organomania.link-list class="mb-3">
                <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/organ-v-kulture-dvoch-tisicroci-294751">
                    Ferdinand Klinda: Organ v kultúre dvoch tisícročí
                    <x-slot:description>KLINDA, Ferdinand. Organ v kultúre dvoch tisícročí. Bratislava : Hudobné centrum, 2000. ISBN 8088884195.</x-slot>
                </x-organomania.link-list-item>
                
                <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/prehled-knihy/dejiny-varhan-a-varhanni-hudby-497787">
                    Milan Šlechta: Dějiny varhan a varhanní hudby
                    <x-slot:description>ŠLECHTA, Milan. Dějiny varhan a varhanní hudby. 2. vyd. přeprac. Praha, 1985.</x-slot>
                </x-organomania.link-list-item>
                
                <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/varhany-a-jejich-osudy-81909">
                    Jan Tomíček: Varhany a jejich osudy
                    <x-slot:description>TOMÍČEK, Jan. Varhany a jejich osudy. [Praha]: PM vydavatelství, 2010. ISBN 978-80-900808-2-9.</x-slot>
                </x-organomania.link-list-item>
                
                <x-organomania.link-list-item icon="book" url="https://www.cbdb.cz/kniha-255158-barokni-varhanarstvi-na-morave-dil-1-varhanari">
                    Jiří Sehnal: Barokní varhanářství na Moravě - I. Varhanáři
                    <x-slot:description>SEHNAL, Jiří. Barokní varhanářství na Moravě. Prameny k dějinám a kultuře Moravy. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. ISBN 80-7275-042-9.</x-slot>
                </x-organomania.link-list-item>
                
                <x-organomania.link-list-item icon="book" url="https://theses.cz/id/ev7b8g/">
                    Petr Lyko: Varhanářská firma Rieger
                    <x-slot:description>LYKO, Petr. Varhanářská firma Rieger. Online. Disertační práce. Ostrava: Ostravská univerzita, Pedagogická fakulta. 2018. Dostupné z: https://theses.cz/id/ev7b8g/.</x-slot>
                </x-organomania.link-list-item>
            </x-organomania.link-list>
            
            <h6>{{ __('Varhanní skříně') }}</h6>
            <x-organomania.link-list class="mb-3">
                <x-organomania.link-list-item icon="book" url="https://www.databazeknih.cz/knihy/vytvarny-vyvoj-varhannich-skrini-v-cechach-397826">
                    Jiří Belis: Výtvarný vývoj varhanních skříní v Čechách
                    <x-slot:description>BELIS, Jiří. Výtvarný vývoj varhanních skříní v Čechách. Praha, 1988.</x-slot>
                </x-organomania.link-list-item>
                
                <x-organomania.link-list-item icon="book" url="https://theses.cz/id/ugwhfa/">
                    Petra Novotná: Sochařská a řezbářská výzdoba barokních varhan v 18. století v Olomoucké arcidiecézi se zaměřením na děkanát Šumperk
                    <x-slot:description>NOVOTNÁ, Petra. Sochařská a řezbářská výzdoba barokních varhan v 18. století v Olomoucké arcidiecézi se zaměřením na děkanát Šumperk. Online. Diplomová práce. Olomouc: Univerzita Palackého v Olomouci, Filozofická fakulta. 2009. Dostupné z: https://theses.cz/id/ugwhfa/.</x-slot>
                </x-organomania.link-list-item>
            </x-organomania.link-list>
            
            <h6>{{ __('Další') }}</h6>
            <x-organomania.link-list>
                <x-organomania.link-list-item icon="book" url="https://www.npu.cz/cs/e-shop/7397-pece-o-varhany-a-zvony-jejich-pamatkova-ochrana">
                    Petr Koukal: Péče o varhany a zvony, jejich památková ochrana
                    <x-slot:description>KOUKAL, Petr. Péče o varhany a zvony, jejich památková ochrana. Odborné a metodické publikace. Praha: Národní památkový ústav, ústřední pracoviště, 2006. ISBN 80-86234-88-6.</x-slot>
                </x-organomania.link-list-item>
            </x-organomania.link-list>
        </x-organomania.modals.references-modal>
    </div>
    
    <script type="module">
        function initNavigation() {
            // při kliknutí položku menu odrolovat kousek zpět, aby nadpis nepřekrývala hlavička stránky
            let offset = 75
            $('.nav-link').on('click', function (e) {
                e.preventDefault()
                let href = $(e.target).attr('href')
                $(href)[0].scrollIntoView({ behavior: 'instant' })
                scrollBy(0, -offset)
                window.location.hash = href
            })
        }
            
        function initScrollSpy() {
            bootstrap.ScrollSpy.getOrCreateInstance($('#article'))
        }
        
        initNavigation()
        initScrollSpy()
    </script>
</x-app-bootstrap-layout>
