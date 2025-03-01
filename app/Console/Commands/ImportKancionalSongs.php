<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;

class ImportKancionalSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-kancional-songs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts songs into songs table';
    
    // https://kancional.cz/ke-stazeni
    //  - ručně doplněna: ordinaria aj. (viz níže)
    //  - odmazáno: 505 Eucharistie, 067a-c, 140
    const DATA = <<<EOL
        065 Litanie k nejsvětějšímu Srdci Ježíšovu
        066 Modlitba k zasvěcení Srdci Ježíšovu
        067 Litanie loretánská
        068 Litanie ke všem svatým
        069 Litanie k svatým ochráncům naší vlasti
        072 Lide můj, slyš nářek Pána svého
        073 Rozjímej, křesťane
        074 První zastavení
        075 Ježíši, tichý Beránku
        101 Ejhle, Hospodin přijde
        102 Mnozí spravedliví
        103 Vládce světa
        104 Panno blahoslavená
        105 Chvalme Boha vesele
        106 Vítej, milý Jezu Kriste
        107 Radostné to Děťátko
        108 Ty jsi, Kriste, Boží Syn
        109 Z milosti tak hojné
        110 Prolom, Pane, nebesa
        111 Všichni věrní křesťané
        112 Z hvězdy vyšlo slunce
        113 Před Pána vstupujem
        114 Zdráv buď, Pane Jezu Kriste
        115 Požehnaný, velebný
        116 Jezu, skrytý v svátosti
        117 Rodičko Boží vznešená
        121 Aj, slyšte znenadání
        122 Co již dávní proroci
        123 Ejhle, přijde Pán
        124 Jak růže krásná
        125 Sešli, nebe, Spasitele
        126 Vesele zpívejme
        127 Zavítej k nám, Stvořiteli
        128 Dej rosu, nebe nad námi
        129 Volá hlas
        130 Z nebe posel vychází
        181 Rosu dejte
        182 Hlásej píseň veselá
        183 Vítej, Jezu Kriste
        184 Vítej, hosti nebeský
        185 Slavná Matko Jezu Krista
        186 Slavná Spasitele Matko
        201 Narodil se Kristus Pán
        202 Pojďte všichni k jesličkám
        203 Zvěstujem vám radost
        204 S pastýři teď o závod
        205 Vítej nám, Bože vtělený
        206 Vítej, milý Jezu Kriste
        207 Nastal přeradostný čas
        208 Čas radosti, veselosti
        209 Splnilo se Písmo svaté
        210 V půlnoční hodinu
        211 Z nebe jsi přišel
        212 Všude radost
        213 Hvězda svítí na Betlémem
        214 Z nebeské výsosti
        215 Přijď, ó milý Spasiteli
        216 Vítej, vítej, božské Dítě
        217 Kriste, světa Spasiteli
        218 Náš Spasitel zvěstovaný
        220 Chtíc, aby spal
        221 Místo Betléma
        222 Nesem vám noviny
        223 Poslouchejte, křesťané
        224 Tichá noc
        225 Veselé vánoční hody!
        226 Vítaný buď, Ježíšku
        227 Co to znamenati má
        228 Vánoční čas nastává
        281 Poslechněte, národové,
        282 Betlémské světlo
        283 S pastýři pospíchejte
        284 Slyšte, slyšte, pastuškové
        285 Sem, pastýři, pospíchejte
        286 S chudými pastýři
        287 Poslyšte, bratři, jen
        288 Ejhle, naše chasa
        289 Co to znamená,
        290 Vzhůru, bratři milí
        291 Ježíška vítat pospěšme,
        292 Sióne, již otvírej
        301 Ach, můj nejsladší Ježíši
        302 Dokonáno jest
        303 Duše věrné
        304 Hořké muky
        305 Již jsem dost pracoval
        306 Když na kříži pněl Spasitel
        307 Kristus, příklad pokory
        308 Křesťanská duše
        309 Ó Kriste, v tichý postní den
        310 Poděkujme Kristu Pánu
        311 Matka pláče
        312 Nedejme se k spánku svésti
        313 Přistup, duše, v zkroušenosti
        314 Stála Matka
        315 Svatý Bože
        316 Svatý Bože
        317 Svatý kříž, tebe ctíme
        318 Kriste Králi, dárce spásy
        319 Já chtěl bych, Bože můj
        320 Ó slyš a shlédni k nám
        321 Ó hlavo těla církve
        322 Před tvou tváří
        382 Ó srdce kamenné
        383 Hořké Páně umučení
        384 Ó Kriste, pohlédni
        385 Kříži svatý
        386 Kriste, pro své rány
        401 Aleluja! Živ buď nad smrtí slavný vítěz
        402A Bůh všemohoucí
        402B Bůh všemohoucí
        403 Hle, vstal již z mrtvých
        404 Raduj se a vesel
        405 Rozleťte se, zpěvy, vzhůru
        406B Velikonoční oběti
        407 Vesel se, nebes Královno
        408 Vstalť jest této chvíle
        409 Základ církve pevná skála
        410 Aleluja, církev zpívá
        411 Aleluja. Zazpívej, církvi (O filii)
        412 Třetího dne vstal Stvořitel
        413 Žije! Kristus povstal z hrobu
        415 Vstoupil Pán v slávu nebeskou
        421 Kristus seslal Těšitele
        422B Přijď, ó Duchu přesvatý
        422C Svatý Duchu, sestup k nám
        423 Přijď, Tvůrce, Duchu svatý
        424 Sestup na nás, Duchu svatý
        425 Ty Boží lásko
        481 Jako vítěz
        482 Zvítězil Lev
        483 Bratři, nad svým vykoupením
        484 Pán Ježíš Kristus z mrtvých vstal
        485 Raduj se, Královno nebeská
        510A Pokrop mě yzopem
        510B Rač yzopem mě pokropit
        511A Viděl jsem pramen vody
        511B Pojď, kajícníku, před oltář
        512 Bože, před tvou velebností
        513 Hleď, mocný Hospodine,
        514 Hospodine, všech věcí Pane,
        515 Jako dítky k Otci svému
        516 Na své tváře padáme
        517 Pozdvihni se, duše, z prachu,
        518 Shlédni, Bože
        519 Buďte bdělí
        520 Hospodine všemohoucí
        521 Pane, chci být
        522 Plníme, Kriste, tvoje přání
        523 Ty jsi, Pane, v každém chrámě
        524 V posvátné úctě klekáme
        525 Všichni jsme děti
        586 Pokrop mě yzopem
        587 Viděl jsem pramen vody
        588 V zkroušenosti klekáme
        589 Na kolena padejme
        590 Tobě Bože na výsosti
        590 Tobě, Bože na výsosti
        591 Před tebou hlavu sklání
        592 V zkroušenosti srdce
        701 Andělský chlebe
        702 Ať Srdci Ježíšovu
        703 Buď slaven, mocný Králi
        704B Chvalte, ústa
        706 Jezu Kriste, štědrý kněže
        707 Ježíši, Králi
        708B Ježíši, tebe hledám
        709 K nebesům se orla vzletem
        710 K svátku tajemnému
        711 Kde jsi, Jezu, spáso má
        712 Klaním se ti vroucně,
        713 Na kolena padejme
        714 Ó pokrme pocestných
        715 Ó Srdce Páně, archo naší spásy
        716 Pojďte uctít poklonou
        717B Sióne, chval Spasitele,
        718 Skrytý Bože,
        719 Svatý, svatý,
        720 Útěcho duše mé
        721 Vítej nám, hoste přemilý
        722 Vítej, vítej, tělo přezázračné,
        723 Vzhůru, srdce,
        724 Zde nebeská je mana
        725 Zdráv buď, Chlebe,
        726 Hledám, kde bydlíš, Pane,
        727 Kde jsi, můj Ježíši
        728 Ó Srdce Páně nejsvětější,
        781 Chvalte, ústa
        783 Klaním se ti vroucně
        784 Svatá oběť začíná se
        785 Z lásky raněný Ježíši
        786 Vzpomínko sladká, Ježíši
        787 Chléb, hle jasných nebešťanů
        788 Srdce Páně, archo spásy
        789 Ó andělé, k nám dolů spějte
        790 Ó Jezu, hoste nebeský,
        791 Dárce spásy, Chlebe živý
        792 Svátosti drahá
        793 Pozdraven buď ve svátosti
        794 Ó Bože, zde se tobě klaníme
        795 Ejhle, oběť přesvatá!
        796 S poníženým k zemi čelem
        801 Budiž vděčně velebena
        802 Chválu vzdejme, ó křesťané
        803 Jako ta růžička krásná,
        804 K nebesům dnes zaleť písní
        805 Maria, Maria!
        806 Máti Páně přesvatá,
        807 Matko Boží, neslýcháno,
        808 Matko přesvatá,
        809 Ó Maria, Boží Máti,
        810 Ó Maria, útočiště naše
        811 Slyš, jaký to nad řekou
        812 Tisíckráte pozdravujem tebe
        813 Velebí má duše
        814 Zdráva buď, nebes Královno
        815 Zdráva buď, Panno Maria
        816 Zdrávas, hvězdo spanilá
        817 Zdrávas, Maria! Anděl Páně z nebe
        818 Buď zdráva, panen koruno
        819 Jakou to vůní dýchá zem
        820 Vychvalujme vesele
        821 Dělníku Boží
        822 Jak se moudrost Boží
        823 Zdráv buď, strážce
        824 Já jsem si vyvolil
        825 Jak červánky ohlašují ráno
        826 Od sítí tvých
        827 Vyvolil si apoštoly
        828 Bože, cos ráčil
        829 Ejhle, oltář
        830B Svatý Václave
        831 V zemi věrných Čechů
        832 Ještě než vzpjal se první chrám
        833 Ty, jenž jsi slavná koruna
        834 Lid český
        835 Vroucně vzýván
        836 Radostnou píseň
        838 Svatým bratřím chvály znějí
        839 K oltáři Páně
        840 Všichni Boží vyvolení
        841 Kolik svatých zdobí nebe
        842 Prolití krve
        843 Pane žní
        844 Bůh je láska
        845 Vždy žena budiž chválena
        870 Anděle Boží, strážce můj
        882 Panno blahoslavená
        883 Hvězdo jitřní
        884 Maria, tvé jméno
        885 Buď zdráva, Panno Maria
        886 Pod ochranu tvou
        887 Zdrávas, Královno
        889 Andělé Boží, modlitby
        891 Pějme píseň o Hedvice
        892 Vzývej, církvi, svého kněze Jana
        901 Blíž k tobě, Bože můj
        902 Chvalme Hospodáře všehomíra
        903 S námi je Bůh náš
        904 Tvůrce mocný
        905 Ty mocný, silný, veliký
        906 Buď Bohu chvála
        907 Chvalte Pána, chvalte
        908 Chvalte Pána, všichni lidé
        909 Nejvyšší a mocný
        910 Jeden Pán
        911 Otče náš, milý Pane
        912 Hospodine, mocný Králi
        913 Přísný soudce
        914 Bůh ti tento život dal
        916 O hojné požehnání
        917 Buď ve svátosti
        918 Bože všemocný a vlídný
        921 Bože, věrným zemřelým
        922 Matičko Kristova
        923 Ó Bože, k prosbám lidu
        924 Odpočiňte v pokoji
        925 Při tvé lásce neslýchané
        926 Zpívejme píseň naděje
        927 Dej mi, Pane, bdělé srdce
        928 Učiň mě, Pane, nástrojem
        929 Vezmi si, Pane
        930A Hospodine, pomiluj ny!
        930B Hospodine, ulituj nás
        931 Tam, kde strmí církve skála
        932A Bože, chválíme tebe
        981 Hospodine, pomiluj nás
        982 Bože chválíme tebe
        983 Mluv, Pane
        984 Panno blahoslavená, Matko milosti
        EOL
        .
        <<<EOL
        
        502 Ordinarium J. Olejníka I.
        503 Ordinarium K. Břízy
        504 Ordinarium P. Ebena
        505 Ordinarium Z. Pololáníka
        507 Ordinarium VIII - De angelis
        509 Ordinarium XI - Orbis factor
        509 Ordinarium - Missa mundi
        582 Ordinarium J. Olejníka II.
        583 Ordinarium J. Olejníka III.
        584 Staroslověnské ordinarium J. Olejníka
        585 Ordinarium missa de angelis
        219 Jdou zástupy věrných (Adéste, fidéles)
        708A Ježíši, tebe hledám
        EOL;
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (explode("\n", static::DATA) as $song) {
            $matches = [];
            preg_match('/^([a-z0-9]+) (.+)/i', $song, $matches);
            Song::create([
                'number' => trim($matches[1]),
                'name' => trim($matches[2])
            ]);
        }
    }
    
}
