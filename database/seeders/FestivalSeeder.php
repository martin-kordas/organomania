<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Festival;
use App\Enums\OrganBuilderCategory;
use App\Enums\Region;

class FestivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$this->insertFestival(
            data: [
                'id' => 1,
                'name' => 'Varhany znějící',
                'locality' => 'Slánsko',
                'place' => null,
                'latitude' => 50.2304578,
                'longitude' => 14.0869344,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'září, jednou za 2 roky',
                'starting_month' => 9,
                'url' => 'http://www.varhany.slansko.cz',
                'importance' => 10,
                'perex' => 'Zatím poslední ročník festivalu, konajícího se na různých místech na Slánsku, se uskutečnil v roce 2018.',
            ],
        );*/
        
        $this->insertFestival(
            data: [
                'id' => 2,
                'name' => 'Audite organum',
                'locality' => 'Praha',
                'place' => 'kostel sv. Jakuba Většího (Staré Město)',
                'latitude' => 50.0883311,
                'longitude' => 14.4249258,
                'region_id' => Region::Praha,
                'organ_id' => 9,
                'frequency' => 'srpen-září',
                'starting_month' => 8,
                'url' => 'http://www.varhany.slansko.cz',
                'importance' => 10,
                'perex' => 'Mezinárodní festival s dlouhouletou tradicí, konající se na největších pražských varhanách, patří mezi nejprestižnější u nás. Festival řídí titulární varhanice u sv. Jakuba v Praze Irena Chřibková.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 3,
                'name' => 'Varhanní léto na Petrově',
                'locality' => 'Brno',
                'place' => 'Katedrála svatých Petra a Pavla',
                'latitude' => 49.1910069,
                'longitude' => 16.6074956,
                'region_id' => Region::Jihomoravsky,
                'organ_id' => null,
                'frequency' => 'červenec-září',
                'starting_month' => 7,
                'url' => 'https://www.katedrala-petrov.cz/varhanni-leto-na-petrove/',
                'importance' => 5,
                'perex' => 'Bohatá řada koncertů v brněnské katedrále hostí řadu renomovaných i mladých talentovaných varhaníků.',
            ],
        );

        $this->insertFestival(
            data: [
                'id' => 4,
                'name' => 'Mezinárodní varhanní festival Olomouc',
                'locality' => 'Olomouc',
                'place' => 'kostel sv. Mořice',
                'latitude' => 49.5925739,
                'longitude' => 17.2496039,
                'region_id' => Region::Olomoucky,
                'organ_id' => 1,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'https://www.mfo.cz/varhanni-festival-program',
                'importance' => 10,
                'perex' => 'Prestižní mezinárodní festival se koná v kostele sv. Mořice v Olomouci již od dob přestavby Englerových varhan v 60. letech 20. století. V té době jej založil významný olomoucký varhaník a organolog Antonín Schindler. V součanosti je uměleckým ředitelem festivalu Karel Martínek. Na vybraných koncertech vystupují členové Moravské filharmonie Olomouc, která je pořadatelem celého festivalu.',
            ],
        );
                
        $this->insertFestival(
            data: [
                'id' => 5,
                'name' => 'Varhanní koncerty na Velehradě',
                'locality' => 'Velehrad',
                'place' => 'Bazilika Nanebevzetí Panny Marie a svatého Cyrila a Metoděje',
                'latitude' => 49.1054278,
                'longitude' => 17.3942636,
                'region_id' => Region::Zlinsky,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'url' => 'https://www.farnostvelehrad.cz/kalendar-akci/koncerty',
                'importance' => 6,
                'perex' => 'Celoroční cyklus varhanních koncertů hostí na nově zrekonstruovaných varhanách velehradské baziliky především domácí interprety. Koncerty bývají tématicky spojeny s výročími významných církevních osobností.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 6,
                'name' => 'Orlicko-kladský varhanní festival',
                'locality' => 'Orlickoústecko',
                'place' => null,
                'latitude' => 49.9738725,
                'longitude' => 16.3936072,
                'region_id' => Region::Pardubicky,
                'organ_id' => null,
                'frequency' => 'září-říjen',
                'starting_month' => 9,
                'url' => 'http://www.ok-organfestival.eu',
                'importance' => 6,
                'perex' => 'Festival probíhá v podhůří Orlických hor již od 90. let a představuje posluchačům řadu stylově různorodých nástrojů.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 7,
                'name' => 'Český varhanní festival',
                'locality' => 'celá ČR',
                'place' => null,
                'latitude' => 49.7428581,
                'longitude' => 15.3384111,
                'region_id' => null,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                // alternativy: https://commons.wikimedia.org/wiki/File:Soli_deo_gloria2.jpg, https://commons.wikimedia.org/wiki/File:Pfarrkirche_Bad_Gams_Interior_Organ_keyboards.jpg
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Organ_in_P%C3%B6llaberg_%28Kirnberg%29%2C_Lower_Austria%2C_Austria_-_Keyboard.jpg/640px-Organ_in_P%C3%B6llaberg_%28Kirnberg%29%2C_Lower_Austria%2C_Austria_-_Keyboard.jpg',
                'image_credits' => 'Benno Sterzer, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'http://www.ceskevarhany.cz',
                'importance' => 8,
                'perex' => 'Cyklus mnoha koncertů na různých místech České republiky si klade za cíl představit vzácné, ale méně známé historické nástroje. Festival založil Adam Viktora, umělecký vedoucí souboru Ensemble Inégal.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 8,
                'name' => 'Arte musica - varhanní hudba ve Filipově',
                'locality' => 'Filipov',
                'place' => 'bazilika Panny Marie Pomocnice křesťanů',
                'latitude' => 50.9851044,
                'longitude' => 14.5946936,
                'region_id' => Region::Ustecky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'http://artemusica.cz',
                'importance' => 6,
                'perex' => 'Programově pestré koncerty hostí kromě varhaníků i vybrané instrumentalisty a sbory. V každém ročníku je jeden z koncertů provázen mluveným slovem v podání známé herecké osobnosti.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 9,
                'name' => 'Brněnský varhanní festival',
                'locality' => 'Brno',
                'place' => 'jezuitský kostel Nanebevzetí Panny Marie, kostel sv. Augustina aj.',
                'latitude' => 49.2002211,
                'longitude' => 16.6078411,
                'region_id' => Region::Jihomoravsky,
                'organ_id' => null,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'http://varhany.nomi.cz',
                'importance' => 9,
                'perex' => 'V současné době nejvýznamnější varhanní festival v Brně hostí každoročně řadu domácích i zahraničních interpretů a vokálně-instrumentálních uskupení.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 10,
                'name' => 'Plzeňský varhanní festival',
                'locality' => 'Plzeň',
                'place' => 'katedrála sv. Bartoloměje',
                'latitude' => 49.7477831,
                'longitude' => 13.3783489,
                'region_id' => Region::Plzensky,
                'organ_id' => null,
                'frequency' => 'duben-červen',
                'starting_month' => 4,
                'url' => 'http://www.plzenskyvarhannifestival.cz',
                'importance' => 7,
                'perex' => 'Festival se koná od roku 2008 na velkých romantických varhanách plzeňské katedrály a přináší především sólové varhanní koncerty. Na dramaturgii festivalu se podílí regenschori katedrály Aleš Nosek.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 11,
                'name' => 'Podzimní varhany',
                'locality' => 'Valašské Meziříčí',
                'place' => 'kostel Nanebevzetí Panny Marie ',
                'latitude' => 49.4718053,
                'longitude' => 17.9711272,
                'region_id' => Region::Zlinsky,
                'organ_id' => null,
                'frequency' => 'září-říjen',
                'starting_month' => 9,
                'url' => 'https://www.zus-vm.cz/varhany',
                'importance' => 6,
                'perex' => 'Festival hostí významné domácí i zahraniční varhaníky. V rámci festivalu se konají i interpretační a improvizační masterclass. Dramaturgem je varhaník Josef Kratochvíl.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 12,
                'name' => 'Svatovítské varhanní večery',
                'locality' => 'Praha',
                'place' => 'katedrála sv. Víta, Václava a Vojtěcha (Hradčany)',
                'latitude' => 50.0908936,
                'longitude' => 14.4006036,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'červenec',
                'starting_month' => 7,
                'url' => 'http://www.svvv.cz',
                'importance' => 8,
                'perex' => 'Festival dává každoročně rozeznít historickým Melzerovým varhanám na Wohlmutově kruchtě svatovítské katedrály.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 13,
                'name' => 'Mezinárodní varhanní festival Zdeňka Pololáníka',
                'locality' => 'Česká Třebová',
                'place' => 'kostel sv. Jakuba Většího',
                'latitude' => 49.9018839,
                'longitude' => 16.4472794,
                'region_id' => Region::Pardubicky,
                'organ_id' => null,
                'frequency' => 'červenec',
                'starting_month' => 7,
                'url' => 'https://www.kcct.cz/festivaly/mezinarodni-varhanni-festival-zdenka-pololanika',
                'importance' => 6,
                'perex' => 'Dramaturgem festivalu, pořádaného v České Třebové už od r. 2005, je varhaník a sbormistr Jan Lorenc.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 14,
                'name' => 'Mladoboleslavské varhanní večery',
                'locality' => 'Mladá Boleslav',
                'place' => 'kostel Nanebevzetí Panny Marie',
                'latitude' => 50.4113514,
                'longitude' => 14.9031850,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'červen',
                'starting_month' => 6,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Church_of_the_Assumption_of_the_Virgin_Mary_%28Mlad%C3%A1_Boleslav%29_02.JPG/640px-Church_of_the_Assumption_of_the_Virgin_Mary_%28Mlad%C3%A1_Boleslav%29_02.JPG',
                'image_credits' => 'Cherubino, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.spumb.cz/l/mladoboleslavske-varhanni-vecery-2024/',
                'importance' => 6,
                'perex' => 'Pořadatelem koncertů je varhaník svatovítské katedrály Ondřej Valenta.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 15,
                'name' => 'Chebské varhanní léto',
                'locality' => 'Cheb',
                'place' => 'kostel svatého Mikuláše',
                'latitude' => 50.0796347,
                'longitude' => 12.3739219,
                'region_id' => Region::Karlovarsky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'https://farnostcheb.cz/chvl',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 16,
                'name' => 'Terezínský varhanní festival',
                'locality' => 'Terezín',
                'place' => 'kostel Vzkříšení Páně',
                'latitude' => 50.5109981,
                'longitude' => 14.1505467,
                'region_id' => Region::Ustecky,
                'organ_id' => null,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'http://varhannifestival-terezin.cz/',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 17,
                'name' => 'Varhanní Vyšehrad',
                'locality' => 'Praha',
                'place' => 'kostel sv. Petra a Pavla (Vyšehrad)',
                'latitude' => 50.0645881,
                'longitude' => 14.4178214,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'červen-červenec',
                'starting_month' => 6,
                'url' => 'https://www.facebook.com/varhannivysehrad',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 18,
                'name' => 'Varhanní podvečery u Salvátora',
                'locality' => 'Praha',
                'place' => 'kostel sv. Salvátora (Staré Město, Salvátorská ul.)',
                'latitude' => 50.0891342,
                'longitude' => 14.4207561,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'url' => 'https://kostelusalvatora.cz/index.php/koncerty',
                'importance' => 7,
                'perex' => 'Cyklus každoměsíčních koncertů se koná na nových varhanách evangelického kostela sv. Salvátora. Koncerty dávají vyniknout mimo jiné řadě talentovaných mladých varhaníků. ',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 19,
                'name' => 'Mezinárodní varhanní festival F. Šťastného',
                'locality' => 'Mělník',
                'place' => 'kostel sv. Petra a Pavla',
                'latitude' => 50.3504969,
                'longitude' => 14.4741075,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'květen-červen',
                'starting_month' => 5,
                'url' => 'http://farnostmelnik.cz/koncerty',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 20,
                'name' => 'Vzkříšené varhany Královéhradeckého kraje',
                'locality' => 'Královéhradecký kraj',
                'place' => null,
                'latitude' => 50.2092283,
                'longitude' => 15.8327683,
                'region_id' => Region::Kralovehradecky,
                'organ_id' => null,
                'frequency' => 'září-říjen',
                'starting_month' => 9,
                'url' => 'https://vzkrisenevarhany.cz',
                'importance' => 7,
                'perex' => 'Cyklus koncertů v různých městech a obcích Královéhradeckého kraje dává zaznít především nově zrekonstrovaným nástrojům. Festival založili varhaník a organolog Václav Uhlíř a pěvec Jakub Hrubý.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 21,
                'name' => 'Varhanní Vysočina',
                'locality' => 'Kraj Vysočina',
                'place' => 'Žďár nad Sázavou, Humpolec, Třebíč aj.',
                'latitude' => 49.5415219,
                'longitude' => 15.3593192,
                'region_id' => Region::Vysocina,
                'organ_id' => null,
                'frequency' => 'září-listopad',
                'starting_month' => 9,
                'url' => 'https://www.varhannivysocina.cz',
                'importance' => 7,
                'perex' => 'Nový festival vzniklý z iniciativy spolku Život pro varhany představuje zajímavé varhany na různých místech Kraje Vysočina.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 22,
                'name' => 'Jablonecké kostely otevřeny',
                'locality' => 'Jablonec nad Nisou',
                'place' => 'kostel Nejsvětějšího Srdce Ježíšova aj.',
                'latitude' => 50.7243075,
                'longitude' => 15.1710772,
                'region_id' => Region::Liberecky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'https://www.jabloneckevarhany.cz',
                'importance' => 6,
                'perex' => 'Koncerty pořádá Nadační fond Jablonecké varhany, který se zasazuje o rekonstrukci varhan v kostele Nejsvětějšího Srdce Ježíšova.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 23,
                'name' => 'Dvořákův festival',
                'locality' => 'celá ČR',
                'place' => 'Hejnice, Kolín aj.',
                'latitude' => 49.7428581,
                'longitude' => 15.3384111,
                'region_id' => null,
                'organ_id' => null,
                'frequency' => 'květen-září',
                'starting_month' => 5,
                'url' => 'https://www.dvorakuvfestival.cz',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Dvorak.jpg/388px-Dvorak.jpg',
                'image_credits' => 'See page for author, Public domain, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'importance' => 7,
                'perex' => 'Široce zaměřený festival s dlouholetou tradicí nabízí kromě jiného i množství varhanních koncertů. Koná se na různých historických místech v Čechách.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 24,
                'name' => 'Silberbauerovo hudební Podyjí',
                'locality' => 'Podyjí',
                'place' => null,
                'latitude' => 48.8454361,
                'longitude' => 15.9113631,
                'region_id' => Region::Jihomoravsky,
                'organ_id' => null,
                'frequency' => 'srpen-říjen',
                'starting_month' => 8,
                'url' => 'https://www.hudebnipodyji.eu',
                'importance' => 8,
                'perex' => 'Festival, který nese jméno barokního varhanáře Josefa Silberbauera, přináší komorní a orchestrální koncerty především staré hudby. Rozvíjí bohatou přeshraniční spolupráci s umělci v Rakousku, kde se také koná část koncertů. Zakladatelkou festivalu je varhanice Kateřina Málková.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 25,
                'name' => 'Nedělní varhanní hudba',
                'locality' => 'Praha',
                'place' => 'kostel sv. Ludmily (Vinohrady)',
                'latitude' => 50.0754669,
                'longitude' => 14.4371797,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'září-říjen',
                'starting_month' => 9,
                'url' => 'https://ludmilavinohrady.cz/rubrika/pozvanky/koncerty/nedelni-varhanni-hudba/',
                'importance' => 6,
                'perex' => 'Sólové varhanní recitály dávají zaznít nově zrekonstruovaným romantickým varhanám kostela sv. Ludmily.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 26,
                'name' => 'Karvinské varhany',
                'locality' => 'Karviná',
                'place' => 'kostel Povýšení sv. Kříže',
                'latitude' => 49.8535119,
                'longitude' => 18.5400853,
                'region_id' => Region::Moravskoslezsky,
                'organ_id' => null,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'https://www.medk.cz/akce/karvinske-varhany-4171',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 27,
                'name' => 'Varhanní koncerty u sv. Máří Magdaleny',
                'locality' => 'Karlovy Vary',
                'place' => 'kostel sv. Máří Magdaleny',
                'latitude' => 50.2227031,
                'longitude' => 12.8844617,
                'region_id' => Region::Karlovarsky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'https://www.kv-concert.com/',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 28,
                'name' => 'Vivat varhany',
                'locality' => 'Slavonice',
                'place' => null,
                'latitude' => 48.9975333,
                'longitude' => 15.3515378,
                'region_id' => Region::Jihocesky,
                'organ_id' => null,
                'frequency' => 'červenec',
                'starting_month' => 7,
                'url' => 'https://ceske-kulturni-slavnosti.webnode.cz/progam/',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 29,
                'name' => 'Trutnovský varhanní festival',
                'locality' => 'Trutnov',
                'place' => 'kostel Narození Panny Marie',
                'latitude' => 50.5603858,
                'longitude' => 15.9133086,
                'region_id' => Region::Kralovehradecky,
                'organ_id' => null,
                'frequency' => 'červenec',
                'starting_month' => 7,
                'url' => 'https://www.trutnov.cz/dre-cs/53877-trutnovsky-varhanni-festival-2024.html',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 30,
                'name' => 'Teplické varhanní léto',
                'locality' => 'Teplice nad Metují, Broumov aj.',
                'place' => null,
                'latitude' => 50.6403975,
                'longitude' => 13.8245072,
                'region_id' => Region::Kralovehradecky,
                'organ_id' => null,
                'frequency' => 'červenec-září',
                'starting_month' => 7,
                'url' => 'https://www.zapoklady.cz/akce/varhanni-leto-2024-rok-ceske-hudby5',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 31,
                'name' => 'Festival Forfest',
                'locality' => 'Kroměříž aj.',
                'place' => 'kostel sv. Mořice',
                'latitude' => 49.2991767,
                'longitude' => 17.3910325,
                'region_id' => Region::Zlinsky,
                'organ_id' => 4,
                'frequency' => 'duben-červenec',
                'starting_month' => 4,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d8/Krom%C4%9B%C5%99%C3%AD%C5%BE%2C_pohled_z_v%C4%9B%C5%BEe_z%C3%A1mku_3.jpg/640px-Krom%C4%9B%C5%99%C3%AD%C5%BE%2C_pohled_z_v%C4%9B%C5%BEe_z%C3%A1mku_3.jpg',
                'image_credits' => 'Palickap, CC BY-SA 3.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.forfest.cz',
                'importance' => 6,
                'perex' => 'Široce zaměřený festival soudobého umění s duchovní tématikou nabízí každoročně i několik varhanních koncertů.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 32,
                'name' => 'Festival duchovní hudby svaté Cecílie',
                'locality' => 'Ústí nad Orlicí',
                'place' => 'kostel Nanebevzetí Panny Marie',
                'latitude' => 49.9746647,
                'longitude' => 16.3933883,
                'region_id' => Region::Pardubicky,
                'organ_id' => null,
                'frequency' => 'říjen',
                'starting_month' => 10,
                'url' => 'https://www.facebook.com/festival.cecilie/',
                'importance' => 3,
                'perex' => 'Sektání a koncert chrámových sborů a varhaníků pořádá každoročně Cecilská hudební jednota v Ústí nad Orlicí.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 33,
                'name' => 'Nedělní varhanní půlhodinky',
                'locality' => 'Svatá hora',
                'place' => 'kostel Nanebevzetí Panny Marie',
                'latitude' => 49.6849767,
                'longitude' => 14.0175856,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'květen-říjen',
                'starting_month' => 5,
                'url' => 'https://www.facebook.com/NedelniVarhanniPulhodinky',
                'importance' => 6,
                'perex' => 'Krátké nedělní koncerty na nově postavených svatohorských varhanách hostí významné domácí i zahraniční interprety. Koncerty pořádá regenschori svatohorské baziliky Pavel Šmolík.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 34,
                'name' => 'Půlhodina s varhanami',
                'locality' => 'Frýdek-Místek',
                'place' => 'bazilika Navštívení Panny Marie',
                'latitude' => 49.6897942,
                'longitude' => 18.3467828,
                'region_id' => Region::Moravskoslezsky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'https://www.voxorganum.cz/pulhodina-s-varhanami/',
                'importance' => 3,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 35,
                'name' => 'Varhanní koncerty v Loretě',
                'locality' => 'Praha',
                'place' => 'kostel Narození Páně (Hradčany)',
                'latitude' => 50.0892925,
                'longitude' => 14.3920533,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'url' => 'https://www.loreta.cz/cs/hudba/varhanni-koncerty-pro-navstevniky',
                'importance' => 3,
                'perex' => 'Neformální koncerty staré varhanní hudby, prokládané často improvizací, probíhají v neděli odpoledne na barokních varhanách pražské Lorety.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 36,
                'name' => 'Noc kostelů',
                'locality' => 'celá ČR',
                'place' => null,
                'latitude' => 49.7428581,
                'longitude' => 15.3384111,
                'region_id' => null,
                'organ_id' => null,
                'frequency' => 'červen',
                'starting_month' => 6,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/7/76/Soli_deo_gloria2.jpg',
                'image_credits' => 'Fox8888, CC BY-SA 3.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.nockostelu.cz/',
                'importance' => 5,
                'perex' => 'V rámci bohatého programu Noci kostelů zaznívá i hudební produkce. Obvykle jde o kratší koncerty varhaníků spojených s místním církevním společenstvím.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 37,
                'name' => 'Osecké ozvěny',
                'locality' => 'Osek u Duchcova',
                'place' => 'cisterciácký klášter Osek',
                'latitude' => 50.6206647,
                'longitude' => 13.6941508,
                'region_id' => Region::Ustecky,
                'organ_id' => null,
                'frequency' => 'květen-září',
                'starting_month' => 5,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Kl%C3%A1%C5%A1ter_Osek_kostel_1.jpg/603px-Kl%C3%A1%C5%A1ter_Osek_kostel_1.jpg',
                'image_credits' => 'VitVit, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.osek.cz/volny-cas/kalendar-akci-1/festival-osecke-ozveny-sevcik-quartet-592_172cs.html',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 38,
                'name' => 'Svatomořická varhanní matinée a koncerty',
                'locality' => 'Olomouc',
                'place' => 'kostel sv. Mořice',
                'latitude' => 49.5925739,
                'longitude' => 17.2496039,
                'region_id' => Region::Olomoucky,
                'organ_id' => 1,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'url' => 'https://www.facebook.com/people/Hudba-v-kostele-sv-Mo%C5%99ice-v-Olomouci/100089914238363/',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 39,
                'name' => 'Maraton hudby Brno - Nekonečné varhany',
                'locality' => 'Brno',
                'place' => 'jezuitský kostel Nanebevzetí Panny Marie',
                'latitude' => 49.1968281,
                'longitude' => 16.6104647,
                'region_id' => Region::Jihomoravsky,
                'organ_id' => null,
                'frequency' => 'srpen',
                'starting_month' => 8,
                'url' => 'https://www.maratonhudby.cz',
                'importance' => 6,
                'perex' => 'Multižánrový festival, odehrávající se v centru města Brna, nabízí každoročně pásmo koncertů Nekonečné varhany. Během jediného odpoledne se u velkých varhan jezuitského kostela vystřídá několik známých českých varhaníků.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 40,
                'name' => 'Nedělní varhanní zastavení',
                'locality' => 'Polička',
                'place' => 'kostel sv. Jakuba',
                'latitude' => 49.7136911,
                'longitude' => 16.2635564,
                'region_id' => Region::Pardubicky,
                'organ_id' => null,
                'frequency' => 'srpen',
                'starting_month' => 8,
                'url' => 'https://www.farnostpolicka.cz/kalendar/akce',
                'importance' => 4,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 41,
                'name' => 'Hudební léto Kuks',
                'locality' => 'Kuks',
                'place' => 'kostel Nejsv. Trojice',
                'latitude' => 50.3979889,
                'longitude' => 15.8894072,
                'region_id' => Region::Kralovehradecky,
                'organ_id' => null,
                'frequency' => 'červen-srpen',
                'starting_month' => 6,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b7/Hospital_Kuks_2022_%2813%29.jpg/640px-Hospital_Kuks_2022_%2813%29.jpg',
                'image_credits' => 'Marie Čcheidzeová, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Kostel_hospital_Kuks_2023_%281%29_07.jpg/640px-Kostel_hospital_Kuks_2023_%281%29_07.jpg',
                'organ_image_credits' => 'Marie Čcheidzeová, CC BY-SA 4.0, via Wikimedia Commons',
                'url' => 'https://hudebniletokuks.cz/',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 42,
                'name' => 'Kladrubské léto',
                'locality' => 'Kladruby',
                'place' => 'kostel Nanebevzetí Panny Marie',
                'latitude' => 49.7126494,
                'longitude' => 12.9955647,
                'region_id' => Region::Plzensky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'https://www.klaster-kladruby.cz/cs/kladrubske-leto-2024',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 43,
                'name' => 'Litoměřické varhanní léto',
                'locality' => 'Litoměřice',
                'place' => 'katedrála sv. Štěpána',
                'latitude' => 50.5321183,
                'longitude' => 14.1285131,
                'region_id' => Region::Ustecky,
                'organ_id' => null,
                'frequency' => 'červenec-září',
                'starting_month' => 7,
                'url' => 'https://www.facebook.com/mkzlitomerice',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 44,
                'name' => 'Plaský varhanní podzim',
                'locality' => 'Plasy',
                'place' => 'kostel Nanebevzetí Panny Marie',
                'latitude' => 49.9354725,
                'longitude' => 13.3903617,
                'region_id' => Region::Plzensky,
                'organ_id' => null,
                'frequency' => 'srpen-říjen',
                'starting_month' => 8,
                'url' => 'https://www.plasy.cz/mesto/aktuality/treti-rocnik-plaskeho-varhanniho-podzimu-2212cs.html',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 45,
                'name' => 'Jablonecké hudební úterky',
                'locality' => 'Jablonec nad Nisou',
                'place' => 'kostel Nejsvětějšího Srdce Ježíšova aj.',
                'latitude' => 50.7243075,
                'longitude' => 15.1710772,
                'region_id' => Region::Liberecky,
                'organ_id' => null,
                'frequency' => 'červenec-srpen',
                'starting_month' => 7,
                'url' => 'http://www.musicavaria.cz',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 46,
                'name' => 'Vinořské varhanní slavnosti',
                'locality' => 'Praha',
                'place' => 'kostel Povýšení Sv. Kříže (Vinoř)',
                'latitude' => 50.1429628,
                'longitude' => 14.5809514,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'https://farnost-vinor.org/vinorske-varhanni-slavnosti/',
                'importance' => 7,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 47,
                'name' => 'Hodolanské varhany',
                'locality' => 'Olomouc',
                'place' => 'kostel Panny Marie Pomocnice Křesťanů (Olomouc-Hodolany)',
                'latitude' => 49.5911181,
                'longitude' => 17.2836803,
                'region_id' => Region::Olomoucky,
                'organ_id' => null,
                'frequency' => 'září',
                'starting_month' => 9,
                'url' => 'https://www.cirkev.cz/hodolanske-varhany-serie-beneficnich-koncertu-duchovni-hudby_43537',
                'importance' => 4,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 48,
                'name' => 'Varhanní festival Maxe Regera',
                'locality' => 'Praha',
                'place' => 'kostel sv. Petra (Nové Město)',
                'latitude' => 50.0912317,
                'longitude' => 14.4342242,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'červen',
                'starting_month' => 6,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Po%C5%99%C3%AD%C4%8D%C3%AD_sv._Petr_8.jpg/633px-Po%C5%99%C3%AD%C4%8D%C3%AD_sv._Petr_8.jpg',
                'image_credits' => 'VitVit, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'http://maxreger.cz/index.php/o-nas/',
                'importance' => 6,
                'perex' => 'Festival představuje kromě jiného především varhanní dílo Maxe Regera. Koncerty probíhají na romantických varhanách Em. Š. Petra v kostele sv. Petra v Praze na Poříčí.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 49,
                'name' => 'Mezinárodní hudební festival J. C. F. Fischera',
                'locality' => 'Karlovarský kraj',
                'place' => 'Karlovy Vary, Jáchymov, Ostrov aj.',
                'latitude' => 50.2301047,
                'longitude' => 12.8665889,
                'region_id' => Region::Karlovarsky,
                'organ_id' => null,
                'frequency' => 'srpen-září',
                'starting_month' => 8,
                'url' => 'https://imff.cz',
                'importance' => 7,
                'perex' => 'Festival nese jméno hudebního skladatele a západočeského rodáka Johanna Caspara Ferdinanda Fischera. Koncerty se zaměřují zejména na barokní instrumentální hudbu. Dramaturgyní festivalu je česká varhanice Michaela Moc-Káčerková.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 50,
                'name' => 'Varhaníci pro sv. Jakuba',
                'locality' => 'Brno',
                'place' => 'katedrála sv. Petra a Pavla, kostel sv. Tomáše, kostel sv. Augustina aj.',
                'latitude' => 49.2002211,
                'longitude' => 16.6078411,
                'region_id' => Region::Jihomoravsky,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ef/01_airshots.cz-wallpaper16x10-noTm.jpg/640px-01_airshots.cz-wallpaper16x10-noTm.jpg',
                'image_credits' => 'by www.airshots.cz, CC BY-SA 3.0 CZ, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.facebook.com/profile.php?id=61553468853819',
                'importance' => 5,
                'perex' => 'Cyklus benefičních koncertů na podporu rekonstrukce brněnského kostela sv. Jakuba.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 51,
                'name' => 'Dušičkové koncerty',
                'locality' => 'Polička',
                'place' => 'kostel sv. Michaela',
                'latitude' => 49.7131675,
                'longitude' => 16.2611239,
                'region_id' => Region::Pardubicky,
                'organ_id' => null,
                'frequency' => 'listopad',
                'starting_month' => 11,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Poli%C4%8Dka%2C_kostel_sv._Michala_%281%29.jpg/360px-Poli%C4%8Dka%2C_kostel_sv._Michala_%281%29.jpg',
                'image_credits' => 'Vlach Pavel, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => null,
                'organ_image_credits' => null,
                'url' => 'https://www.farnostpolicka.cz/kalendar/akce',
                'importance' => 4,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 52,
                'name' => 'Svatojánský varhanní cyklus',
                'locality' => 'Praha',
                'place' => 'kostel sv. Jana Nepomuckého na Skalce (Nové Město)',
                'latitude' => 50.0722814,
                'longitude' => 14.4186058,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'přes celý rok',
                'starting_month' => null,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Pr%C3%A1ga%2C_%C3%BAjv%C3%A1rosi_Nepomuki_Szent_J%C3%A1nos-templom_2022_01.jpg/365px-Pr%C3%A1ga%2C_%C3%BAjv%C3%A1rosi_Nepomuki_Szent_J%C3%A1nos-templom_2022_01.jpg',
                'image_credits' => 'Pasztilla aka Attila Terbócs, CC BY-SA 4.0, via Wikimedia Commons',
                'organ_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Orgel_St._Johannes_von_Nepomuk_am_Felsen%2C_Prag.jpg/360px-Orgel_St._Johannes_von_Nepomuk_am_Felsen%2C_Prag.jpg',
                'organ_image_credits' => 'Kirchenwisser, CC BY-SA 4.0, via Wikimedia Commons',
                'url' => 'https://bachcollegium.cz/svatojansky-varhanni-cyklus-2024',
                'importance' => 6,
                'perex' => 'Dramaturgie koncertů je tvořena jak sólovými varhanami, tak komorní a vokálně instrumentální hudbou. Na varhany hraje a festival umělecky řídí Linda Sítková.',
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 53,
                'name' => 'Mezinárodní dekáda varhanní, komorní hudby a sborového zpěvu',
                'locality' => 'Český Těšín, Cieszyn',
                'place' => null,
                'latitude' => 49.7461381,
                'longitude' => 18.6261358,
                'region_id' => Region::Moravskoslezsky,
                'organ_id' => null,
                'frequency' => 'říjen',
                'starting_month' => 10,
                'url' => 'https://bachcollegium.cz/svatojansky-varhanni-cyklus-2024',
                'importance' => 6,
                'perex' => 'Festival s dlouholetou tradicí a bohatým programem nabízí koncerty v řadě kostelů Českého a Polského Těšína.',
            ],
        );
    }
    
    private function insertFestival(array $data)
    {
        $festival = new Festival($data);
        $festival->save();
    }
}
