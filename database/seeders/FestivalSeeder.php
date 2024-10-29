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
        $this->insertFestival(
            data: [
                'id' => 1,
                'name' => 'Varhany znějící',
                'locality' => 'Slánsko',
                'place' => null,
                'latitude' => 50.2304578,
                'longitude' => 14.0869344,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'jednou za 2 roky',
                'url' => 'http://www.varhany.slansko.cz',
                'importance' => 10,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 2,
                'name' => 'Audite organum',
                'locality' => 'Praha',
                'place' => 'kostel sv. Jakuba Většího',
                'latitude' => 50.0883311,
                'longitude' => 14.4249258,
                'region_id' => Region::Praha,
                'organ_id' => 9,
                'frequency' => 'srpen-září',
                'url' => 'http://www.varhany.slansko.cz',
                'importance' => 10,
                'perex' => 'Festival s dlouhou tradicí, konající se na největších pražských varhanách, patří mezi nejprestižnější u nás.',
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
                'url' => 'https://www.katedrala-petrov.cz/varhanni-leto-na-petrove/',
                'importance' => 5,
                'perex' => 'Festival s dlouhou tradicí, konající se na největších pražských varhanách, patří mezi nejprestižnější u nás.',
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
                'url' => 'https://www.mfo.cz/varhanni-festival-program',
                'importance' => 10,
                'perex' => 'Prestižní festival se koná v kostele sv. Mořice v Olomouci již od dob přestavby Englerových varhan.',
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
                'url' => 'https://www.farnostvelehrad.cz/kalendar-akci/koncerty',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'http://www.ok-organfestival.eu',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'http://www.ceskevarhany.cz',
                'importance' => 8,
                'perex' => null,
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
                'url' => 'http://artemusica.cz',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'http://varhany.nomi.cz',
                'importance' => 9,
                'perex' => null,
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
                'url' => 'http://www.plzenskyvarhannifestival.cz',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://www.zus-vm.cz/varhany',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 12,
                'name' => 'Svatovítské varhanní večery',
                'locality' => 'Praha',
                'place' => 'katedrála sv. Víta, Václava a Vojtěcha',
                'latitude' => 50.0908936,
                'longitude' => 14.4006036,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'červenec',
                'url' => 'http://www.svvv.cz',
                'importance' => 8,
                'perex' => null,
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
                'url' => 'https://www.kcct.cz/festivaly/mezinarodni-varhanni-festival-zdenka-pololanika',
                'importance' => 6,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 14,
                'name' => 'Mladoboleslavské varhanní večery',
                'locality' => 'Mladá Boleslav',
                'place' => 'kostel sv. Jakuba Většího',
                'latitude' => 50.4113514,
                'longitude' => 14.9031850,
                'region_id' => Region::Stredocesky,
                'organ_id' => null,
                'frequency' => 'červen',
                'url' => 'https://www.spumb.cz/l/mladoboleslavske-varhanni-vecery-2024/',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://kostelusalvatora.cz/index.php/koncerty',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://vzkrisenevarhany.cz',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://www.varhannivysocina.cz',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://www.jabloneckevarhany.cz',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://www.dvorakuvfestival.cz',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://www.hudebnipodyji.eu',
                'importance' => 8,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 25,
                'name' => 'Nedělní varhanní hudba',
                'locality' => 'Praha',
                'place' => 'kostel sv. Ludmily',
                'latitude' => 50.0754669,
                'longitude' => 14.4371797,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'září-říjen',
                'url' => 'https://ludmilavinohrady.cz/rubrika/pozvanky/koncerty/nedelni-varhanni-hudba/',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://www.teplicenadmetuji.cz/volny-cas/akce-u-nas/varhanni-leto-2024-1628_1031cs.html',
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
                'url' => 'https://www.forfest.cz',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://www.facebook.com/festival.cecilie/',
                'importance' => 3,
                'perex' => null,
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
                'url' => 'https://www.facebook.com/NedelniVarhanniPulhodinky',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://www.loreta.cz/cs/hudba/varhanni-koncerty-pro-navstevniky',
                'importance' => 3,
                'perex' => null,
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
                'url' => 'https://www.nockostelu.cz/',
                'importance' => 5,
                'perex' => null,
            ],
        );
        
        $this->insertFestival(
            data: [
                'id' => 37,
                'name' => 'Osecké ozvěny',
                'locality' => 'Osek u Duchcova',
                'place' => null,
                'latitude' => 50.6206647,
                'longitude' => 13.6941508,
                'region_id' => Region::Ustecky,
                'organ_id' => null,
                'frequency' => 'květen-září',
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
                'url' => 'https://www.maratonhudby.cz',
                'importance' => 6,
                'perex' => null,
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
                'place' => 'kostel Povýšení Sv. Kříže (Praha-Vinoř)',
                'latitude' => 50.1429628,
                'longitude' => 14.5809514,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'září',
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
                'place' => 'kostel sv. Petra (Praha-Nové Město)',
                'latitude' => 50.0912317,
                'longitude' => 14.4342242,
                'region_id' => Region::Praha,
                'organ_id' => null,
                'frequency' => 'červen',
                'url' => 'http://maxreger.cz/index.php/o-nas/',
                'importance' => 6,
                'perex' => null,
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
                'url' => 'https://imff.cz',
                'importance' => 7,
                'perex' => null,
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
                'url' => 'https://www.facebook.com/profile.php?id=61553468853819',
                'importance' => 5,
                'perex' => null,
            ],
        );
    }
    
    private function insertFestival(array $data)
    {
        $festival = new Festival($data);
        $festival->save();
    }
}
