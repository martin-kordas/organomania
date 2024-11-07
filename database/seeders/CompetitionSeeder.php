<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\CompetitionYear;
use App\Enums\Region;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertCompetition(
            data: [
                'id' => 1,
                'name' => 'Mezinárodní varhanní soutěž Petra Ebena',
                'locality' => 'Opava, Hlučín',
                'place' => null,
                'latitude' => 49.9390617,
                'longitude' => 17.9026936,
                'region_id' => Region::Moravskoslezsky,
                'frequency' => 'říjen, každé 2 roky',
                'max_age' => 29,
                'participation_fee' => 1500,
                'participation_fee_eur' => 60,
                'first_prize' => 60_000,
                'next_year' => 2026,
                'inactive' => false,
                'international' => true,
                'image_url' => null,
                'image_credits' => null,
                'url' => 'https://www.ebencompetition.cz',
                'perex' => 'Nejznámější a nejstarší čistě varhanní soutěž v České republice. Tradice soutěží mladých varhaníků v Opavě sahá až do r. 1978. Jméno hudebního skladatele Petra Ebena nese název soutěže od r. 2004. Soutěžní podmínky se ustálily na tříkolové variantě soutěže, kdy každé z kol probíhá na jiných varhanách. Skladby Petra Ebena bývají povinnou součástí repertoáru. Český hudební fond pravidelně uděluje prémii za nejlepší provedení skladby Petra Ebena ve finálním kole. Soutěž je nejsilněji obsazena účastníky ze středovýchodní Evropy, láká však i soutěžící ze západní Evropy či ze zámoří. Součástí každého ročníku je jednokolová soutěž ve varhanní improvizaci, zahrnující improvizaci na témata z tvorby Petra Ebena.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2024,
                    'description' => <<<END
                        **Interpretace**
                        1\. cena: Stanislav Mühlfait (Česká republika)
                        2. cena: Lukáš Dvořák (Česká republika)
                        3. cena: David Kiefer (Německo)
                        (Počet soutěžících: 34)

                        **Improvizace**
                        1\. cena: Brúnó Kaposi (Maďarsko)
                        (Počet soutěžících: 6)

                        **Porota**
                        Michel Bouvard, předseda (Francie)
                        Andrzej Białko (Polsko)
                        Eva Bublová (Česká republika)
                        Peter van Dijk (Holandsko)
                        Wolfgang Kogert (Rakousko)
                        Petr Kolař (Česká republika)
                        Bernadetta Šuňavská (Slovensko/Německo)
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2022,
                    'description' => <<<END
                        **Interpretace**
                        1\. cena: Laura Schlappa (Německo)
                        2. cena: Mariusz Wycisk (Polsko)
                        2. cena: Yewon Choi (Korejská republika)
                        3. cena: Marek Lipka (Česká republika)
                        (Počet soutěžících: 28)

                        **Improvizace**
                        1\. cena: Mariusz Kozieł (Polsko)
                        (Počet soutěžících: 9)

                        **Porota**
                        Julian Gembalski, předseda (Polsko)
                        Petr Čech (Česká republika)
                        Aude Heurtematte (Francie)
                        Irena Chřibková (Česká republika)
                        Zuzana Mausen-Ferjenčíková (Slovensko-Švýcarsko)
                        Martin Sander (Německo)
                        Marek Vrábel (Slovensko)
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2018,
                    'description' => <<<END
                        **Interpretace**
                        1\. cena: Filip Šmerda (Česká republika)
                        2. cena: Tatiana Pernetová
                        3. cena: Anastasia Kovbyk, Josef Kratochvíl

                        **Improvizace**
                        1\. cena: Marcel Eliasch

                        **Porota**
                        Wacław Golonka, předseda (Polsko)
                        Pavel Černý (Česká republika)
                        Jürgen Essl (Německo)
                        Monika Melcová (Slovensko/Španělsko)
                        Petr Rajnoha (Česka republika)
                        Gunther Rost (Německo)
                        Imrich Szabó (Slovensko)
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2016,
                    'description' => <<<END
                        **Interpretace**
                        1\. cena: Jemina Stephenson (Velká Británie)
                        2. cena: Daniel Strządała (Polsko)
                        3. cena: Petra Andrlová (Česká republika), Daniel Knut Pernet (Česká republika)
                        (Počet soutěžících: 29)

                        **Improvizace**
                        1\. cena: Daniel Strządała (Polsko)
                        (Počet soutěžících: 12)

                        **Porota**
                        Jaroslav Tůma, předseda (Česká republika)
                        Aude Heurtematte (Francie)
                        Balázs Szabó (Maďarsko)
                        Bernadetta Šuňavská (Slovensko/Německo)
                        Hans Fagius (Švédsko/Dánsko)
                        Petr Čech (Česká republika)
                        Jarosław Wróblewski (Polsko)
                        END,
                ]),
            ]
        );
        
        // propozice: https://www.facebook.com/photo/?fbid=4753868681335002&set=pcb.4753870704668133
        $this->insertCompetition(
            data: [
                'id' => 2,
                'name' => 'Mezinárodní soutěž Leoše Janáčka v Brně',
                'locality' => 'Brno',
                'place' => 'HF JAMU, kostel Nanebevzetí P. Marie',
                'latitude' => 49.2002211,
                'longitude' => 16.6078411,
                'region_id' => Region::Jihomoravsky,
                'frequency' => 'září, každých 5 let',
                'max_age' => 35,
                'participation_fee' => 2500,
                'participation_fee_eur' => null,
                'first_prize' => 100_000,
                'next_year' => 2027,
                'inactive' => false,
                'international' => true,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ef/01_airshots.cz-wallpaper16x10-noTm.jpg/640px-01_airshots.cz-wallpaper16x10-noTm.jpg',
                'image_credits' => 'by www.airshots.cz, CC BY-SA 3.0 CZ, via Wikimedia Commons',
                'url' => 'https://hf.jamu.cz/mslj/',
                'perex' => 'Interpretační soutěž každoročně pořádaná Janáčkovou akademií múzických umění v Brně vznikla už v r. 1993. V každém ročníku soutěží jiné nástroje - ročník se soutěží pro varhany a klavír tedy proběhne jednou za 5 let. Soutěž bývá tříkolová. V r. 2022 proběhlo úvodní kolo ve varhanním sále Hudební fakulty JAMU, zbylá kola pak na velkých varhanách jezuitského kostela.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2022,
                    'description' => <<<END
                        1\. cena: Seona Mun (Korejská republika)
                        2. cena: neudělena
                        3. cena: Daniela Hřebíčková (Česká republika)
                        3. cena: Jan Kopřiva (Česká republika)
                        (Počet soutěžících: 24)

                        **Porota**
                        Johannes Ebenbauer, předseda (Rakousko)
                        Stefan Baier (Německo)
                        Zdeněk Nováček (Česká republika)
                        Roman Perucki (Polsko)
                        Petr Rajnoha (Česká republika)
                        Balázs Szabó (Maďarsko)
                        Jaroslav Tůma (Česká republika)
                        END,
                ]),
                
                new CompetitionYear([
                    'year' => 2017,
                    'description' => <<<END
                        1\. cena: Martin Moudrý (Česká republika)
                        2. cena: Petra Kujalová (Česká republika)
                        3. cena: Tatiana Ivanshina (Česká republika)
                        (Počet soutěžících: 7)

                        **Porota**
                        Wictor Lyjak (Polsko)
                        Balász Szabo (Maďarsko)
                        Velin Iliev (Bulharsko)
                        Ján Vladimír Michalko (Slovensko)
                        Zdeněk Nováček (Česká republika)
                        END,
                ]),
            ]
        );
        
        $this->insertCompetition(
            data: [
                'id' => 3,
                'name' => 'Voříškův Vamberk',
                'locality' => 'Vamberk',
                'place' => 'kostel sv. Prokopa',
                'latitude' => 50.1176228,
                'longitude' => 16.2882267,
                'region_id' => Region::Kralovehradecky,
                'frequency' => 'již se nekoná',
                'max_age' => 24,
                'participation_fee' => null,
                'participation_fee_eur' => null,
                'first_prize' => null,
                'next_year' => null,
                'inactive' => true,
                // TODO: opravdu?
                'international' => false,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Vamberk_-_Husovo_n%C3%A1m%C4%9Bst%C3%AD_s_mari%C3%A1nsk%C3%BDm_sloupem_a_kostelem_sv._Prokopa.jpg/640px-Vamberk_-_Husovo_n%C3%A1m%C4%9Bst%C3%AD_s_mari%C3%A1nsk%C3%BDm_sloupem_a_kostelem_sv._Prokopa.jpg',
                'image_credits' => 'MartinVeselka, CC BY-SA 4.0, via Wikimedia Commons',
                'url' => null,
                'perex' => 'Soutěž probíhala v letech 1991 až 2008 v rámci stejnojmenného hudebního festivalu. Soutěžilo se v kostele sv. Prokopa na historických varhanách z dílny králických varhanářů, kde hudební skladatel J. V. H. Voříšek sám hrával. Repertoár soutěže byl zaměřen na barokní a raně klasicistní hudbu. Soutěž měla i improvizační část, zahrnující improvizaci na vybraná Voříškova témata.',
            ],
            organs: [],
            competitionYears: []
        );
        
        $this->insertCompetition(
            data: [
                'id' => 4,
                'name' => 'Prague Organ Competition',
                'locality' => 'Praha',
                'place' => 'Dvořákova síň Rudolfina',
                'latitude' => 50.08993,
                'longitude' => 14.4154419,
                'region_id' => Region::Praha,
                'frequency' => 'listopad',
                'max_age' => 34,
                'participation_fee' => 2500,
                'participation_fee_eur' => 100,
                'first_prize' => null,
                'next_year' => 2025,
                'inactive' => false,
                'international' => true,
                'image_url' => null,
                'image_credits' => null,
                'url' => 'http://www.zivotprovarhany.cz/pragueorgancompetition/',
                'perex' => 'Soutěž pořádá spolek Život pro varhany. První kolo je organizováno formou zaslání audiovizuální nahrávky, druhé kolo proběhne na varhanách v Rudolfinu.',
            ],
            organs: [8],
            competitionYears: []
        );
        
        $this->insertCompetition(
            data: [
                'id' => 5,
                'name' => 'Soutěžní varhanní přehlídka Kolín',
                'locality' => 'Kolín',
                'place' => 'kostel sv. Bartoloměje',
                'latitude' => 50.0266383,
                'longitude' => 15.2017997,
                'region_id' => Region::Stredocesky,
                'frequency' => 'září',
                'max_age' => null,
                'participation_fee' => 1000,
                'participation_fee_eur' => null,
                'first_prize' => 15_000,
                'next_year' => null,
                'inactive' => false,
                'international' => false,
                'image_url' => null,
                'image_credits' => null,
                'url' => 'https://varhanykolin.cz/',
                'perex' => 'Soutěž pořádaná na nových symfonických varhanách Kolínského chrámu. Je otevřena studentům konzervatoří, akademií a univerzit v České republice bez omezení věku.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2024,
                    'description' => <<<END
                        **I. kategorie** (konzervatoře)
                        1\. cena: Markéta Poláková
                        2. cena: Jana Pochobradská, Patrik Buchta
                        3. cena: Adam Suk
                        (Počet soutěžnících: 11)
                    
                        **II. kategorie** (akademie a vysoké školy)
                        1\. cena: Tomáš Sommer
                        2. cena: Petr Otáhal
                        3. cena: Monika Šnorbertová
                        (Počet soutěžnících: 6)

                        **Porota**
                        Luboš Sluka, čestný předseda
                        Aleš Bárta
                        Petr Kolař
                        Přemysl Kšica
                        Pavel Černý
                        Pavel Rybka
                        END,
                ]),
            ]
        );
        
        $this->insertCompetition(
            data: [
                'id' => 6,
                'name' => 'Pro Bohemia Ostrava',
                'locality' => 'Ostrava',
                'place' => 'Janáčkova konzervatoř',
                'latitude' => 49.838215,
                'longitude' => 18.2829992,
                'region_id' => Region::Moravskoslezsky,
                'frequency' => 'duben, každoročně',
                'max_age' => 22,
                'participation_fee' => 1200,
                'participation_fee_eur' => null,
                'first_prize' => 0,
                'next_year' => null,
                'inactive' => false,
                'international' => true,
                'image_url' => null,
                'image_credits' => null,
                'url' => 'https://www.jko.cz/pro-bohemia-ostrava-2025',
                'perex' => 'Jednokolová soutěž mladých interpretů různých oborů, pořádaná každoročně Janáčkovou konzervatoří v Ostravě. Pro obor varhany soutěž v některých ročnících není vypsána.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2024,
                    'description' => <<<END
                        **2. kategorie**
                        1\. cena: Martin Droppa (Česká republika)
                        2. cena: Maksym Tomczak (Polsko)
                        3. cena: Bartosz Wesołek (Polsko)
                    
                        **3. kategorie**
                        1\. cena: Švaříčková Jana (Česká republika)
                        2. cena: Vojtěch Krejsa (Česká republika)
                        3. cena: Hornjak Andrej (Slovensko)
                    
                        **4. kategorie**
                        1\. cena: Samuel Lihotský (Slovensko)
                        2. cena: Adam Suk (Česká republika)
                        3. cena: Matyáš Moravetz (Česká republika)
                    
                        **Porota**
                        Krzysztof Lukas, předseda (Polsko)
                        Michal Hanuš (Česká republika)
                        Ester Moravetzová (Česká republika)
                        Peter Reiffers (Slovensko)
                        Pavel Rybka (Česká republika)
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2023,
                    'description' => <<<END
                        **2. kategorie**
                        1\. cena: Stanislav Mühlfait (Česká republika), Agata Żogała (Polsko)
                        2. cena: neudělena
                        3. cena: Martin Droppa (Česká republika), Paweł Stroka (Polsko)
                    
                        **3. kategorie**
                        1\. cena: Adam Suk (Česká republika)
                        2. cena: Matyáš Moravetz (Česká republika), Bartosz Wódczak (Polsko)
                        3. cena: Šárka Machačová (Česká republika), Jiří Šulák (Česká republika)
                    
                        **4. kategorie**
                        1\. cena: Karolína Blabová (Česká republika)
                        2. cena: Lamlová Eliška (Česká republika)
                        3. cena: Dominik Kruyer (Česká republika), Nela Pálková (Česká republika)
                    
                        **Porota**
                        Krzysztof Lukas, předseda (Polsko)
                        Michal Hanuš (Česká republika)
                        Ester Moravetzová (Česká republika)
                        Peter Reiffers (Slovensko)
                        Pavel Rybka (Česká republika)
                        END,
                ]),
            ]
        );
        
        $this->insertCompetition(
            data: [
                'id' => 7,
                'name' => 'Organum regium',
                'locality' => 'Pardubice',
                'place' => 'Sukova síň',
                'latitude' => 50.0383264,
                'longitude' => 15.7753506,
                'region_id' => Region::Pardubicky,
                'frequency' => 'jaro, každoročně',
                'max_age' => null,
                'participation_fee' => 1200,
                'participation_fee_eur' => null,
                'first_prize' => 0,
                'next_year' => 2025,
                'inactive' => false,
                'international' => false,
                'image_url' => null,
                'image_credits' => null,
                'url' => 'https://sites.google.com/zuspardubice.cz/organumregium/',
                'perex' => 'Soutěž se koná od r. 2001 a je vyhrazena žákům základních uměleckých škol v České republice. Soutěž probíhá v prostorách Konzervatoře Pardubice a je rozdělena do kategorií Prima, Junior a Virtuos. Všechny nebo alespoň vyšší kategorie účastníků soutěží na velkých varhanách Sukovy síně. V kategorii Virtuosi jsou součástí doporučeného repertoáru soudobé skladby, zkomponované přímo pro potřeby soutěže.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2024,
                    'description' => <<<END
                        **Porota**
                        František Vaníček, předseda (Česká republika)
                        Jan Hora (Česká republika)
                        Přemysl Kšica (Česká republika)
                        Michal Hanuš (Česká republika)
                        Janko Siroma
                        END,
                ]),
            ]
        );
        
        $source = '(Zdroj: SVOBODA, Pavel. Varhanní tvorba na objednávku Mezinárodní hudební soutěže Pražské jaro. Praha, 2020. Disertační práce. HAMU v Praze, Hudební a taneční fakulta)';
        $this->insertCompetition(
            data: [
                'id' => 8,
                'name' => 'Mezinárodní hudební soutěž Pražské jaro',
                'locality' => 'Praha',
                'place' => null,
                'latitude' => 50.0835494,
                'longitude' => 14.4341414,
                'region_id' => Region::Praha,
                'frequency' => 'květen, v nepravidelných intervalech',
                'max_age' => 29,
                'participation_fee' => null,
                'participation_fee_eur' => 150,
                'first_prize' => 250_000,
                'next_year' => null,
                'inactive' => false,
                'international' => true,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Pet%C5%99%C3%ADn_Tower_View_IMG_3020.JPG/640px-Pet%C5%99%C3%ADn_Tower_View_IMG_3020.JPG',
                'image_credits' => 'Deror_avi, CC BY-SA 4.0, via Wikimedia Commons',
                'url' => 'https://festival.cz/soutez/',
                'perex' => 'Interpretační soutěž konaná od r. 1947 při hudebním festivalu Pražské jaro má mimořádné mezinárodní renomé. V každém ročníku soutěží jiné nástroje. Pro varhany byla soutěž vypsána naposledy v r. 2013, kdy proběhla jako tříkolová s výběrovým předkolem. Výběr varhan, kde probíhají jednotlivá kola, se v každém ročníku mírně liší. Součástí repertoáru je vždy soudobá skladba napsaná přímo pro potřeby soutěže.',
            ],
            organs: [8],
            competitionYears: [
                new CompetitionYear([
                    'year' => 1958,
                    'description' => <<<END
                        1\. cena
                        Václav Rabas (ČSR)
                    
                        2\. cena
                        Gisbert Schneider (NSR), Jaroslav Vodrážka (ČSR) a Helmuth Plattner (Rumunsko)
                    
                        3\. cena
                        Janine Corajod (Švýcarsko)
                    
                        4\. cena
                        Lubina Holanec - Rawpova (NDR)
                    
                        5\. cena
                        Irma Thenior – Janecka (Polsko)
                    
                        (Počet soutěžících: 39)
                    
                        **Porota**
                        Václav Holzknecht (ČSR) – předseda
                        Pierre Segond (Švýcarsko) – I. místopředseda
                        Hugo Lepnurm (SSSR) – II. místopředseda
                        Adrian Engels (Holandsko)
                        Miroslav Kampelsheimer (ČSR)
                        Johann Ernst Köhler (NDR)
                        Josef Kubáň (ČSR)
                        Štefan Németh-Šamorínsky (ČSR)
                        Sebestyén Pécsi (Maďarsko)
                        Jiří Reinberger (ČSR)
                        Bronislav Rutkowski (Polsko)
                        Gabriel Verschraegen (Belgie)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1966,
                    'description' => <<<END
                        **Interpretační soutěž**
                        1\. cena
                        Petr Sovadina (ČSSR)
                        Otfried Miller (NSR)
                    
                        2\. cena
                        Ivan Sokol (ČSSR)
                    
                        3\. cena
                        Karl Reiner Böhme (NDR)
                        Eva Kamrlová (ČSSR)
                        Jaroslava Potměšilová (ČSSR)
                    
                        (Počet soutěžících: 42)
                    
                    
                        **Improvizační soutěž**
                        1\. cena
                        Karl Rainer Böhme (NDR)
                    
                        2\. cena
                        neudělena
                    
                        3\. cena
                        Johan Huye (Belgie)
                    
                    
                        **Porota**
                        Jiří Reinberger (ČSSR) – předseda
                        Anton Nowakowski (NSR) – I. místopředseda
                        Leonid Roizmann (SSSR) – II. místopředseda
                        Xaver Dressler (Rumunsko)
                        Alois Forer (Rakousko)
                        Ferenc Gergely (Maďarsko)
                        Józef Chwedczuk (Polsko)
                        Ferdinand Klinda (ČSSR)
                        Johann Ernst Köhler (NDR)
                        Jan Bedřich Krajs (ČSSR)
                        Gabriel Verschraegen (Belgie)
                        Alena Veselá (ČSSR)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1971,
                    'description' => <<<END
                        1\. cena
                        neudělena
                    
                        2\. cena
                        Edgar Krapp (NSR)
                        Kamila Klugarová (ČSSR)
                    
                        3\. cena
                        Vladimír Rusó (ČSSR)
                        Charles Robert Benbow (USA)
                    
                        (Počet soutěžících: 34)
                    
                        **Porota**
                        Jiří Reinberger (ČSSR) – předseda
                        Ferenc Gergély (Maďarsko) – místopředseda
                        Hans Vollenweider (Švýcarsko) – místopředseda
                        Herbert Collum (NDR)
                        Hans Haselböck (Rakousko)
                        Jan Jargon (Polsko)
                        Ferdinand Klinda (ČSSR)
                        Leonid Roizman (SSSR)
                        Schneider Gisbert (NSR)
                        Milan Šlechta (ČSSR)
                        Alena Veselá (ČSSR)
                        Peter Hurford (Velká Británie)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1979,
                    'description' => <<<END
                        1\. cena
                        neudělena
                    
                        2\. cena
                        James Kibbie (USA)
                        Josef Popelka (ČSSR)
                        Ursula Compoy-Philippi (Rumunsko)
                    
                        3\. cena
                        Jaroslav Tůma (ČSSR)
                        Zsuzsana Elekes (Maďarsko)
                        Imrich Szabó (ČSSR)
                    
                        (Počet soutěžících: 43)
                    
                        **Porota**
                        Václav Rabas (Československo) – předseda
                        Arsenij Kotljarevskij (SSSR) – místopředseda
                        Pierre Segond (Švýcarsko) – místopředseda
                        Herbert Collum (NDR)
                        Joachim Grubich (Polsko)
                        Hans Haselböck (Rakousko)
                        Ferdinand Klinda (Československo)
                        Sándor Margittay (Maďarsko)
                        Eckerhard Schneck (NSR)
                        Milan Šlechta (Československo)
                        Alena Veselá (Československo)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1984,
                    'description' => <<<END
                        1\. cena
                        Aleš Bárta (ČSSR)
                    
                        2\. cena
                        neudělena
                    
                        3\. cena
                        Zuzana Němečková (ČSSR)
                        Jan Kalfus (ČSSR)
                        Rainer Maria Rückschloss (NSR)
                    
                        (Počet soutěžících: 54)
                    
                        **Porota**
                        Václav Rabas (ČSSR) – předseda
                        Hans Haselböck (Rakousko) – místopředseda
                        Johannes-Ernst Köhler (NDR) – místopředseda
                        Garri Grodberg (SSSR)
                        Ferdinand Klinda (ČSSR)
                        Sándor Margittay (Maďarsko)
                        Carlo Florindo Semini (Švýcarsko)
                        Józef Serafin (Polsko)
                        Arno Schönstedt (NSR)
                        Milan Šlechta (ČSSR)
                        Alena Veselá (ČSSR)
                        Giuseppe Zanaboni (Itálie)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1989,
                    'description' => <<<END
                        1\. cena
                        Martin Sander
                    
                        2\. cena neudělena
                    
                        3\. cena
                        Heidi Emmert (Německo)
                        Frank Volke (Německo)
                        Petr Kolář (ČSSR)
                    
                        (Počet soutěžících: 60)
                    
                        **Porota**
                        Václav Rabas (Československo) – předseda
                        Leopoldas Digris (SSSR) – místopředseda
                        Hans Haselböck (Rakousko) – místopředseda
                        Michio Akimoto (Japonsko)
                        Christoph Albrecht (NDR)
                        István Ella (Maďarsko)
                        Geraint Jones (Velká Británie)
                        Józef Serafin (Polsko)
                        Gisbert Schneider (NSR)
                        Ivan Sokol (Československo)
                        Milan Šlechta (Československo)
                        Alena Veselá (Československo)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1994,
                    'description' => <<<END
                        1\. cena
                        Pavel Černý (Česká republika)
                    
                        2\. cena
                        Wacław Seweryn Golonka (Polsko)
                    
                        3\. cena
                        nebyla udělena
                    
                        (Počet soutěžících: 33)
                    
                        **Porota**
                        Hans Haselböck (Rakousko) – místopředseda
                        Alena Veselá (Česká republika) – místopředseda
                        Aleš Bárta (Česká republika)
                        Jan Hora (Česká republika)
                        Susan Landale (Francie)
                        Karel Paukert (USA)
                        Gisbert Schneider (SRN)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 1999,
                    'description' => <<<END
                        1\. cena
                        neudělena
                    
                        2\. cena
                        Eva Bublová
                    
                        3\. cena
                        Petr Rajnoha
                    
                        (Počet soutěžících: 33)
                    
                        **Porota**
                        Gisbert Schneider (SRN) – předseda
                        Jan Hora (Česká republika) – místopředseda
                        Ewald Kooiman (Nizozemí)
                        Susan Landale (Francie)
                        Karel Paukert (USA)
                        Jaroslav Tůma (Česká republika)
                        Alena Veselá (Česká republika)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2006,
                    'description' => <<<END
                        1\. cena
                        neudělena
                    
                        2\. cena
                        Petr Čech (Česká republika), Maria Mokhova (Rusko)
                    
                        3\. cena
                        Els Biesemans (Belgie)
                    
                        **Porota**
                        Martin Sander (Německo) – předseda
                        Jan Hora (Česká republika)
                        Bárta Aleš (Česká republika)
                        Paolo Crivellaro (Itálie)
                        Julian Gembalski (Polsko)
                        Susan Landale (Francie)
                        Simon Preston (Velká Británie)
                    
                        $source
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2013,
                    'description' => <<<END
                        1\. cena
                        Karol Mossakowski (Polsko)
                    
                        2\. cena
                        Johanna Soller (Německo)
                    
                        3\. cena
                        Mari Okhi (Japonsko), Pavel Svoboda (Česká republika)
                    
                        (Počet soutěžících: 42)
                    
                        **Porota**
                        Jon Laukvik (Norsko) – předseda
                        Aleš Bárta (Česká republika) – místopředseda
                        Pavel Černý (Česká republika)
                        James Kibbie (USA)
                        Susan Landale (Francie)
                        Jacques van Oortmerssen (Nizozemsko)
                        Elisabeth Ullmann (Rakousko)
                    
                        $source
                        END,
                ]),
            ]
        );
        
        $this->insertCompetition(
            data: [
                'id' => 9,
                'name' => 'Mezinárodní varhanní soutěž Jana Křtitele Vaňhala',
                'locality' => 'Opočno',
                'place' => 'kostel Narození Páně',
                'latitude' => 50.2676300,
                'longitude' => 16.1160244,
                'region_id' => Region::Kralovehradecky,
                'frequency' => 'červen, každoročně',
                'max_age' => null,
                'participation_fee' => 1500,
                'participation_fee_eur' => null,
                'first_prize' => 10_000,
                'next_year' => 2025,
                'inactive' => false,
                'international' => true,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Church_of_the_Nativity_of_Christ_%28Opo%C4%8Dno%29.JPG/640px-Church_of_the_Nativity_of_Christ_%28Opo%C4%8Dno%29.JPG',
                'image_credits' => 'Packare, CC BY 4.0, via Wikimedia Commons',
                'url' => 'https://www.provarhany.cz/rubrika/49-Varhanni-soutez-Jana-Krtitele-Vanhala/index.htm',
                'perex' => 'Nová soutěž, pořádaná spolkem PROVARHANY, probíhá na restaurovaných varhanách králické dílny z r. 1748 v kostele Narození Páně v Opočně, tedy v místě působiště slavného skladatele J. K. Vaňhala. Soutěž je vyhrazena žákům základních uměleckých škol (I. kategorie) a studentům konzervatoří, akademií a univerzit (II. kategorie). Repertoár soutěže se vzhledem k charakteristikám nástroje, disponujícího krátkou oktávou, zaměřuje na starou hudbu.',
            ],
            organs: [],
            competitionYears: [
                new CompetitionYear([
                    'year' => 2024,
                    'description' => <<<END
                        **I. kategorie**
                        1\. místo
                        Martin Droppa

                        2\. místo
                        Daniel Nerad

                        3\. místo
                        Olga Báčová
                        Filip Kobělka
                    
                        **II. kategorie**
                        1\. místo
                        Lukáš Dvořák (Česká republika)

                        2\. místo
                        Miroslav Baklík (Česká republika)
                        Michaela Toráčová (Slovensko)

                        3\. místo
                        Wei Tsai (Tchaj-wan)
                    
                        **Porota**
                        Michaela Káčerková (Česká republika)
                        Waclaw Golonka (Polsko)
                        Petr Čech (Česká republika)
                        END,
                ]),
                new CompetitionYear([
                    'year' => 2023,
                    'description' => <<<END
                        **I. kategorie**
                        1\. místo
                        Josef Průša

                        2\. místo
                        Martin Droppa

                        3\. místo
                        Ondrej Rákoš
                        Daniel Nerad
                        Dominika Podlešáková
                    
                        **II. kategorie**
                        1\. místo
                        Adam Suk (Česká republika)

                        2\. místo
                        Alfred Habermann (Česká republika)
                        Stanislav Mühlfait (Česká republika)
                        Monika Šnorbertová (Česká republika)

                        3\. místo
                        Miroslav Baklík (Česká republika)
                        Wei-Tsai (Tchaj-wan)
                    
                        **Porota**
                        Jan Hora (Česká republika)
                        Martin Hroch (Česká republika)
                        Romanu Perucki (Polsko)
                        END,
                ]),
            ]
        );
    }
    
    private function insertCompetition(array $data, array $organs, array $competitionYears)
    {
        $competition = new Competition($data);
        $competition->save();
        if (!empty($organs)) {
            $competition->organs()->attach($organs);
        }
        if (!empty($competitionYears)) {
            $competition->competitionYears()->saveMany($competitionYears);
        }
    }
}
