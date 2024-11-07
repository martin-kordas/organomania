<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganBuilder;
use App\Enums\OrganBuilderCategory;
use App\Enums\Region;

class OrganBuilderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertOrganBuilder(
            data: [
                'id' => 1,
                'is_workshop' => true,
                'workshop_name' => 'Rieger',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1873-1945',
                'active_from_year' => 1873,
                'municipality' => 'Krnov',
                'latitude' => 50.0896686,
                'longitude' => 17.7038469,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 10,
                'varhany_net_id' => 143666,
                'description' => 'Naše největší, nejproduktivnější a světově nejznámější varhanářská firma vznikla poté, co bratři Otto a Gustav převzali krnovskou varhanářskou dílnu po svém otci. Díky studiím v Německu přinesli do zaostávajícího domácího varhanářství moderní prvky. Velký úspěch sklidil jejich nástroj již na světové výstavě v Paříži v r. 1878. Zpočátku stavěli varhany s mechanickou trakturu, případně s využitím Barkerovy páky. Od r. 1895 začínají experimenty s pneumatickou trakturou. Relativně dlouhou dobu trvá, než je firma obchodně zavedena i v Čechách - poměrově více zakázek získává stále na Moravě, ve Slezsku nebo v Uhrách. I proto firmu obchodně oslabí rozpad Rakousko-Uherska po 1. světové válce. V meziválečném období již firma Rieger reflektuje myšlenky tzv. varhanního hnutí, kdy dispozice jejích varhan obsahují v duchu tradic barokního varhanářství více rejstříků ve vyšších polohách, avšak při celkovém zachování původní romantické zvukovosti. Po r. 1945 dochází k odsunu dosavadních německých pracovníků závodu, kteří později zakládají nástupnický závod Rieger v rakouském Schwarzachu. Závod v Krnově je naopak sloučen s menší varhanářskou dílnou Josefa Klosse a pokračuje pod značkou Továrna na varhany (později Rieger-Kloss). Celkový počet nástrojů původní firmy Rieger, postavených do r. 1945, se pohybuje okolo 3000. Firma Rieger byla vždy proslulá vysokou kvalitou (ale též cenou) svých výrobků. Mezi významné dochované nástroje z raného období podniku, kdy byla stavěna ještě mechanická traktura, patří varhany pro katedrálu sv. Václava v Olomouci, kostel sv. Štěpána v Praze nebo kostel Vzkříšení Páně v Terezíně. Novější varhany jsou obvykle pneumatické – např. v kostele Zvěstování Panny Marie ve Šternberku, v kostele Nejsvětějšího Srdce Páně v Jablonci nad Nisou, v kostele sv. Mikuláše v Ludgeřovicích nebo v kostele Navštívení Panny Marie v Hejnicích.',
                'perex' => 'Krnovský podnik bratří Riegerů, jehož četné nástroje byly ceněny pro svou vysokou kvalitu.',
                'literature' => <<<END
                    Lyko, Petr. Varhanářská firma Rieger. Vydání první. Ostrava: Ostravská univerzita, Pedagogická fakulta, 2017. 146 stran. ISBN 978-80-7464-976-9.
                    Varhany Krnov - sborník k 100. výročí založení závodu. Krnov: Československé hudební nástroje - závod varhany, 1973. 143 s.
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::FactoryProduction,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 2,
                'is_workshop' => true,
                'workshop_name' => 'Rieger-Kloss',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1945-2018',
                'active_from_year' => 1945,
                'municipality' => 'Krnov',
                'latitude' => 50.0896686,
                'longitude' => 17.7038469,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 5,
                'varhany_net_id' => 143666,
                'description' => 'Název „Továrna na varhany“, zavedený po znárodnění firmy Rieger, je později změněn na obchodně vhodnější „Rieger-Kloss“. Od 40. let firma pokračuje v konstrukci nejběžnějších varhan s pneumatickou trakturou a kuželkovou vzdušnicí. Později se u velkých nástrojů začíná používat traktura elektrická. Od 70. let se úspěšně rozvíjí modernější typ mechanických varhan se zásuvkovou vzdušnicí, exportovaný i do západoevropských zemí. Rieger-Kloss tedy vyrábí všechny typy traktur. Navzdory proticírkevní politice režimu je firmě umožněno stavět velké množství varhan v církevních objektech. Častý je však také export velkých varhan do koncertních sálů, především ve východní Evropě. V rámci varhanářských podniků působících před r. 1989 v Československu je Rieger-Kloss nejvýznamnějším a nejkvalitnějším. Po r. 1989 dochází k útlumu ve výrobě, řada varhanářů působících v podniku se osamostatňuje. V současné době již firma neexistuje. Od r. 1945 firma postavila okolo 700 nástrojů. S působením firmy Rieger-Kloss je spjato i založení Střední umělecké školy varhanářské v Krnově. Z množství velkých nástrojů s pneumatickou nebo elektrickou trakturou postavených v církevních objektech vybíráme varhany v Olomouci (sv. Michal, Husův sbor), Opavě (sv. Vojtěch), Uherském Hradišti (sv. František Xaverský), Valašském Meziříčí (Nanebevzetí Panny Marie) nebo Zábřehu (sv. Bartoloměj). Firma postavila i řadu velkých varhan pro koncertní sály, obvykle již s mechanickou trakturou (Praha – Rudolfinum, Praha – HAMU, Brno – Besední dům, Zlín – Dům hudby, Pardubice – Sukova síň, Ostrava – Janáčkova konzervatoř, Uničov). Firma se věnovala i opravám a rozšiřování historických nástrojů. Novodobý náhled na nástroje firmy Rieger-Kloss vnímá kriticky nižší kultivovanost zvuku jednotlivých rejstříků a také stylovou nevyhraněnost (univerzálnost) rejstříkových dispozic.',
                'perex' => 'Krnovská firma navazující na tradici závodu bratří Riegerů. V období po 2. světové válce šlo o největšího výrobce varhan ve východní Evropě.',
                'literature' => <<<END
                    Lyko, Petr. Varhanářská firma Rieger. Vydání první. Ostrava: Ostravská univerzita, Pedagogická fakulta, 2017. 146 stran. ISBN 978-80-7464-976-9.
                    Varhany Krnov - sborník k 100. výročí založení závodu. Krnov: Československé hudební nástroje - závod varhany, 1973. 143 s.
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::NeobaroqueUniversal,
                OrganBuilderCategory::FactoryProduction,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 3,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Jan',
                'last_name' => 'Výmola',
                'place_of_birth' => 'Ptení',
                'place_of_death' => 'Brno',
                'active_period' => '1722-1805',
                'active_from_year' => 1722,
                'municipality' => 'Brno',
                'latitude' => 49.2002211,
                'longitude' => 16.6078411,
                'region_id' => Region::Jihomoravsky,
                'importance' => 10,
                'varhany_net_id' => 164330,
                'description' => 'Významný pozdně barokní varhanář. V Brně udržoval přátelské styky se svými současníky Antonem Richterem a Františkem Ignácem Sieberem. Po zvukové i řemeslné stránce patří jeho dílo k vrcholu moravského barokního varhanářství. Za celou jeho varhanářskou kariéru víme pouze o jednom jazykovém rejstříku, který postavil – byl to Fagot 8‘ na velkých varhanách v kostele Očištování Panny Marie v Dubu nad Moravou. Velké dvoumanuálové varhany postavil Výmola také v bazilice Panny Marie Sedmibolestné ve slovenském Šaštíně, kde však byly v r. 1951 nahrazeny zvukově zcela nepodařeným pětimanuálovým nástrojem firmy Rieger-Kloss. Nejlépe hratelné jsou Výmolovy nově restaurované dvoumanuálové varhany v kostele Povýšení sv. Kříže v Doubravníku (okr. Brno-venkov).',
                'perex' => 'Oceňovaný brněnský varhanář pozdního baroka.',
                'literature' => <<<END
                    Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (1. díl, str. 132)
                    SOBOTKA, Petr. Jan David Sieber - osobnost českého barokního varhanáře [online]. Olomouc, 2012 [cit. 2022-05-04]. Dostupné z: https://theses.cz/id/nq8oe6/. Diplomová práce. Univerzita Palackého v Olomouci, Pedagogická fakulta. Vedoucí práce Doc. MgA. Petr Planý. (str. 28)
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 4,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Jan David',
                'last_name' => 'Sieber',
                'place_of_birth' => 'Česká Lípa',
                'place_of_death' => 'Brno',
                'active_period' => '1670-1723',
                'active_from_year' => 1670,
                'municipality' => 'Brno',
                'latitude' => 49.2002211,
                'longitude' => 16.6078411,
                'region_id' => Region::Jihomoravsky,
                'importance' => 10,
                'varhany_net_id' => 152153,
                'description' => 'Nejvýznamnější osobnost moravského barokního varhanářství a zakladatel varhanářství v Brně. Varhanářství se věnoval i jeho syn, František Ignác Sieber (1716-1785). Jako jediný z moravských varhanářů stavěl i třímanuálové varhany, např. mimořádně oceňované a dodnes dochované varhany v kostele sv. Michala ve Vídni. Další třímanuálové varhany v polské Svídnici a v Brně v kostele sv. Tomáše se nedochovaly (ty svatotomášské přitom byly ve své době největšími v českých zemích). Sieber stavěl i jazykové rejstříky, z nichž se však dochovaly pouze pedálové hlasy Schnarrbass 16‘ a Trompetbass 8‘ v dobře zachovaných varhanách v kostele Nanebevzetí Panny Marie v Polné. Sieber nicméně jazyky stavěl i v manuálu, což je v pro barokní varhanářství u nás značně netypické. Z dochovaných domácích varhan uveďme ještě dvoumanuálový nástroj v bazilice Nanebevzetí Panny Marie a sv. Mikuláše ve Žďáru nad Sázavou, postavený do skříně navržené Janem Blažejem Santinim.',
                'perex' => 'Nejvýznamnější osobnost moravského barokního varhanářství a zakladatel varhanářství v Brně.',
                'literature' => <<<END
                    Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (1. díl, str. 11, 110)
                    SOBOTKA, Petr. Jan David Sieber - osobnost českého barokního varhanáře [online]. Olomouc, 2012 [cit. 2022-05-01]. Dostupné z: https://theses.cz/id/nq8oe6/. Diplomová práce. Univerzita Palackého v Olomouci, Pedagogická fakulta. Vedoucí práce Doc. MgA. Petr Planý.
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 5,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Emanuel Štěpán',
                'last_name' => 'Petr',
                'place_of_birth' => 'Opočno',
                'place_of_death' => 'Praha',
                'active_period' => '1853-1930',
                'active_from_year' => 1853,
                'municipality' => 'Praha',
                'latitude' => 50.0835494,
                'longitude' => 14.4341414,
                'region_id' => Region::Praha,
                'importance' => 10,
                'varhany_net_id' => 140345,
                'description' => 'Vedle firmy Rieger náš nejvýznamnější romantický varhanář. Byl oceňován pro kvalitu své práce, díky níž se dodnes zachovala řada jeho nástrojů, i pro ušlechtilou intonaci jednotlivých rejstříků. Absolvoval pracovní pobyt u nejvýznamnějšího francouzského varhanáře 19. století Aristida de Cavaillé-Colla. U některých varhan (např. v Praze u sv. Ludmily) používal tradiční mechanickou trakturu s Barkerovou pákou, kde je pro usnadnění hry pohyb mechanické traktury po stisku klávesy proveden ne silou hráče, ale tlakem vzduchu. U jiných varhan (např. v kostele sv. Mořice v Kroměříži) byla použita pro tehdejší období nejtypičtější pneumatická traktura. Z dalších významných nástrojů uveďme třímanuálové varhany pro katedrálu sv. Bartoloměje v Plzni nebo pro kostel sv. Cyrila a Metoděje v pražském Karlíně (obojí dochováno).',
                'perex' => 'Oceňovaný pražský varhanář, stavějící nástroje romantického typu.',
                'literature' => <<<END
                    Tomší, Lubomír et al. Historické varhany v Čechách. 1. vyd. Praha: Libri, 2000. 263 s. ISBN 80-7277-009-8. (str. 133)
                    Tomíček, Jan. Varhany a jejich osudy. [Praha]: PM vydavatelství, 2010. 310 s. ISBN 978-80-900808-2-9. (str. 196)
                    Němec, Vladimír. Pražské varhany. 1. vyd. Praha: František Novák, 1944. 349, [II] s. Naše poklady; sv. 1. (str. 232)
                    ŠON, Jiří. Emanuel Štěpán Petr v kontextu českého varhanářství [online]. Brno, 2011 [cit. 2022-05-04]. Dostupné z: https://is.jamu.cz/th/y47ke/. Bakalářská práce. Janáčkova akademie múzických umění, Hudební fakulta. Vedoucí práce Petr LYKO.
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::FactoryProduction,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 6,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'František',
                'last_name' => 'Svítil',
                'place_of_birth' => 'Nové Město na Moravě',
                'place_of_death' => 'Nové Město na Moravě',
                'active_period' => '1805-1873',
                'active_from_year' => 1805,
                'municipality' => 'Nové Město na Moravě',
                'latitude' => 49.5614431,
                'longitude' => 16.0741833,
                'region_id' => Region::Vysocina,
                'importance' => 3,
                'varhany_net_id' => 156089,
                'description' => 'Produktivní moravský varhanář 2. čtvrtiny 19. století. Působil zejména na venkově. Byl vychován jako evangelík, později přestoupil ke katolictví. Zpočátku se živil tkalcovstvím a s varhanářstvím začínal až později jako samouk, což bylo pro jeho dobu, kdy zájem o kvalitní varhanářství opadal, typické. Varhany stavěl v klasickém stylu vycházejícím z barokních tradic, snad proto, že právě ze vzorů existujících barokních varhan jako samouk vycházel. Tato konzervativnost se projevuje i v klasicistním vzhledu jeho skříní a ve stavbě pozitivu umístěného dle barokních tradic v zábradlí kůru. S úspěchem stavěl menší    jedno- a dvoumanuálové varhany. Z dodnes zachovalých jmenujme jednomanuálové nástroje v kostele sv. Václava v Trpíně (okr. Svitavy) z r. 1840 (restaurováno v r. 2008) nebo v evangelickém kostele v Olešnici na Moravě (okr. Blansko) z r. 1868, z dvoumanuálových pak varhany v kostele sv. Petra a Pavla v Batelově (okr. Jihlava). Největším Svítilovým nástrojem jsou dvoumanuálové varhany pro brněnský kostel sv. Jakuba. V tomto případě však již konstrukční složitost varhan přesahovala varhanářovy schopnosti.',
                'perex' => null,
                'literature' => <<<END
                    Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (1. díl, str. 124)
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 7,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Jan',
                'last_name' => 'Tuček',
                'place_of_birth' => 'Golčův Jeníkov',
                'place_of_death' => 'Kutná Hora',
                'active_period' => '1842-1913',
                'active_from_year' => 1842,
                'municipality' => 'Kutná Hora',
                'latitude' => 49.9483886,
                'longitude' => 15.2681647,
                'region_id' => Region::Stredocesky,
                'importance' => 3,
                'varhany_net_id' => 161255,
                'description' => 'Frekventovaná varhanářská firma konce 19. a začátku 20. století. V porovnání s dalšími varhanářskými výrobci své doby se vyznačovala nižšími cenami, ale někdy i menší kvalitou zpracování. Firma se podílela na výstavbě Voitových varhan v Obecním domě. V r. 1940 provedla první velkou přestavbu cenných barokních varhan u sv. Jakuba v Praze. Kvůli vývoji zvukového ideálu byl pak nástroj znovu přestavován firmami Organa a Rieger-Kloss. Tučkova firma postavila řadu pneumatických nástrojů, nicméně nejlépe zachované velké varhany v kostele sv. Jiljí v Nymburku byly v r. 1899 postaveny ještě s mechanickou tónovou trakturou.',
                'literature' => <<<END
                    UHLÍŘ, Václav. Specifika varhanářské firmy Jana Tučka. Online. Diplomová práce. Brno: Janáčkova akademie múzických umění, Hudební fakulta. 2023. Dostupné z: https://theses.cz/id/mgr39n/.
                    TOMŠÍ, Lubomír. Historické varhany v Čechách. Praha: Libri, 2000. ISBN 80-7277-009-8. (s. 214-222)
                    END,
                'perex' => 'Frekventovaná varhanářská firma konce 19. a začátku 20. století.',
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::FactoryProduction,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 8,
                'is_workshop' => true,
                'workshop_name' => 'Starkové',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '17.-18. stol.',
                'active_from_year' => 1650,
                'municipality' => 'Loket',
                'latitude' => 50.1860008,
                'longitude' => 12.7540467,
                'region_id' => Region::Karlovarsky,
                'importance' => 10,
                'varhany_net_id' => 155351,
                'description' => 'Dílo Abraháma Starka je považováno za vrchol barokního varhanářství u nás, po zvukové i řemeslné stránce. Dodnes se zachovaly jeho dva velké nástroje mezinárodního významu – Zlatá Koruna (okr. Český Krumlov) a Plasy (okres Plzeň-sever). Navzdory svým nadprůměrným schopnostem Stark nikdy do varhan nestavěl jazykové rejstříky. Abrahám Stark byl také zakladatelem a vůdčí osobností varhanářské tradice v Lokti. Ze zdejší varhanářské školy vzešli mj. Leopold Burkhardt (1673-1741), Jan Adam Pleyer (1686-1759), František Fassmann (1697-1760) a také Abrahámův syn Wenzel Stark (1670-1757). Důvodem pro výběr této lokality byla zřejmě těžba cínu v oblasti a dostatek dřeva v okolních lesích. Z dalších nástrojů Abraháma Starka jmenujme velké varhany u sv. Jakuba v Praze (necitlivě přestavěny ve 20. století) nebo varhany v kostele sv. Jakuba Většího v Přelouči.',
                'perex' => 'Dílo nejvýznamnějšího představitele tohoto varhanářského rodu, Abraham Starka, je považováno za vrchol barokního varhanářství u nás.',
                'workshop_members' => 'Stark, Abrahám (1659-1709); Stark, Wenzel (1670-1757)',
                'literature' => <<<END
                    Tomší, Lubomír et al. Historické varhany v Čechách. 1. vyd. Praha: Libri, 2000. 263 s. ISBN 80-7277-009-8. (str. 60)
                    END,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 9,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Michael',
                'last_name' => 'Engler',
                'place_of_birth' => 'Vratislav',
                'place_of_death' => 'Vratislav',
                'active_period' => '1688-1760',
                'active_from_year' => 1688,
                'municipality' => 'Vratislav (Slezsko)',
                'latitude' => 51.1089775,
                'longitude' => 17.0326689,
                'region_id' => null,
                'importance' => 7,
                'varhany_net_id' => 108365,
                'description' => null,
                'perex' => 'Významný slezský barokní varhanář. V našich zemích se proslavil stavbou velkých varhan pro kostel sv. Mořice v Olomouci.',
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 10,
                'is_workshop' => true,
                'workshop_name' => 'Mathis Orgelbau',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1960-nyní',
                'active_from_year' => 1960,
                'municipality' => 'Luchsingen (Švýcarsko)',
                'web' => 'https://www.mathis-orgelbau.ch/',
                'latitude' => 46.9666928,
                'longitude' => 9.0370300,
                'region_id' => null,
                'importance' => 7,
                'varhany_net_id' => 170303,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 11,
                'is_workshop' => true,
                'workshop_name' => 'Eule',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1872-nyní',
                'active_from_year' => 1872,
                'municipality' => 'Bautzen (Německo)',
                'web' => 'https://www.euleorgelbau.de/',
                'latitude' => 51.1813906,
                'longitude' => 14.4275733,
                'region_id' => null,
                'importance' => 7,
                'varhany_net_id' => 109226,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 12,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'David',
                'last_name' => 'Däcker',
                'place_of_birth' => 'Durynsko',
                'place_of_death' => null,
                'active_period' => '1583?-1641',
                'active_from_year' => 1583,
                'municipality' => 'Čechy',
                'latitude' => 0,
                'longitude' => 0,
                'region_id' => null,
                'importance' => 3,
                'varhany_net_id' => 105782,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 13,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Carl Eduard',
                'last_name' => 'Schubert',
                'place_of_birth' => 'Halsbrücke',
                'place_of_death' => 'Reichenbach im Vogtland',
                'active_period' => '1830-1900',
                'active_from_year' => 1830,
                'municipality' => 'Sasko',
                'latitude' => 50.3046394,
                'longitude' => 12.1772547,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => 170299,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 14,
                'is_workshop' => true,
                'workshop_name' => 'Jehmlich',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1808-nyní',
                'active_from_year' => 1808,
                'municipality' => 'Drážďany (Německo)',
                'web' => 'https://jehmlich-orgelbau.de/',
                'latitude' => 51.0764194,
                'longitude' => 13.7383917,
                'region_id' => null,
                'importance' => 6,
                'varhany_net_id' => 123863,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 15,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Jakob',
                'last_name' => 'Deutschmann',
                'place_of_birth' => null,
                'place_of_death' => 'Vídeň (Rakousko)',
                'active_period' => '1795-1853',
                'active_from_year' => 1795,
                'municipality' => 'Vídeň',
                'latitude' => 48.2083536,
                'longitude' => 16.3725042,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => 168552,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 16,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Johannes',
                'last_name' => 'Effnert',
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '2. polovina 17. století',
                'active_from_year' => 1650,
                'municipality' => 'Třebíč',
                'latitude' => 49.2149228,
                'longitude' => 15.8816556,
                'region_id' => Region::Vysocina,
                'importance' => 3,
                'varhany_net_id' => 168574,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 17,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Andreas',
                'last_name' => 'Wambesser',
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1720-1794',
                'active_from_year' => 1720,
                'municipality' => 'Praha',
                'latitude' => 50.0835494,
                'longitude' => 14.4341414,
                'region_id' => Region::Stredocesky,
                'importance' => 3,
                'varhany_net_id' => 169597,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 18,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Gerhard',
                'last_name' => 'Grenzing',
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1942-současnost',
                'active_from_year' => 1942,
                'municipality' => 'Barcelona',
                'latitude' => 41.3828939,
                'longitude' => 2.1774322,
                'region_id' => null,
                'web' => 'https://www.grenzing.com/',
                'importance' => 5,
                'varhany_net_id' => null,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 19,
                'is_workshop' => true,
                'workshop_name' => 'Steinmeyer',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1847-2001',
                'active_from_year' => 1847,
                'municipality' => 'Oettingen (Německo)',
                'web' => null,
                'latitude' => 48.9524578,
                'longitude' => 10.6036825,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => 154859,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 20,
                'is_workshop' => true,
                'workshop_name' => 'Voit und Söhne',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1890-1932',
                'active_from_year' => 1890,
                'municipality' => 'Durlach (Německo)',
                'web' => null,
                'latitude' => 48.9994422,
                'longitude' => 8.4702067,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => 170284,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 21,
                'is_workshop' => true,
                'workshop_name' => 'Vleugels',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1855-nyní',
                'active_from_year' => 1855,
                'municipality' => 'Hardheim (Německo)',
                'web' => 'https://vleugels.de/',
                'latitude' => 49.6101842,
                'longitude' => 9.4732442,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => null,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 22,
                'is_workshop' => true,
                'workshop_name' => 'A. Schuster & Sohn',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1870-?',
                'active_from_year' => 1870,
                'municipality' => 'Zittau (Německo)',
                'web' => null,
                'latitude' => 50.8960964,
                'longitude' => 14.8064806,
                'region_id' => null,
                'importance' => 3,
                'varhany_net_id' => 151169,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::Romantic,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 23,
                'is_workshop' => false,
                'workshop_name' => null,
                'first_name' => 'Lothar Franz',
                'last_name' => 'Walther',
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1696–1733',
                'active_from_year' => 1696,
                'municipality' => 'Vídeň (Rakousko)',
                'web' => null,
                'latitude' => 48.2083536,
                'longitude' => 16.3725042,
                'region_id' => null,
                'importance' => 2,
                'varhany_net_id' => 168992,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltTo1799,
                OrganBuilderCategory::Baroque,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 24,
                'is_workshop' => true,
                'workshop_name' => 'A.Q. Attentus Qualitatis',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '2006-současnost',
                'active_from_year' => 2000,
                'municipality' => 'Lomnice u Tišnova',
                'web' => 'https://www.a-q.cz/',
                'latitude' => 49.4046186,
                'longitude' => 16.4135897,
                'region_id' => Region::Jihomoravsky,
                'importance' => 3,
                'varhany_net_id' => 170142,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1990,
                OrganBuilderCategory::NeobaroqueUniversal,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 25,
                'is_workshop' => true,
                'workshop_name' => 'Škrabl',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1990-současnost',
                'active_from_year' => 1990,
                'municipality' => 'Rogaška Slatina (Slovinsko)',
                'web' => 'https://skrabl.com/',
                'latitude' => 46.216971,
                'longitude' => 15.6239931,
                'region_id' => null,
                'importance' => 3,
                'varhany_net_id' => 170037,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::BuiltFrom1990,
            ]
        );
        
        $this->insertOrganBuilder(
            data: [
                'id' => 26,
                'is_workshop' => true,
                'workshop_name' => 'Klais',
                'first_name' => null,
                'last_name' => null,
                'place_of_birth' => null,
                'place_of_death' => null,
                'active_period' => '1882-současnost',
                'active_from_year' => 1882,
                'municipality' => 'Bonn (Německo)',
                'web' => 'https://klais.de/',
                'latitude' => 50.7430481,
                'longitude' => 7.0924694,
                'region_id' => null,
                'importance' => 4,
                'varhany_net_id' => 169917,
                'description' => null,
                'perex' => null,
            ],
            categories: [
                OrganBuilderCategory::Romantic,
                OrganBuilderCategory::NeobaroqueUniversal,
                OrganBuilderCategory::BuiltFrom1800To1944,
                OrganBuilderCategory::BuiltFrom1945To1989,
                OrganBuilderCategory::BuiltFrom1990,
            ]
        );
    }
    
    private function insertOrganBuilder(array $data, array $categories)
    {
        $organBuilder = new OrganBuilder($data);
        $organBuilder->save();
        $organBuilder->organBuilderCategories()->attach($categories);
    }
}
