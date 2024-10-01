<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organ;
use App\Models\OrganRebuild;
use App\Enums\Region;
use App\Enums\OrganCategory;

class OrganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->insertOrgan(
            data: [
                'id' => 1,
                'place' => 'kostel sv. Mořice',
                'municipality' => 'Olomouc',
                'latitude' => 49.5951461,
                'longitude' => 17.2512853,
                'region_id' => Region::Olomoucky,
                'importance' => 10,
                'organ_builder_id' => 9,
                'year_built' => 1745,
                'stops_count' => 94,
                'manuals_count' => 5,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/5/52/Kostel_svat%C3%A9ho_Mo%C5%99ice_v_Olomouci.jpg',
                'outside_image_credits' => 'Jan Jeništa, CC BY-SA 4.0 <https://creativecommons.org/licenses/by-sa/4.0>, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Kostel_svateho_Morice_varhany_%28retouched%29.jpg/640px-Kostel_svateho_Morice_varhany_%28retouched%29.jpg',
                'image_credits' => 'Michal Maňas , Public domain, via Wikimedia Commons',
                'perex' => 'Vzácné barokní varhany, označované jako "královna moravských varhan", jsou po rozšíření v 60. letech 20. století považovány za největší nástroj na území ČR co do počtu píšťal.',
                'description' => 'Nástroj patří mezi největší a nejvýznamnější u nás postavené barokní varhany. Po opravě a rozšíření v 60. letech 20. století jsou varhany považovány za největší co do počtu píšťal v ČR. Varhany hostí renomovaný varhanní festival s dlouholetou tradicí.',
                'web' => <<<END
                    https://varhany.moric-olomouc.cz/
                    http://www.anatomie-varhan.cz/texty/varhany/nastroje/olomouc/olomouc.htm
                    END,
                'literature' => <<<END
                Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 158)
                END,
                'disposition' => <<<END
                    **I. man. (dolní stroj - Unterwerk)**
                    1\. Handregisterfeststeller
                    2. Principal 8'
                    3. Flaut amabile 8'
                    4. Unda maris 1 X 8'
                    5. Oktave 4'
                    6. Flaut minor 4'
                    7. Trinuna 4'
                    8. Spitzflaut 2 2/3'
                    9. Superoktave 2'
                    10. Mixtur 4 X 11/3'
                    11. Glocken I.
                    12. Glocken II.
                    13. III - I 8'
                    14. IV - I 8'
                    15. V Sp - I 8'
                    16. V Bw - I 8'

                    **II. man. (hlavní stroj - Hauptwerk)**
                    17. Hauptwerk ab
                    18. Principal 16'
                    19. Bourdonflaut 16'
                    20. Salicet 16'
                    21. Principal 8'
                    22. Flaut major 8'
                    23. Gemshorn 8'
                    24. Gamba 8'
                    25. Oktave 4'
                    26. Nachthorn 4'
                    27. Quinte 2 2/3 '
                    28. Cimbel 2 X 2'
                    29. Mixtur 6 X 2'
                    30. Trompete 8'
                    31. I - II 8'
                    32. III - II 8'
                    33. IV - II 8'
                    34. V Sp - II 8'
                    35. V Bw - II 8'

                    **III. man. (horní stroj - Oberwerk)**
                    36. Principal 8'
                    37. Flaut allemand 8'
                    38. Rohrflaut 8'
                    39. Quintadena 8'
                    40. Oktave 4'
                    41. Quinte 2 2/3'
                    42. Superoktave 2'
                    43. Mixtur 4 X 1 1/3'
                    44. Vox humana 8'
                    45. Glocken I.
                    46. Glocken II.
                    46. Echo
                    47. IV - III 8'
                    48. V Sp - III 8'
                    49. V Bw - III 8'
                    50. Tremolo AW
                    51. Handregister ab
                    52. Walze ab
                    53. Zungen ab (general)
                    54. Man. 16, und Ped. 32, ab
                    55. Mixturen ab

                    **IV. man. (Schwellwerk)**
                    56. Spitzgedact 16'
                    57. Weitprincipal 8'
                    58. Rohrgedackt 8'
                    59. Harfpfeife 8'
                    60. Vox angelica 3 X 8'-4'-4'
                    61. Kupferoktave 4'
                    62. Spillflöte 4'
                    63. Rohrquintatön 4'
                    64. Nachthornquinte 2 2/3'
                    65. Quintadecima 2'
                    66. Waldflöte 2'
                    67. Koppelflötenterz 1 3/5'
                    68. Querflöte 1'
                    69. Farbenmixtur 3-4 X
                    70. Mixtur 6-7 X
                    71. Quintzimbel 3 X 1/4'
                    72. Bassethorn 16'
                    73. Franz. Trompete 8'
                    74. Rohrschalmei 8'
                    75. Geigendregal 8'
                    76. Clairon 4'
                    77. Glocken I
                    78. Glocken II
                    79. V Sp - IV 8'
                    80. V Bw - IV 8'
                    81. Tremolo IV

                    **V. man. (Schwellpositiv)**
                    82. Schwellpositiv ab
                    83. Gedact 8'
                    84. Trichterprincipal 4'
                    85. Blockflöte 4'
                    86. Kleinprincipal 2'
                    87. Quinte 1 1/3'
                    88. Schwegel 1'
                    89. Sesquialtera 2-3 X 22/3' -13/5' -11/7'
                    90. Scharf 5 X 1'
                    91. Krummhorn 8'
                    92. Singend Kornett 4'
                    93. Tremolo V Sp

                    **V. man. (Bombardenwerk)**
                    94. Bombardenwerk ab
                    95. Holzprincipal 8'
                    96. Solokornett 4-6 X 4'
                    97. Principalmixtur 8 X 4'
                    98. Trompete 16'
                    99. Trompete (horizont.) 8'
                    100. Trompete (horizont.) 4'

                    **Pedal (Neues Werk)**
                    101. Pedal NW ab
                    102. Holzprincipal 16'
                    103. Gedackpommer 16'
                    104. Grossnasat 10 2/3'
                    105. Kupferprincipal 8'
                    106. Bleikoppelflöte 8'
                    107. Choralbass 4'
                    108. Rohrpfeife 4'
                    109. Russischhorn 2'
                    110. Rauschbass 5 X 5 1/3'
                    111. Mixtur 6 X 2'
                    112. Bombarde 16'
                    113. Trompete 8'
                    114. Kopftrompete 4'
                    115. Zink 2'
                    116. Akustik 21 1/3'
                    117. IV - P 8'
                    118. V Sp - P 8'
                    119. V Bw - P 8'

                    **Pedal (Altes Werk)**
                    120. Pedal AW ab
                    121. Maiorbass 32'
                    122. Principal 16'
                    123. Offenerbass 16'
                    124. Subbass 16'
                    125. Quintadenbass 16'
                    126. Oktavenbass 8'
                    127. Gemshornquinte 5 1/3'
                    128. Mixtur 6 X 4'
                    129. Contraposaune 32'
                    130. Posaunenbass 16'
                    131. Trombabass 8'
                    132. Clarino 4'
                    133. I - P 8'
                    134. II - P 8'
                    135. III - P 8'

                    ([Zdroj](https://varhany.moric-olomouc.cz/dispozice))
                    END,
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::ActionElectrical,
                OrganCategory::WindchestSchleif,
            ],
            rebuilds: [
                new OrganRebuild(['organ_builder_id' => 2, 'year_built' => 1968])
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 2,
                'place' => 'kostel Očišťování Panny Marie',
                'municipality' => 'Dub nad Moravou',
                'latitude' => 49.4834803,
                'longitude' => 17.2749228,
                'region_id' => Region::Olomoucky,
                'importance' => 10,
                'organ_builder_id' => 3,
                'year_built' => 1768,
                'stops_count' => 29,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Kostel_O%C4%8Di%C5%A1%C5%A5ov%C3%A1n%C3%AD_Panny_Marie%2C_Dub_nad_Moravou%2C_okres_Olomouc.jpg/640px-Kostel_O%C4%8Di%C5%A1%C5%A5ov%C3%A1n%C3%AD_Panny_Marie%2C_Dub_nad_Moravou%2C_okres_Olomouc.jpg',
                'image_credits' => 'xkomczax, CC BY-SA 4.0, via Wikimedia Commons',
                'perex' => 'Velké a zachovalé barokní varhany.',
                'description' => 'Varhany svojí monumentalitou, vyjádřenou též velkou a bohatě zdobenou skříní, odpovídají významu známého poutního místa. Dispozice byla upravena jen částečně na konci 19. století Františkem Čápkem. Ačkoli jsou varhany v současné době hratelné, bylo by žádoucí jejich restaurování.',
                'literature' => 'Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 64)',
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 3,
                'place' => 'kostel sv. Mikuláše',
                'municipality' => 'Ludgeřovice',
                'latitude' => 49.8910011,
                'longitude' => 18.2389378,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 6,
                'organ_builder_id' => 1,
                'year_built' => 1931,
                'stops_count' => 45,
                'manuals_count' => 2,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Kostel_svat%C3%A9ho_Mikul%C3%A1%C5%A1e_a_fara.JPG/620px-Kostel_svat%C3%A9ho_Mikul%C3%A1%C5%A1e_a_fara.JPG',
                'image_credits' => 'Magdon, CC BY-SA 4.0, via Wikimedia Commons',
                'perex' => 'Zdařilý nástroj z meziválečné produkce firmy Rieger pravidelně hostí varhanní koncerty.',
                'description' => 'Konzervativní neogotická varhanní skříň zapadá dobře do interiéru kostela. Varhany již částečně vycházejí ze zásad tzv. varhanního hnutí, usilujícího o návrat k baroknímu zvukovému ideálu, spočívajícímu mj. v lesklém zvuku rejstříků vyšších poloh.',
                'literature' => 'Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 126)',
                'disposition' => <<<END
                    **Spojky**
                    1\. I / P 8'
                    2. II / P 8'
                    3. I – 4'
                    4. II / I 16'
                    5. II / I 8'
                    6. II / I 4'
                    7. II – 16'
                    8. II – 4'

                    **Pedal C – f1**
                    9. Trompete 8' trsm.
                    10. Posaune 16'
                    11. Oktave 4'
                    12. Flöte 4'
                    13. Oktavbass 8'
                    14. Cello 8'
                    15. Quintbass 10 2/3'
                    16. Principalbass 16'
                    17. Violon 16'
                    18. Subbass 16'
                    19. Quintatön 16' trsm.

                    **I.manual C – g3**
                    20. Trompete harm. 8'
                    21. Principal 16'
                    22. Mixtur 5 fach
                    23. Kornett 3-5 fach 8'
                    24. Oktave 2'
                    25. Flautino 2'
                    26. Bachflote 2'
                    27. Quintflote 2 2/3'
                    28. Oktave 4'
                    29. Fugara 4'
                    30. Holzflöte 4'
                    31. Rohrflöte 4'
                    32. Principal 8'
                    33. Hohlflöte 8'
                    34. Nachthorn 8'
                    35. Salicional 8'

                    **II.manual C – g3 – g4**
                    36. Aeoline 8'
                    37. Vox coelestis 8'
                    38. Traversflöte 8'
                    39. Rohrflöte 8'
                    40. Gamba 8'
                    41. Geigenprincipal 8'
                    42. Flute amabile 4'
                    43. Nachthorn 4'
                    44. Gemshorn 4'
                    45. Prästant 4'
                    46. Nasad 2 2/3'
                    47. Oktave 2'
                    48. Terzflöte 1 3/5'
                    49. Sifflöte 1'
                    50. Harmonia aetherea 4 fach
                    51. Scharff 3 fach 1'
                    52. Quintatön 16'
                    53. Oboe 8'

                    Crescendo
                    Schweller – II. manual
                    2 VK
                    6 PK
                    Tremolo – II. manual
                    Zungen ab
                    Aut. pedal
                    Crescendo ab

                    ([Zdroj](https://www.ponca-organs.com/varhany/ludgerovice/foto.htm))
                    END,
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Romantic,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::ActionPneumatical,
                OrganCategory::WindchestKegel,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 4,
                'place' => 'kostel sv. Mořice',
                'municipality' => 'Kroměříž',
                'latitude' => 49.2991767,
                'longitude' => 17.3910325,
                'region_id' => Region::Zlinsky,
                'importance' => 5,
                'organ_builder_id' => 5,
                'year_built' => 1905,
                'stops_count' => 46,
                'manuals_count' => 3,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Krom%C4%9B%C5%99%C3%AD%C5%BE_St._Mauritius_915.jpg/512px-Krom%C4%9B%C5%99%C3%AD%C5%BE_St._Mauritius_915.jpg',
                'image_credits' => 'GFreihalter, CC BY-SA 3.0, via Wikimedia Commons',
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Kostel_sv._Mo%C5%99ice_v_Krom%C4%9B%C5%99%C3%AD%C5%BEi.JPG/497px-Kostel_sv._Mo%C5%99ice_v_Krom%C4%9B%C5%99%C3%AD%C5%BEi.JPG',
                'outside_image_credits' => 'MadamLochnessia, CC BY-SA 4.0, via Wikimedia Commons',
                'perex' => 'Jediný velký nástroj varhanáře Emanuela Štěpána Petra na Moravě.',
                'description' => 'Velké a dobře zachované Petrovy varhany, jediný velký Petrův nástroj na Moravě.',
                'literature' => <<<END
                    Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 110)
                    ŠON, Jiří. Emanuel Štěpán Petr v kontextu českého varhanářství [online]. Brno, 2011 [cit. 2022-05-04]. Dostupné z: https://is.jamu.cz/th/y47ke/. Bakalářská práce. Janáčkova akademie múzických umění, Hudební fakulta. Vedoucí práce Petr LYKO. (str. 20)
                    END,
                'disposition' => <<<END
                    **1. manuál**
                    Principal 16'
                    Principal 8'
                    Corno 8'
                    Flétna harmonická 8'
                    Kryt 8'
                    Viola alta 8'
                    Tromba 8'
                    Kvinta 5 1/3'
                    Fléta 4'
                    Cornetto 4'
                    Oktáva 4'
                    Oktáva 2'
                    Mixtura 2 2/3'
                    Cymbel 2'

                    **2. manuál**
                    Bordun 8'
                    Principal 8'
                    Viola da Gamba 8'
                    Salicional 8'
                    Flétna koncertní 8'
                    Dolce 8'
                    Klarinetto 8'
                    Kvintadena 8'
                    Fléta 4'
                    Oktáva 4'
                    Oktáva 2'
                    Mixtura 2 2/3'

                    **3. manuál**
                    Principál 8'
                    Flétna rourková 8'
                    Kryt jemný 8'
                    Aeolina 8'
                    Vox coelestis 8'
                    Hoboj 8'
                    Kryt jemný 16'
                    Oktáva 4'
                    Mixtura 2 2/3'
                    Fléta 4'

                    **Pedál**
                    Principalbas 16'
                    Oktávbas 8'
                    Violonbas 16'
                    Subbas 16'
                    Bordunbas 8'
                    Bordun 8'
                    Pozoun 16'
                    Contrabas 32'
                    Chorálbas 4'
                    Kvintbas 10 2/3'

                    ([Zdroj](http://organist-ub.cz/varhany/kromeriz-sv-moric/))
                    END,
                'web' => <<<END
                    http://organist-ub.cz/varhany/kromeriz-sv-moric/
                    END,
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Romantic,
                OrganCategory::Biggest,
                OrganCategory::ActionPneumatical,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 5,
                'place' => 'kostel sv. Jakuba',
                'municipality' => 'Brno',
                'latitude' => 49.1966367,
                'longitude' => 16.6084586,
                'region_id' => Region::Jihomoravsky,
                'importance' => 7,
                'organ_builder_id' => 6,
                'year_built' => 1862,
                'stops_count' => 31,
                'manuals_count' => 2,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/Church_of_Saint_James_in_Brno.jpg/465px-Church_of_Saint_James_in_Brno.jpg',
                'outside_image_credits' => 'Millenium187, CC BY-SA 3.0, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/M%C4%9Bsto_Brno_-_varhany_v_kostele_Sv._Jakuba.jpg/516px-M%C4%9Bsto_Brno_-_varhany_v_kostele_Sv._Jakuba.jpg',
                'image_credits' => 'Kirk, CC BY-SA 3.0, via Wikimedia Commons',
                'description' => 'Z barokních dvoumanuálových varhan Jakuba Ryšáka z r. 1691 se zachovala pouze významná varhanní skříň. Nástroj Františka Svítila je zajímavým a ojedinělým dokladem varhanářství v 1. pol. 19. stol.',
                'literature' => 'Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 34)',
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Baroque,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 6,
                'place' => 'kostel Nanebevzetí Panny Marie',
                'municipality' => 'Polná',
                'latitude' => 49.4876303,
                'longitude' => 15.7183628,
                'region_id' => Region::Vysocina,
                'importance' => 10,
                'organ_builder_id' => 4,
                'year_built' => 1708,
                'stops_count' => 31,
                'manuals_count' => 2,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Kostel_NPM_Poln%C3%A1_1.JPG/640px-Kostel_NPM_Poln%C3%A1_1.JPG',
                'outside_image_credits' => 'Jana Lánová, CC BY-SA 3.0, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Poln%C3%A1%2C_Church_of_the_Assumption%2C_organ_%2802%29%2C_cropped.jpg/507px-Poln%C3%A1%2C_Church_of_the_Assumption%2C_organ_%2802%29%2C_cropped.jpg',
                'image_credits' => 'Czeva, CC BY-SA 3.0, via Wikimedia Commons',
                'description' => 'Velké a jedinečně zachovalé Sieberovy varhany.',
                'literature' => <<<END
                    Sehnal, Jiří. Barokní varhanářství na Moravě. Vydání první. Brno: Muzejní a vlastivědná společnost v Brně, 2003-2018. 3 svazky. Prameny k dějinám a kultuře Moravy; č. 9, 10. Monografie. ISBN 80-7275-042-9. (2. díl, str. 210)
                    SOBOTKA, Petr. Jan David Sieber - osobnost českého barokního varhanáře [online]. Olomouc, 2012 [cit. 2022-05-01]. Dostupné z: https://theses.cz/id/nq8oe6/. Diplomová práce. Univerzita Palackého v Olomouci, Pedagogická fakulta. Vedoucí práce Doc. MgA. Petr Planý. (str. 48)
                    END,
                'disposition' => <<<END
                    **Hlavní stroj**
                    Bourdonflöte 16'
                    Principal 8'
                    Quintadena 8'
                    Gamba 8'
                    Salecinal 8'
                    Octava 4'
                    Fugara 4'
                    Nachthorn 4'
                    Quinta 3'
                    Super Octava 2'
                    Feldtflet 2'
                    Quindecima 1 1/2'
                    Sedecima 1'
                    Sesquealter
                    Mixtura 5 fach
                    Cymbel 3 fach

                    **Positiv**
                    Copula major 8'
                    Principal 4'
                    Copula minor 4'
                    Nassatquinte 3'
                    Octava 2'
                    Quinta 1 1/2'
                    Superoctava 1'
                    Mixtura 4 fach

                    **Pedál**
                    Subbass 16'
                    Subbass claus. 16'
                    Octavbass 8'
                    Quintbass 6'
                    Superoctavbass 4'
                    Schnarrbass 16'
                    Trompetbass 8

                    ([Zdroj](http://www.ceskevarhany.cz/portfolio/kostel-nanebevzeti-panny-marie-polna/))
                    END,
                'web' => <<<END
                    http://www.ceskevarhany.cz/portfolio/kostel-nanebevzeti-panny-marie-polna/
                    END,
            ],
            categories: [
                OrganCategory::BuiltTo1799,
                OrganCategory::Baroque,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 7,
                'place' => 'konkatedrála Nanebevzetí Panny Marie',
                'municipality' => 'Opava',
                'latitude' => 49.9387481,
                'longitude' => 17.9006833,
                'region_id' => Region::Moravskoslezsky,
                'importance' => 1,
                'organ_builder_id' => 2,
                'year_built' => 1957,
                'stops_count' => 56,
                'manuals_count' => 3,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Kostel_Nanebevzet%C3%AD_P._Marie%2C_Opava_-_panoramio.jpg/640px-Kostel_Nanebevzet%C3%AD_P._Marie%2C_Opava_-_panoramio.jpg',
                'outside_image_credits' => 'Vladimír Jadrný, CC BY 3.0, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Konkatedr%C3%A1la_Opava%2C_Varhany.jpg/640px-Konkatedr%C3%A1la_Opava%2C_Varhany.jpg',
                'image_credits' => 'Vojtěch Dočkal, CC BY-SA 4.0, via Wikimedia Commons',
                'web' => <<<END
                    https://www.ebencompetition.cz/soutezni-varhany/varhany-v-konkatedrale-Nanebevzeti-Panny-Marie-v-Opave
                    http://www.anatomie-varhan.cz/texty/varhany/nastroje/opava/pm/P-Maria.htm
                    END,
                'description' => 'Velký chrámový nástroj byl několikrát přebudován. Pravidelně se zde koná Mezinárodní varhanní soutěž Petra Ebena.',
                'disposition' => <<<END
                    **I. manuál – Hlavní stroj**
                    *C - a3/a4, 58 kláves, 70 tónů*
                    1\. Principál 16'
                    2. Kryt špičatý 16'
                    3. Principál 8'
                    4. Principál italský 8'
                    5. Flétna dutá 8'
                    6. Flétna trubicová 8'
                    7. Gamba špičatá 8'
                    8. Oktáva 4'
                    9. Flétna zobcová 4'
                    10. Kvinta 2 2/3'
                    11. Oktáva flétnová 2'
                    12. Mixtura velká 5-6x 2'
                    13. Mixtura malá 4x 1'
                    14. Fagot 16'
                    15. Trompeta 8'
                    16. Trompeta ext. 4'
                    17. Kornet 3-5x 2 2/3'
                    18. III/I 16'
                    19. II/I 16'
                    20. III/I 8'
                    21. II/I 8'
                    22. III/I 4'
                    23. II/I 4'
                    24. I 4'
                    25. Zapínač ručních rejstříků 
                    26. Vypínač jazykových hlasů 
                    Vypínače jednotlivých jazyků (bez číslování) 
                
                    **II. manuál – Horní pozitiv**
                    *C - a3/a4, 58 kláves, 70 tónů*
                    27. Kryt dvojitý 8'
                    28. Kvintadena 8'
                    29. Principál 4'
                    30. Roh noční 4'
                    31. Seskvialtera 3x 2 2/3'
                    32. Oktáva 2'
                    33. Flétna lesní 2'
                    34. Kvinta špičatá 1 1/3'
                    35. Akuta 4x 1'
                    36. Roh křivý 16'
                    37. Šalmaj trubicová 8'
                    38. Tremolo 
                    39. II 16'
                    40. III/II 8'
                    41. III/II 4'
                    42. II 4'
                    43. Zvony g0 – dis2 
                
                    **III. manuál – Žaluziový stroj**
                    *C - a3/a4, 58 kláves, 70 tónů*
                    44. Kryt tichý 16'
                    45. Principál 8'
                    46. Kryt dřevěný 8'
                    47. Salicionál 8'
                    48. Vox celestis 1-2x 8'+ 8'
                    49. Oktáva 4'
                    50. Flétna trubicová 4'
                    51. Nasard 2 2/3'
                    52. Kvintadecima 2'
                    53. Tercie 1 3/5'
                    54. Mixtura 5-7x 1 1/3'
                    55. Dolkán 1'
                    56. Cymbál kvintový 3x 1/2'
                    57. Trubka ext. 8'
                    58. Eufon 8'
                    59. Vox humana 8'
                    60. Clairon 4'
                    61. Tremolo 
                    62. III 4'
                
                    **Pedál**
                    *C - f1, 30 kláves i tónů*
                    63. Bas akustický 2x 32' + 16'
                    64. Principálbas 16'
                    65. Apertabas 16'
                    66. Subbas 16'
                    67. Burdonbas 16'
                    68. Kvintbas 10 2/3'
                    69. Oktávbas 8'
                    70. Flétna basová 8'
                    71. Bas šumivý 4x 5 1/3'
                    72. Chorálbas 4'
                    73. Mixtura 5-7x 2'
                    74. Roh noční 2'
                    75. Kontrafagot 32'
                    76. Pozoun 16'
                    77. Fagot ext. 16'
                    78. Trompeta basová 8'
                    79. Klarina ext. 4'
                    80. III/P 8'
                    81. II/P 8'
                    82. I/P 8'
                    83. III/P 4'
                    84. II/P 4'
                    85. I/P 4'
                    86. Zvony G – dis1
                    Vypínače jednotlivých jazyků (bez číslování) 

                    Traktura elektropneumatická
                    Kuželkové vzdušnice
                    Crescendový válec
                    Žaluzie III. manuálu
                    8 volných kombinací
                    Pleno
                    Tutti
                    2 pedálové kombinace

                    Pistony (zleva):
                    Pleno
                    Tutti
                    PK I
                    PK II
                    Vypínač jazykových hlasů
                    Vypínač mixtur
                    Vypínač 32' ped. 16' man.
                    Vypínač spojek z crescenda
                    Vypínač crescenda
                    Vypínač volných kombinací
                    Volné kombinace 1-8 

                    ([Zdroj](https://www.ebencompetition.cz/soutezni-varhany/varhany-v-konkatedrale-Nanebevzeti-Panny-Marie-v-Opave/))
                    END
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::BuiltFrom1990,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::ActionPneumatical,
                OrganCategory::ActionElectrical,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 8,
                'place' => 'Dvořákova síň Rudolfina',
                'municipality' => 'Praha',
                'latitude' => 50.0899300,
                'longitude' => 14.4154419,
                'region_id' => Region::Praha,
                'importance' => 9,
                'organ_builder_id' => 2,
                'year_built' => 1974,
                'stops_count' => 63,
                'manuals_count' => 4,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Rudofinum_%C4%8Delo_1.jpg/640px-Rudofinum_%C4%8Delo_1.jpg',
                'outside_image_credits' => 'VitVit, CC BY-SA 4.0, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Praha_Rudolfinum_Interior_2003.jpg/640px-Praha_Rudolfinum_Interior_2003.jpg',
                'image_credits' => 'Photo: Andreas Praefcke, CC BY 3.0, via Wikimedia Commons',
                'web' => <<<END
                    https://www.rudolfinum.cz/blog/nejlepsi-povalecne-pistaly-rudolfinum-melo-s-varhanami-stesti/
                    https://www.klasikaplus.cz/serial/item/3297-varhany-a-varhanici-5-prvni-koncertni-varhany-v-praze
                    https://magazin.ceskafilharmonie.cz/varhany-ve-dvorakove-sini-rudolfina/
                    END,
                'description' => 'Ve Dvořákově síni Rudolfina se vystřídalo několik nástrojů. Současné varhany stojí ve skříni z původních varhan Wilhelma Sauera z r. 1885. Jedná se o první čtyřmanuálové varhany s mechanickou trakturou u nás (pro jednoduchost konstrukce a snadnost hry se u takto velkých varhan používala spíše traktura elektrická). Na umělecké a technické koncepci varhan se podíleli mj. Jiří Reinberger, významný český koncertní varhaník, a německý varhanář Rudolf von Beckerath.',
                'perex' => 'Reprezentativní koncertní nástroj poválečné produkce krnovské firmy Rieger-Kloss.',
                'literature' => 'Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 208)',
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::Biggest,
                OrganCategory::ActionMechanical,
                OrganCategory::WindchestSchleif,
            ]
        );

        $this->insertOrgan(
            data: [
                'id' => 9,
                'place' => 'kostel sv. Jakuba Většího',
                'municipality' => 'Praha',
                'latitude' => 50.0883311,
                'longitude' => 14.4249258,
                'region_id' => Region::Praha,
                'importance' => 8,
                'organ_builder_id' => 8,
                'year_built' => 1705,
                'stops_count' => 95,
                'manuals_count' => 4,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/1c/Kostel_sv._Jakuba_v%C4%9B%C5%BE.JPG',
                'outside_image_credits' => 'VitVit, CC BY-SA 4.0, via Wikimedia Commons',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/Svatojakubske-Varhany.jpg/640px-Svatojakubske-Varhany.jpg',
                'image_credits' => 'Anton Fedorenko, CC BY-SA 3.0, via Wikimedia Commons',
                'description' => 'V roce 1705 postavil do kostela velké dvoumanuálové varhany Abrahám Stark. Cenné varhany se zachovaly až do r. 1941, kdy byly Janem Tučkem s využitím části původního píšťalového fondu zcela přestavěny na romantický nástroj. Zdejší varhaník a iniciátor přestavby Bedřich Antonín Wiedermann toužil mít u sv. Jakuba především koncertní nástroj, schopný interpretace soudobé varhanní literatury. Tento požadavek byl nadřazen nad tehdy k nám již pronikající myšlenky varhanního hnutí, zdůrazňujícího úctu a obdiv k historickým barokním nástrojům. O vyřešení různých nedostatků Tučkových varhan se pokusila v 80. letech další přestavbou firma Rieger-Kloss. V roce 2011 provedla modernizaci hracího stolu firma Ponča.',
                'perex' => 'Největší varhany v Praze jsou poznamenány řadou přestaveb. Každoročně hostí prestižní varhanní festival Audite organum.',
                'web' => <<<END
                    https://sanctijacobiorganum.com/
                    https://auditeorganum.cz/varhany/
                    https://www.klasikaplus.cz/varhany-a-varhanici-10-br-bazilika-sv-jakuba-v-praze/
                    END,
                'literature' => <<<END
                    Krátký, Jiří a Svoboda, Štěpán. Nejvýznamnější varhany v České republice. 1. vydání. V Brně: CPress, 2019. 267 stran. ISBN 978-80-264-2859-6. (str. 202)
                    Tomší, Lubomír et al. Historické varhany v Čechách. 1. vyd. Praha: Libri, 2000. 263 s. ISBN 80-7277-009-8. (str. 133)
                    Tomíček, Jan. Varhany a jejich osudy. [Praha]: PM vydavatelství, 2010. 310 s. ISBN 978-80-900808-2-9. (str. 71)
                    END,
                'disposition' => <<<END
                    **PEDÁL C – g‘**
                    Podstav (Untersatz) 32'
                    Principálbas 16'
                    Subbas otevřený (S. offen) 16'
                    Subbas krytý (S. gedeckt) 16'
                    Burdon 16'
                    Viola 16'
                    Kryt tichý (Ext.) (Stillgedeck) 16' + 8'
                    Kvinta velká (Grossquinte) 10 2/3'
                    Oktávbas 8'
                    Flétna basová (Bassflöte) 8'
                    Princ. rohový (Trsm) (Hornprinzipal) 8'
                    Kvinta 5 1/3'
                    Superoktáva 4'
                    Flétna trubicová (Rohrflöte) 4'
                    Flétna zobcová (Blockflöte) 2'
                    Mixtura 6x 2 2/3'
                    Bombard 32'
                    Pozoun (Posaune) 16'
                    Fagot (Trsm) 16'
                    Trompeta basová 8'
                    Clairon 4'

                    **II. MANUÁL (HW, Gr.) C – c““**
                    Principál 16'
                    Burdonflauta 16'
                    Prestant 8'
                    Principál 8'
                    Kvintadena 8'
                    Flétna barokní (Barockflöte) 8'
                    Gamba špičatá (Spitzgambe) 8'
                    Oktáva 4'
                    Oktáva italská 4'
                    Flétna dřevěná (Holzflöte) 4'
                    Kvintadena 4'
                    Salicet 4'
                    Kvinta 2 2/3'
                    Superoktáva 2'
                    Roh kamzičí (Gemshorn) 2'
                    Kvinta malá (Kleine Quinte) 1 1/3'
                    Píšťala šumivá 4-5x (Rauschpfeife) 2 2/3'
                    Mixtura barokní 6x 1'
                    Španělská trubka (Spanische Tromp.) 16'
                    Španělská trubka (Ext.) 8'
                    Trompeta 8'

                    **I. MANUÁL (Positiv) C – c““**
                    Kopula major 8'
                    Salicionál 8'
                    Principál 4'
                    Kopula minor 4'
                    Oktáva 2'
                    Seskvialtera 2x 1 1/3'
                    Mixtura 4-5x 1'
                    Regál 16'
                    Roh basetový (Basetthorn) 8'
                    III. MANUÁL (SW) C – c““
                    Tibia clausa 16'
                    Viola 16'
                    Principál 8'
                    Flétna dutá (Hohlflöte) 8'
                    Flétna dvojitá (Doppelflöte) 8'
                    Fugara 8'
                    Kryt zpěvný (Singendgedeck) 8'
                    Dolce 8'
                    Oktáva trychtýřová (Trichteroktave) 4'
                    Flétna koncertní (Konzertflöte) 4'
                    Roh kamzičí (Gemshorn) 4'
                    Kvinta flétnová (Flötkvinte) 2 2/3'
                    Oktáva švýcarská (Schweizeroktave) 2'
                    Roh noční (Nachthorn) 2'
                    Kornet sept. 3-5x 2 2/3'
                    Mixtura 5x 2'
                    Kvinta šelest 2x (Rauschquinte) 1 1/3'
                    Fagot 16'
                    Trompeta harm. 8'
                    Šalmaj trubicová (Rohrschalmei) 8'
                    Hoboj francouzská (Fr. Oboe) 4'

                    **IV. MANUÁL (SW) C – c““**
                    Kvintadena 16'
                    Kryt tichý (Stillgedeck) 16'
                    Principál rohový (Hornprinzipal) 8'
                    Principál houslový (Geigenprinzipal) 8'
                    Flétna harmonická (Harm. Flöte) 8'
                    Burdon 8'
                    Gamba jemná (Zartgambe) 8'
                    Vox celestis 2x 8' + 4'
                    Oktáva 4'
                    Flétna příčná (Querflöte) 4'
                    Flétna něžná (Zartflöte) 4'
                    Nasard 2 2/3'
                    Octavin 2'
                    Flétna lesní (Waldflöte) 2'
                    Tercie 1 3/5'
                    Larigot 1 1/3'
                    Flautino 1'
                    Plein jeu 5-6x 1 1/3'
                    Alikvoty 3x 1 1/7'
                    Roh anglický (Englischhorn) 8'
                    Hoboj (Oboe) 8'
                    Vox humana 8'
                    Klarina 4'

                    **SPOJKY-KOPPEL**
                    IV/P 8'
                    III/P 8'
                    II/P 8'
                    I/P 8'
                    III/P 4'
                    II/P 4'
                    P 4'
                    II/I 8'
                    III/II 16'
                    IV/II 8'
                    III/II 8'
                    I/II 8'
                    IV/II 4'
                    III/II 4'
                    II 4'
                    IV/III 16'
                    IV/III 8'
                    III 4'
                    IV 4'

                    ([Zdroj](https://auditeorganum.cz/varhany/dispozice-varhan/))
                    END
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::Biggest,
                OrganCategory::ActionElectrical,
                OrganCategory::WindchestKegel,
            ],
            rebuilds: [
                new OrganRebuild(['organ_builder_id' => 7, 'year_built' => 1941]),
                new OrganRebuild(['organ_builder_id' => 2, 'year_built' => 1982]),
            ]
        );
        
        // Velký Újezd pro uživatele test@example.com
        $this->insertOrgan(
            data: [
                'id' => 10,
                'user_id' => 2,
                'place' => 'kostel sv. Jakuba Staršího',
                'municipality' => 'Velký Újezd',
                'latitude' => 49.5778403,
                'longitude' => 17.4833708,
                'region_id' => Region::Olomoucky,
                'importance' => 1,
                'organ_builder_id' => 1,
                'year_built' => 1937,
                'stops_count' => 17,
                'manuals_count' => 2,
                'outside_image_url' => null,
                'outside_image_credits' => null,
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Kostel_Velk%C3%BD_%C3%9Ajezd_front.JPG/360px-Kostel_Velk%C3%BD_%C3%9Ajezd_front.JPG',
                'image_credits' => 'Dr. Killer, CC BY-SA 3.0, via Wikimedia Commons',
                'description' => null,
                'perex' => 'Menší nástroj meziválečné produkce firmy Rieger, hojně využívaný v liturgickém a koncertním provozu',
                'web' => <<<END
                    http://www.farnost.velkyujezd.cz/varhany
                    END,
                'literature' => null,
                'disposition' => <<<END
                    **I. manuál**
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

                    **II. manuál**
                    Kryt jemný 8'
                    Vox coelestis 8'
                    Flétna koncertní 8'
                    Principál italský 4'
                    Noční roh 2'
                    Cymbál 3x
                    Hoboj 8'
                    II/II 16'
                    II/II 4'

                    **Pedál**
                    Oktávbas 8'
                    Cello  8'
                    Subbass 16'
                    Bourdonbas 16'
                    I/P 8'
                    II/P 8'

                    ([Zdroj](http://www.farnost.velkyujezd.cz/varhany/))
                    END
            ],
            categories: [
                OrganCategory::BuiltFrom1800To1944,
                OrganCategory::Romantic,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::ActionPneumatical,
            ],
            rebuilds: [
            ]
        );
        
        // ZUŠ Žerotín pro uživatele test@example.com
        $this->insertOrgan(
            data: [
                'id' => 11,
                'user_id' => 2,
                'place' => 'ZUŠ Žerotín',
                'municipality' => 'Olomouc',
                'latitude' => 49.5924169,
                'longitude' => 17.2686928,
                'region_id' => Region::Olomoucky,
                'importance' => 1,
                'organ_builder_id' => 2,
                'year_built' => 1976,
                'stops_count' => 19,
                'manuals_count' => 2,
                'outside_image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Mural_%C4%8Cmel%C3%A1ka%2C_Olomouc_%2801%29.jpg/640px-Mural_%C4%8Cmel%C3%A1ka%2C_Olomouc_%2801%29.jpg',
                'outside_image_credits' => 'Jana Sekyrová, CC BY-SA 4.0, via Wikimedia Commons',
                'image_url' => 'https://i.ibb.co/gZVddX8/att-d-GBOhwjiyiu-FOh-Vau-VEAVJCy-E5q-ko-ZKWsh-WZu-Q3w-Ak.jpg',
                'image_credits' => null,
                'concert_hall' => true,
                'description' => null,
                'perex' => null,
                'web' => <<<END
                    https://www.zus-zerotin.cz
                    END,
                'literature' => null,
                'disposition' => <<<END
                    **I. manuál**
                    1\. Flétna dutá 8'
                    2. Salicionál 8'
                    3. Principál 4'
                    4. Nasard 2 2/3'
                    5. Flétna lesní 2'
                    6. Mixtura 4x 1 1/3'
                    7. II/I 8'
                    8. III/I 8'
                    9. III/I 16'

                    **II. manuál**
                    10. Kryt 8'
                    11. Kvintadena 4'
                    12. Principál 2'
                    13. III/II 8'

                    **III. manuál**
                    14. Flétna trubicová 8'
                    15. Chvění houslové 2x 8'
                    16. Flétna zobcová 4'
                    17. Oktáva 2'
                    18. Seskvialtera 2x 1 1/3'
                    19. Akuta 4x 1'
                    20. Tremolo II+III

                    **Pedál**
                    21. Subbas 16'
                    22. Principál 8'
                    23. Burdon 8'
                    24. Oktáva 4'
                    25. I/P 8'
                    26. II/P 8'
                    27. III/P 8'
                
                    Rieger-Kloss, opus 3450
                    Rozsah manuálů C-a3, rozsah pedálu C-f1
                    Pevné kombinace Pleno, Tutti
                    Volné kombinace A, B
                    Žaluzie I., Žaluzie III.
                    Crescendo, Vypínač crescenda, Vypínač spojek z crescenda
                    END
            ],
            categories: [
                OrganCategory::BuiltFrom1945To1989,
                OrganCategory::NeobaroqueUniversal,
                OrganCategory::ActionPneumatical,
                OrganCategory::WindchestKegel,
            ],
            rebuilds: [
            ]
        );
    }

    private function insertOrgan(array $data, array $categories = [], array $rebuilds = [])
    {
        $organBuilder = new Organ($data);
        $organBuilder->save();
        if (!empty($categories)) {
            $organBuilder->organCategories()->attach($categories);
        }
        if (!empty($rebuilds)) {
            $organBuilder->organRebuilds()->saveMany($rebuilds);
        }
    }
}
