<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\DispositionLanguage;
use App\Services\DispositionParser;
use App\Models\Disposition;

class DispositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ujezdAppendix = <<<END
            Rozsah manuálů C-g3, rozsah pedálu C-f1
            Celkem 1226 píšťal
            Kolektivy:  P, MF, F, FF, Tutti
            Crescendo válec, VK1, vyp. RR, vyp. jazyků, tremolo II. man., automat. anulátor pedálu u kolektivů
            END;
        $ujezdDescription = 'Jedná se o dvoumanuálové varhany  s pedálem firmy Rieger - Opus 2798, postavené v roce 1937. Ve stylové samonosné varhanní skříni jsou na sloupoví umístěny vzdušnice I. a II. manuálu, v zadní části podél stěny pedálu. Vzdušnice II. manuálu je v žaluziové skříni s vertikálními křídly. V části věže je vzduchový zásobník plovákové konstrukce s nožním foukačem, nad ním samostatný křídlový regulátor pro II. manuál.  V sousedící místnosti je umístěné vzduchové čerpadlo zn. Rieger. Vzdušnice s rejstříkovou kancelou jsou výpustné taštičkové konstrukce. Hrací stůl a traktura je tlaková s rozvodem vzduchu olověnými trubičkami. Pracovní tlak vzduchu je sice vysoký, 101 milimetrů vodního slouce, ale nástroj je velmi decentně naintonován. Středem kůru čelem k oltáři je postaven hrací stůl. Skříň varhan je zdobená plastikou anděla a zlacenými řezbami se secesními prvky. (Zdroj: http://www.farnost.velkyujezd.cz/varhany)';
        
        $this->insertDisposition(
            data: [
                'id' => 1,
                'name' => 'Velký Újezd, kostel sv. Jakuba Staršího – uspořádání podle strojů',
                'user_id' => 2,
                'organ_id' => 10,
                'keyboard_numbering' => true,
                'numbering' => false,
                'language' => DispositionLanguage::Czech,
                'appendix' => $ujezdAppendix,
                'description' => $ujezdDescription,
            ],
            dispositionText: <<<END
                I. manuál
                Bourdon 16'
                Principál 8'
                Gamba 8'
                Oktáva 4'
                Flétna trubicová 2'
                Mixtura 4x
                I/I 4'
                II/I 16'
                II/I 8'
                II/I 4'
            
                II. manuál
                Kryt jemný 8'
                Vox coelestis 8'
                Flétna koncertní 8'
                Principál italský 4'
                Roh noční 2'
                Cymbál 3x
                Hoboj 8'
                II/II 16'
                II/II 4'
            
                Pedál
                Oktávbas 8'
                Cello  8'
                Subbass 16'
                Bourdonbas 16'
                I/P 8'
                II/P 8'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 2,
                'name' => 'Velký Újezd, kostel sv. Jakuba Staršího – uspořádání podle hracího stolu',
                'user_id' => 2,
                'organ_id' => 10,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => $ujezdAppendix,
                'description' => $ujezdDescription,
            ],
            dispositionText: <<<END
                Pedál
                Oktávbas 8'
                Cello 8'
                Subbass 16'
                Bourdonbas 16'
            
                I. manuál
                Bourdon 16'
                Mixtura 4x
                Flétna trubicová 2'
                Oktáva 4'
                Principál 8'
                Gamba 8'
            
                Spojky
                I/I 4'
                II/II 16'
                II/II 4'
                II/I 16'
                II/I 4'
                II/I 8'
                II/P 8'
                I/P 8'
            
                II. manuál
                Kryt jemný 8'
                Vox coelestis 8'
                Flétna koncertní 8'
                Principál italský 4'
                Roh noční 2'
                Cymbál 3x
                Hoboj 8'
                END);
        
        // TODO: spojky staré dispozice - jak přesně jsou?
        // TODO: Trinuna není kategorizována
        $this->insertDisposition(
            data: [
                'id' => 3,
                'name' => 'Olomouc, kostel sv. Mořice – původní stav (1745)',
                'organ_id' => 1,
                'keyboard_numbering' => false,
                'numbering' => false,
                'language' => DispositionLanguage::German,
                'appendix' => 'Pořadí a názvy rejstříků byly kvůli snadnému porovnání přizpůsobeny současné dispozici.',
                'description' => 'Zdroj: Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 158)',
            ],
            dispositionText: <<<END
                I. Unterwerk
                Principal 8'
                Flaut amabile 8'
                Unda maris 1x 8'
                Oktave 4'
                Flaut minor 4'
                Trinuna 4'
                Spitzflaut 2 2/3'
                Superoktave 2'
                Mixtur 4x 1 1/3'
            
                II. Hauptwerk
                Principal 16'
                Bourdonflaut 16'
                Salicet 8'
                Principal 8'
                Flaut major 8'
                Gemshorn 8'
                Gamba 8'
                Oktave 4'
                Nachthorn 4'
                Quinte 2 2/3'
                Cimbel 2x 2'
                Mixtur 6x 2'
                Trompete 8'
                I/II 8'
                III/II 8'
            
                III. Oberwerk
                Principal 8'
                Flaut allemand 8'
                Rohrflaut 8'
                Quintadena 8'
                Oktave 4'
                Quinte 2 2/3'
                Superoktave 2'
                Mixtur 4x 1 1/3'
                Vox humana 8'
            
                Pedal
                Maiorbass 32'
                Principal 16'
                Offenerbass 16'
                Subbass 16'
                Quintadenbass 16'
                Oktavenbass 8'
                Gemshornquinte 5 1/3'
                Mixtur 6x 4'
                Posaunenbass 16'
                Trombabass 8'
            
                Cammer Thon Basse
                Subbass 16'
                Quintadenbass 16'
                Octavenbass 8'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 4,
                'name' => 'Olomouc, kostel sv. Mořice – současný stav (2023)',
                'organ_id' => 1,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::German,
                'appendix' => <<<END
                    Volné kombinace: A, B, C, D, E + Setzerkomb.
                    Cresc. válec 4 varianty.
                    Samostatné vypínače jednotlivých jazyk. rejstříků. MIDI + Computer
                    END,
                'description' => 'Stav dispozice po rozšíření varhan o 2 manuály v 60. letech 20. století a po drobných úpravách provedených při generální opravě v r. 2023. Zdroj: https://varhany.moric-olomouc.cz/dispozice',
            ],
            dispositionText: <<<END
                I. Unterwerk
                Handregisterfeststeller
                Principal 8'
                Flaut amabile 8'
                Unda maris 1x 8'
                Oktave 4'
                Flaut minor 4'
                Trinuna 4'
                Spitzflaut 2 2/3'
                Superoktave 2'
                Mixtur 4x 1 1/3'
                Glocken I.
                Glocken II.
                III/I 8'
                IV/I 8'
                V Sp / I 8'
                V Bw / I 8'

                II. Hauptwerk
                Hauptwerk ab
                Principal 16'
                Bourdonflaut 16'
                Salicet 8'
                Principal 8'
                Flaut major 8'
                Gemshorn 8'
                Gamba 8'
                Oktave 4'
                Nachthorn 4'
                Quinte 2 2/3'
                Cimbel 2x 2'
                Mixtur 6x 2'
                Trompete 8'
                I/II 8'
                III/II 8'
                IV/II 8'
                V Sp / II 8'
                V Bw / II 8'

                III. Oberwerk
                Principal 8'
                Flaut allemand 8'
                Rohrflaut 8'
                Quintadena 8'
                Oktave 4'
                Quinte 2 2/3'
                Superoktave 2'
                Mixtur 4x 1 1/3'
                Vox humana 8'
                Glocken I.
                Glocken II.
                Echo
                IV / III 8'
                V Sp / III 8'
                V Bw / III 8'
                Tremolo AW
                Handregister ab
                Walze ab
                Zungen ab (general)
                Man. 16, und Ped. 32, ab
                Mixturen ab

                IV. Schwellwerk
                Spitzgedact 16'
                Weitprincipal 8'
                Rohrgedackt 8'
                Harfpfeife 8'
                Vox angelica 3x 8'
                Kupferoktave 4'
                Spillflöte 4'
                Rohrquintatön 4'
                Nachthornquinte 2 2/3'
                Quintadecima 2'
                Waldflöte 2'
                Koppelflötenterz 1 3/5'
                Querflöte 1'
                Farbenmixtur 3-4x
                Mixtur 6-7x
                Quintzimbel 3x 1/4'
                Bassethorn 16'
                Franz. Trompete 8'
                Rohrschalmei 8'
                Geigendregal 8'
                Clairon 4'
                Glocken I
                Glocken II
                V Sp / IV 8'
                V Bw / IV 8'
                Tremolo IV

                V. Schwellpositiv
                Schwellpositiv ab
                Gedact 8'
                Trichterprincipal 4'
                Blockflöte 4'
                Kleinprincipal 2'
                Quinte 1 1/3'
                Schwegel 1'
                Sesquialtera 2-3x 2 2/3'
                Scharf 5x 1'
                Krummhorn 8'
                Singend Kornett 4'
                Tremolo V Sp

                V. Bombardenwerk
                Bombardenwerk ab
                Holzprincipal 8'
                Solokornett 4-6x 4'
                Principalmixtur 8x 4'
                Trompete 16'
                Trompete (horizont.) 8'
                Trompete (horizont.) 4'

                Pedal (Neues Werk)
                Pedal NW ab
                Holzprincipal 16'
                Gedackpommer 16'
                Grossnasat 10 2/3'
                Kupferprincipal 8'
                Bleikoppelflöte 8'
                Choralbass 4'
                Rohrpfeife 4'
                Russischhorn 2'
                Rauschbass 5x 5 1/3'
                Mixtur 6x 2'
                Bombarde 16'
                Trompete 8'
                Kopftrompete 4'
                Zink 2'
                Akustik 21 1/3'
                IV/P 8'
                V Sp / P 8'
                V Bw / P 8'

                Pedal (Altes Werk)
                Pedal AW ab
                Maiorbass 32'
                Principal 16'
                Offenerbass 16'
                Subbass 16'
                Quintadenbass 16'
                Oktavenbass 8'
                Gemshornquinte 5 1/3'
                Mixtur 6x 4'
                Contraposaune 32'
                Posaunenbass 16'
                Trombabass 8'
                Clarino 4'
                I/P 8'
                II/P 8'
                III/P 8'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 5,
                'name' => 'Olomouc, bazilika Navštívení Panny Marie (Svatý Kopeček) – současný stav (1998)',
                'organ_id' => 50,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rozsah manuálů C-g3, rozsah pedálu C-f1
                    Pomocná zařízení: Tremolo I. man., Tremolo II. man.'
                    END,
                'description' => 'Seskvialtera obsahuje septimu 1 1/7\'. Oktávbas 8\' je extenze rejstříku Principálbas 16\'. Zdroj: http://www.svatykopecek.cz/FixPage.asp?ID=112',
            ],
            dispositionText: <<<END
                I. manuál
                Principál 8'
                Kryt 8'
                Roh kamzičí 8'
                Oktáva 4'
                Roh noční 4'
                Kvinta 2 2/3'
                Superoktáva 2'
                Seskvialtera 2-3x 2 2/3'
                Mixtura 3-4x 1 1/3'
                Cimbál 3x 1/2'

                II. manuál
                Flétna trubicová 8'
                Salicionál 8'
                Principál 4'
                Nasard 2 2/3'
                Flétna lesní 2'
                Tercie 1 3/5'
                Kvinta 1 1/3'
                Mixtura 3x 1'

                Pedál
                Principálbas 16'
                Subbas 16'
                Oktávbas 8'
                Flétna krytá 8'
                Kvintbas 5 1/3'
                Superoktáva 4'
                Pozoun 16'
            
                Spojky
                II/I
                I/P
                II/P
                END);
        
        $domAppendix = <<<END
            Rozsah manuálů C-g3, rozsah pedálu C-f1
            Rohrwerk Koppel (připíná jazyky)
            Kolektivy: Mezzoforte I., Forte I., Mezzoforte II., Forte II., Forte III., Forte Pedal, Fortissimo volles Werk
            Crescendo
            Žaluzie pro Vox humanu
            Tremolo pro Vox humanu
            END;
        $domDescription = 'Zdroj: Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 154)';
        
        $this->insertDisposition(
            data: [
                'id' => 6,
                'name' => 'Olomouc, katedrála sv. Václava – velké varhany – uspořádání podle strojů',
                'organ_id' => 54,
                'keyboard_numbering' => false,
                'numbering' => false,
                'language' => DispositionLanguage::German,
                'appendix' => $domAppendix,
                'description' => $domDescription,
            ],
            dispositionText: <<<END
                I. manuál
                Principal 16'
                Bourdon 16'
                Principal 8'
                Bourdon 8'
                Fugara 8'
                Gemshorn 8'
                Concertflöte 8'
                Dolce 8'
                Quinte 5 1/3'
                Octave 4'
                Flute dulce 4'
                Fugara 4'
                Salicet 4'
                Octave 2'
                Cornet 3-5x 2 2/3'
                Mixtur 5x 4'
                Trompete 8'
                II/I
                III/I
            
                II. manuál
                Bourdon 16'
                Prinzipal 8'
                Bourdon 8'
                Gamba 8'
                Salicional 8'
                Flûte Harmonique 8'
                Spitzflöte 8'
                Quintatöna 8'
                Octave 4'
                Dolce 4'
                Gemshorn 4'
                Octave 2'
                Mixtur 4x 2 2/3'
                Basson-Oboe 8'
            
                III. manuál
                Lieblich Gedeckt 16'
                Geigenprinzipal 8'
                Zartflöte 8'
                Violine 8'
                Aeoline 8'
                Octave 4'
                Flauto traverse 4'
                Flautino 2'
                Vox humana 8'
            
                Pedál
                Bourdon 32'
                Prinzipalbass 16'
                Violon 16'
                Subbass 16'
                Bourdon 16'
                Quintbass 10 2/3'
                Octavbass 8'
                Cello 8'
                Bombarde 32'
                Posaune 16'
                I/P
                II/P
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 7,
                'name' => 'Olomouc, katedrála sv. Václava – velké varhany – uspořádání podle hracího stolu',
                'organ_id' => 54,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::German,
                'appendix' => $domAppendix,
                'description' => $domDescription,
            ],
            dispositionText: <<<END
                Pedál - levá strana
                Vacant
                Prinzipalbass 16'
                Bourdon 32'
                Violon 16'
                Subbass 16'
                Bourdon 16'
                Quintbass 10 2/3'
            
                I. manuál - levá strana
                Principal 16'
                Bourdon 16'
                Concertflöte 8'
                Fugara 8'
                Gemshorn 8'
                Bourdon 8'
                Dolce 8'
            
                II. manuál - levá strana
                Bourdon 16'
                Bourdon 8'
                Flûte Harmonique 8'
                Spitzflöte 8'
                Quintatöna 8'
                Gamba 8'
                Salicional 8'
                
                III. manuál - levá strana
                Calcantenruf
                Lieblich Gedeckt 16'
                Violine 8'
                Zartflöte 8'
                Aeoline 8'
                Geigenprinzipal 8'
            
                Pedál - pravá strana
                Bombarde 32'
                Posaune 16'
                Cello 8'
                Octavbass 8'
            
                I. manuál - pravá strana
                Octave 2'
                Cornet 3-5x 2 2/3'
                Mixtur 5x 4'
                Principal 8'
                Trompete 8'
                Quinte 5 1/3'
                Octave 4'
                Flute dulce 4'
                Fugara 4'
                Salicet 4'
            
                II. manuál - pravá strana
                Prinzipal 8'
                Basson-Oboe 8'
                Dolce 4'
                Gemshorn 4'
                Octave 4'
                Octave 2'
                Mixtur 4x 2 2/3'
            
                III. manuál - pravá strana
                Vox humana 8'
                Flauto traverse 4'
                Octave 4'
                Flautino 2'
                Tremolo
                Rohrwerk Koppel
            
                Spojky
                II/I
                III/I
                I/P
                II/P
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 8,
                'name' => 'Olomouc, katedrála sv. Václava – chorální varhany',
                'organ_id' => 121,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rozsah manuálů C-g3, rozsah pedálu C-f1
                    Tremolo II. man. je s elektrickým řízením kmitů
                    Žaluzie II. man.
                    Rejstříkové crescendo + Vypínač
                    Volné kombinace typu Setzer (mechanické) A, B, C, D, Pl, T, Vypínač
                    Tónová traktura mechanická (manuály) a elektrická (pedál)
                    Rejstříková traktura mechanicko-elektrická
                    END,
                'description' => 'Zdroj: LYKO, Petr. Varhanářská firma Rieger. Online. Disertační práce. Ostrava: Ostravská univerzita, Pedagogická fakulta. 2018. Dostupné z: https://theses.cz/id/ev7b8g/ (str. 91)',
            ],
            dispositionText: <<<END
                I. Manuál
                Kvintadéna 16'
                Principál 8'
                Flétna kopulová 8'
                Salicionál 8'
                Oktáva 4'
                Flétna dutá 4'
                Flétna příčná 2'
                Mixtura 5x 2'
                Trompeta 8'
                II/I
            
                II. Manuál
                Kryt 8'
                Principál 4'
                Flétna trubicová 4'
                Oktáva 2'
                Kvinta 1 1/3'
                Seskvialtera 2-3x 2 2/3'
                Akuta 4x 1'
                Šalmaj regál 8'
                Tremolo
            
                Pedál
                Subbas 16'
                Principál 8'
                Flétna krytá 8'
                Chorál 4'
                Mixtura 4x 5 1/3'
                Pozoun 16'
                I/P
                II/P
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 9,
                'name' => 'Olomouc, kostel Panny Marie Sněžné – původní stav (1730)',
                'organ_id' => 49,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::German,
                'appendix' => <<<END
                    Pořadí rejstříků bylo kvůli snadnému porovnání přizpůsobeno současné dispozici.'.
                    Rozsah manuálů C-c3, rozsah pedálu C-a
                    END,
                'description' => 'Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (2. díl, str. 215)',
            ],
                
            // upraveno: Sallicional -> Salicional
            // Quinta 2x: ponecháno 2x, ačkoli v Sehnalovi není
            dispositionText: <<<END
                I. Hlavní stroj
                Principal 8'
                Quintadena 8'
                Viola de Gamba 8'
                Salicional 8'
                Octava 4'
                Flauta 4'
                Sesquialtera 2x 2 2/3'
                Quinta 2 2/3'
                Superoctava 2'
                Mixtura 6x 2'
                Rauschquinta 2x 1 1/3'
                Cimbal 4x 1'
                
                II. Positiv
                Copl major 8'
                Principal 4'
                Copl minor 4'
                Fugara 4'
                Superoctava 2'
                Quinta 2x 1 1/3'
                Mixtura 3x
                
                Pedál
                Principal Bass 16'
                Sub Bass 16'
                Octav Bass 8'
                Quinta Bass 5 1/3'
                Superoctava 4'
                Cornet Bass 4x 8'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 10,
                'name' => 'Olomouc, kostel Panny Marie Sněžné – současný stav (1977)',
                'organ_id' => 49,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::German,
                'appendix' => <<<END
                    Rozsah manuálů C-g3, rozsah pedálu C-f1
                    Tremolo II. manuálu
                    END,
                'description' => 'Současný stav po restaurování firmou Rieger-Kloss v r. 1977, kdy byly odstraněny nestylové zásahy z počátku 20. století a dispozice byla navrácena do téměř původní podoby. Rovněž byl rozšířen tónový rozsah manuálů i pedálu. Zdroj: Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 156)',
            ],
            // upraveno: Sallicional -> Salicional
            dispositionText: <<<END
                I. Hlavní stroj
                Principal 8'
                Flauta major 8'
                Quintadena 8'
                Viola de Gamba 8'
                Salicional 8'
                Octava 4'
                Flauta minor 4'
                Sesquialtera 2x 2 2/3'
                Quinta 2 2/3'
                Superoctava 2'
                Mixtura 6x 2'
                Rauschquinta 2x 1 1/3'
                Cimbal 4x 1'
                II/I
                
                II. Positiv
                Copl major 8'
                Principal 4'
                Copl minor 4'
                Fugara 4'
                Quintflauta 2 2/3'
                Octava 2'
                Quinta 2x 1 1/3'
                Mixtura 3x
                
                Pedál
                Principal Bass 16'
                Sub Bass 16'
                Octav Bass 8'
                Portunal Bass 8'
                Quinta Bass 5 1/3'
                Superoctava 4'
                Cornet Bass 4x 8'
                I/P
                II/P
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 11,
                'name' => 'Praha, kostel Matky Boží před Týnem (Staré Město)',
                'organ_id' => 73,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::German,
                'appendix' => <<<END
                    přetahovací manuálová spojka
                    END,
                'description' => 'Zdroj: http://www.tyn.cz/cz/index.php?stranka=tnsk-varhany',
            ],
            dispositionText: <<<END
                I. Hlavní stroj
                Bourdon Flauta 16'
                Principal 8'
                Copula major 8'
                Flauta dulcis 8'
                Quintatöne 8'
                Salicional 8'
                Octava 4'
                Copula minor 4'
                Quinta major 2 2/3'
                Superoctava 2'
                Quinta minor 1 1/3'
                Sedecima 1'
                Mixtur 6x 1'
                Cembalo 4x

                II. Pozitiv
                Copula major 8'
                Principal 4'
                Copula minor 4'
                Octava 2'
                Quinta 1 1/3'
                Quinta decima 1'
                Rauschquinta 2x
                Mixtura 3x 1'
                Cymbelstern I
                Cymbelstern II

                Pedál
                Subbass offen 16'
                Subbass gedeckt 16'
                Octavbass 8'
                Quinta 5 1/3'
                Superoctav 4'
                Mixtur 3x 2 2/3'
                Posaunbass 8'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 12,
                'name' => 'Příbram, kostel Nanebevzetí Panny Marie (Svatá Hora)',
                'organ_id' => 97,
                'keyboard_numbering' => true,
                'numbering' => false,
                'language' => DispositionLanguage::German,
                'appendix' => <<<END
                    Rozsahy manuálů C - f3, rozsah pedálu C-f1
                    Rejstříky Spodního stroje děleny na bas a diskant mezi h0-c1
                
                    Spojky UW/HW a POS/HW přetahováním manuálů
                    HW/PED a UW/PED rejstříkovým táhlem

                    Traktura (i rejstříková) mechanická
                    Ladění: a1 = 440 Hz při 18°C, ~ Valotti
                    Zásuvkové vzdušnice

                    END,
                'description' => 'Zdroj: http://jaroslavtuma.cz/wp-content/uploads/2018/10/svata_hora_dispozice_varhan.pdf',
            ],
            dispositionText: <<<END
                I. UW - Spodní stroj
                Gamba 8'
                Portunal 8'
                Fugara 4'
                Principal 4'
                Mixtura 2-3x
                Oboe 8'
                Tremulant

                II. HW - Hlavní stroj
                Principal 8'
                Bifara 8'
                Bourdon 8'
                Quintadena 8'
                Octava 4'
                Violeta 4'
                Quinta 2 2/3'
                Nassat 2 2/3'
                Super Octava 2'
                Quinta Minor 1 1/3'
                Tertia 1 3/5'
                Mixtura 3x
                Cimbal 3x
                Tromba 8'

                III. POS - Positiv
                Copula Major 8'
                Flauta Minor 4'
                Principal 2'
                Flauto Soprano 2'
                Sedecima 1'
                Mixtura 2x
                Vox Humana 8'
                Tremulant

                Pedál
                Sub Bass 16'
                Octav Bass 8'
                Super Octav Bass 4'
                Trombone 16'
                Zimbelstern (2x)
                Kornett Bass 3x 4'
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 13,
                'name' => 'Praha, kostel sv. Petra a Pavla (Vyšehrad)',
                'organ_id' => 82,
                'keyboard_numbering' => true,
                'numbering' => false,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rozsahy manuálů C-f3, rozsah pedálu C-d1
                    Spojky: I/P, II/P, III/P, II/I, III/I, III/II, III/I 4', II/I 4',  III/I 16',  III/ II 16'
                    Jedna volná kombinace.
                    Pevné kombinace: PP, P, MF, F, FF, PL, Tutti II.man., Tutti III.man, Piano Pedál, Mezzoforte Pedál
                    Crescendový válec
                    END,
                'description' => 'Zdroj: https://bazilika.kkvys.cz/cs/o-bazilice/vysehradske-varhany',
            ],
            dispositionText: <<<END
                I. Manuál
                Principál 16'
                Bordun 16'
                Principál 8'
                Kryt 8'
                Flétna dvojitá 8'
                Roh kamzičí 8'
                Gamba 8'
                Oktáva 4'
                Flétna 4'
                Fugara 4'
                Kvinta 2x 2'
                Cornet 3x 8'
                Mixtura 5x 5 1/3'
                Trompeta 8'

                II. Manuál
                Kvintadena 16'
                Principál 8'
                Kryt 8'
                Flétna dutá 8'
                Flétna trubicová 8'
                Fugara 8'
                Viola d'amour 8'
                Salicionál 8'
                Oktáva 4'
                Flétna 4'
                Mixtura 5x 2 2/3'
                Klarinet 8'

                III. Manuál
                Kryt jemný 16'
                Salicet 16'
                Principál 8'
                Principál 8'
                Kryt líbezný 8'
                Flétna harmonická 8'
                Aeolina 8'
                Vox coelestis 8'
                Oktáva 4'
                Flétna příčná 4'
                Viola 4'
                Kvinta flétnová 2 2/3'
                Flageolet 2'
                Oboe 8'

                Pedál
                Principálbas 16'
                Violon 16'
                Subbas 16'
                Subbas 16'
                Kryt tichý 16'
                Salicet 16'
                Oktávbas 8'
                Bourdun 8'
                Cello 8'
                Posona 16'
                Trompeta 8'
                END);
                
        $this->insertDisposition(
            data: [
                'id' => 14,
                'name' => 'Uničov, klášterní kostel Povýšení sv. Kříže',
                'organ_id' => 109,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rozsahy manuálů C-c4, rozsah pedálu C-g1
                    Tónová traktura: mechanická
                    Rejstříková traktura: elektrická, 53 znějících rejstříků, 3107 hrajících píšťal
                    Ladění: rovnoměrné temperované, komorní A - 442Hz/20°
                    Spojky: III/I – 16', III/I – 8', III/II – 8', II/I – 8'
                    I/P – 8', II/P – 8', III/P – 8', III/P – 4'
                    Volné kombinace: 8x – A,B,C,D,E,F,G,H (E-H mimo hrací stůl)
                    Pedálové kombinace: 2x – PK1, PK2
                    Pevné kombinace: 3x – Pleno 1, Pleno2, Tutti
                    Rejstříkové cresscendo: válec
                    Žaluzie pro III. manuál: mechanická, balanční šlapka
                    Vypínače: cresscenda, jazykových rejstříků generální, jazykových rejstříků jednotlivých
                    16'manuál rejstříků, mixtur, spojek z cresscenda
                    Zapínač ručních rejstříků do volných kombinací a plena
                    Violino coelestis od c0.
                    END,
                'description' => 'Zdroj: https://rkfunic.wbs.cz/pictures/varhany/varhany_jinde/klaster/varhany_v_ks_unicov.pdf',
            ],
            dispositionText: <<<END
                Pedál
                Burdon velký 32'
                Bas otevřený 16'
                Subbas 16'
                Burdon 16'
                Oktávbas 8'
                Flétna basová 8'
                Chorálbas 4'
                Flétna kopulová 4'
                Flétna zobcová 2'
                Mixtura 5x 2 2/3'
                Pozoun 16'
                Fagot 16'
                Trubka 8'
                Trubka 4'
                I/P 8'
                II/P 8'
                III/P 8'
                III/P 4'

                I. Manuál
                Principál 16'
                Principál 8'
                Flétna dřevěná 8'
                Gamba špičatá 8'
                Oktáva 4'
                Flétna harmonická 4'
                Kvinta 2 2/3'
                Oktáva špičatá 2'
                Mixtura 6x 1 1/3'
                Fagot 16'
                Trompeta 8'
                III/I 16'
                III/I 8'
                II/I 8'
                Spojky z cresc
                Vypínač cresc
                Vypínač jazyků

                II. Manuál
                Burdon 16'
                Principal 8'
                Kryt 8'
                Kvintadena 8'
                Viola 8'
                Principál 4'
                Flétna krytá 4'
                Oktáva 2'
                Kvinta 1 1/3'
                Seskvialtera 2-3x 2/3'
                Akuta 4x 1'
                Roh křivý 8'
                Trubka 4'
                III/II 8'
                Tremolo
                Vypínač 32'ped 16'man
                Vypínač mixtur
                Zapínač ruč. rejstř.

                III. Manuál
                Principál dřevěný 8'
                Flétna trubicová 8'
                Salicionál 8'
                Violino coelestis 8'
                Oktáva 4'
                Roh noční 4'
                Nasard 2 2/3'
                Flétna lesní 2'
                Tercie 1 3/5'
                Piccolo 1'
                Mixtura 5x 2'
                Hoboj 8'
                Dulcián 16'
                Trubka 8'
                Trubka 4'
                Tremolo
                END);
                
        $this->insertDisposition(
            data: [
                'id' => 15,
                'name' => 'Olomouc, kostel Panny Marie Pomocnice křesťanů (Hodolany)',
                'organ_id' => 122,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rozsahy manuálů C-g3, rozsah pedálu C-f1
                    Tremolo pro II. manuál, Anulátor pedálu, Setzer volné
                    kombinace 128x8, sekvencér Sq + a – jako tlačítko i piston,
                    Crescendo válec s vypínačem, Vypínač jazyků, Tutti
                    Tlak vzduchu: 92mmVs
                    Počet znějících píšťal: 2175 ze dřeva (Lg), cínoolověné
                    slitiny (Sn) a zinku (Zn)
                    Traktura: elektrická, hrací stůl s výbavou fy Peterson
                    Vzdušnice: kuželkové a skříňové, osazeno elektromagnety
                    Měchy: plovákové s napojeným elektroventilátorem
                    END,
                'description' => 'Zdroj: https://www.hodolany-farnost.cz/wp-content/uploads/2020/10/Varhany_Hodolany_01.pdf',
            ],
            dispositionText: <<<END
                I. manuál
                Principál rohový 16'
                Principál 8'
                Flétna 8'
                Kryt 8'
                Gamba 8'
                Salicionál 8'
                Oktáva 4'
                Flétna trubicová 4'
                Violina 4'
                Kvinta 2 2/3'
                Superoktáva 2'
                Kornett 5x 2 2/3'
                Mixtura 5x 2'
                Trompeta 8'

                II. manuál
                Bourdon 16'
                Principál houslový 8'
                Kryt jemný 8'
                Aeolina 8'
                Vox coelestis 8'
                Fugara 4'
                Flétna příčná 4'
                Nasat 2 2/3'
                Piccolo 2'
                Tercie 1 3/5'
                Harmonia aetherea 4x 2 2/3'
                Klarinet 8'

                Pedál
                Kontrabass 32'
                Principálbass 16'
                Subbass 16'
                Bourdonbass 16'
                Violonbass 16'
                Kvintbass 10 2/3'
                Oktávbass 8'
                Krytbass 8'
                Flétnabass 8'
                Violoncello 8'
                Viola 4'
                Pozoun 16'
            
                Pomocná zařízení
                II/I 16'
                II/I 8'
                II/I 4'
                I/P 8'
                II/P 8'
                Tremolo pro II. manuál
                Anulátor pedálu
                Vypínač crescenda
                Vypínač jazyků
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 16,
                'name' => 'Olomouc, kostel svatého Cyrila a Metoděje (Hejčín) – původní stav (1931)',
                'organ_id' => 123,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Vzdušnice kuželková, traktura pneumatická
                    END,
                'description' => 'Zdroj: https://www.farnost-olomouc-hejcin.cz/images/historie/chram_sv_cyril_metodej_olomouc_hejcin_varhany.pdf',
            ],
            dispositionText: <<<END
                Pedál
                Flétna basová 4'
                Oktávbas 8'
                Cello 8'
                Principálbas 16'
                Violonbas 16'
                Subbas 16'
                Bourdonbas 16'

                I. manuál
                Principál 16'
                Mikstura 5x
                Oktáva 2'
                Oktáva 4'
                Flétna oktávová 4'
                Principál 8'
                Gamba 8'
                Roh kamzičí 8'
                Flétna dutá 8'
                Kryt 8'
                Salicionál 8'

                Spojky
                I/I 4'
                II/II 16'
                II/II 4'
                II/I 16'
                II/I 4'
                II/I 8'
                I/P 8'
                II/P 8'

                II. manuál
                Eolina 8'
                Vox celestis 8'
                Kryt jemný 8'
                Flétna harmonická 8'
                Principál flétnový 8'
                Flétna trubicová 4'
                Prestant 4'
                Nasard 2 2/3'
                Flageolet 2'
                Bourdon 16'
                Hoboj 8'
                END);
                
        $this->insertDisposition(
            data: [
                'id' => 17,
                'name' => 'Olomouc, kostel svatého Cyrila a Metoděje (Hejčín) – současný stav (2020)',
                'organ_id' => 123,
                'keyboard_numbering' => false,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Vzdušnice kuželková, traktura elektropneumatická
                    END,
                'description' => 'Zdroj: https://www.farnost-olomouc-hejcin.cz/images/historie/chram_sv_cyril_metodej_olomouc_hejcin_varhany.pdf',
            ],
            dispositionText: <<<END
                Pedál
                Pozoun 16'
                Kontrabas 32'
                Flétna basová 4'
                Oktávbas 8'
                Cello 8'
                Principálbas 16'
                Violonbas 16'
                Subbas 16'
                Bourdonbas 16'

                I. manuál
                Trompeta 8'
                Cornet 5x
                Principál 16'
                Mikstura 5x
                Oktáva 2'
                Oktáva 4'
                Flétna oktávová 4'
                Principál 8'
                Gamba 8'
                Roh kamzičí 8'
                Flétna dutá 8'
                Kryt 8'
                Salicionál 8'

                Spojky
                I/I 4'
                II/II 16'
                II/II 4'
                II/I 16'
                II/I 4'
                II/I 8'
                I/P 8'
                II/P 8'

                II. manuál
                Eolina 8'
                Vox celestis 8'
                Kryt jemný 8'
                Flétna harmonická 8'
                Principál flétnový 8'
                Flétna trubicová 4'
                Prestant 4'
                Nasard 2 2/3'
                Flageolet 2'
                Mikstura 4x
                Bourdon 16'
                Hoboj 8'
                Harfa
                END);
        
        $this->insertDisposition(
            data: [
                'id' => 18,
                'name' => 'Olomouc, ZUŠ Žerotín',
                'user_id' => 2,
                'organ_id' => 11,
                'keyboard_numbering' => true,
                'numbering' => true,
                'language' => DispositionLanguage::Czech,
                'appendix' => <<<END
                    Rieger-Kloss, opus 3450
                    Rozsah manuálů C-a3, rozsah pedálu C-f1
                    Pevné kombinace Pleno, Tutti
                    Volné kombinace A, B
                    Žaluzie I., Žaluzie III.
                    Crescendo, Vypínač crescenda, Vypínač spojek z crescenda
                    END,
                'description' => null,
            ],
            dispositionText: <<<END
                I. manuál
                Flétna dutá 8'
                Salicionál 8'
                Principál 4'
                Nasard 2 2/3'
                Flétna lesní 2'
                Mixtura 4x 1 1/3'
                II/I 8'
                III/I 8'
                III/I 16'
            
                II. manuál
                Kryt 8'
                Kvintadena 4'
                Principál 2'
                III/II 8'
            
                III. manuál
                Flétna trubicová 8'
                Chvění houslové 2x 8'
                Flétna zobcová 4'
                Oktáva 2'
                Seskvialtera 2x 1 1/3'
                Akuta 4x 1'
                Tremolo II+III
            
                Pedál
                Subbas 16'
                Principál 8'
                Burdon 8'
                Oktáva 4'
                I/P 8'
                II/P 8'
                III/P 8'
                END);
    }
    
    private function insertDisposition(array $data, string $dispositionText)
    {
        $disposition = new Disposition($data);
        $disposition->save();
        
        $parser = new DispositionParser($dispositionText, $data['language'], $data['keyboard_numbering'] ?? true);
        $parser->saveInto($disposition);
    }
}
