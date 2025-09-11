<?php

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;
use App\Enums\OrganBuilderCategory;
use App\Enums\Region;
use App\Services\MarkdownConvertorService;
use App\Traits\HasAccordion;

new #[Layout('layouts.app-bootstrap')] class extends Component {

    use HasAccordion;

    #[Locked]
    public OrganBuilder $organBuilder;

    protected MarkdownConvertorService $markdownConvertor;

    private $showActivePeriodInHeading;

    const
        SESSION_KEY_SHOW_MAP = 'organ-builders.show.show-map',
        SESSION_KEY_SHOW_LITERATURE = 'organs.show.show-literature';

    public function boot(MarkdownConvertorService $markdownConvertor)
    {
        $this->markdownConvertor = $markdownConvertor;

        $this->showActivePeriodInHeading
            = isset($this->organBuilder->active_period)
            && !$this->organBuilder->is_workshop
            && !in_array($this->organBuilder->active_period, ['současnost', '–']);
    }

    public function mount()
    {
        if (!request()->hasValidSignature(false)) {
            $this->authorize('view', $this->organBuilder);
        }
        $this->organBuilder->viewed();
    }

    public function rendering(View $view): void
    {
        $title = '';
        if ($this->organBuilder->baroque) $title .= 'Barokní varhanářství na Moravě - ';
        $title .= $this->organBuilder->name;
        // alternativy: varhanářská výroba, výroba varhan
        $type = __($this->organBuilder->is_workshop ? 'varhanářství' : 'varhanář');
        $title .= " - $type";
        $view->title($title);
    }

    #[Computed]
    public function categoryGroups()
    {
        $groups = [];
        foreach ($this->organBuilder->organBuilderCategories as $category) {
            $categoryEnum = $category->getEnum();
            $color = $categoryEnum->getColor();
            $groups[$color] ??= [];
            $groups[$color][] = $categoryEnum;
        }
        return $groups;
    }

    #[Computed]
    private function previousUrl()
    {
        $previousUrl = url()->previous();
        if ($previousUrl === route('welcome') || $previousUrl === route('organ-builders.edit', [$this->organBuilder->id])) {
            return route('organ-builders.index');
        }
        return $previousUrl;
    }

    #[Computed]
    private function descriptionHtml()
    {
        $description = $this->markdownConvertor->convert($this->organBuilder->description);
        return trim($description);
    }

    #[Computed]
    private function organBuilderCategoriesGroups()
    {
        return OrganBuilderCategory::getCategoryGroups();
    }

    #[Computed]
    private function municipalityCountry()
    {
        $matches = [];
        if (preg_match('/\((.+)\)/', $this->organBuilder->municipality, $matches)) {
            $country = $matches[1];
            $municipality = trim(preg_replace('/\(.+\)/', '', $this->organBuilder->municipality));
        }
        else {
            $country = null;
            $municipality = $this->organBuilder->municipality;
        }
        return [$municipality, $country];
    }

    #[Computed]
    private function workshopMembers()
    {
        if (isset($this->organBuilder->workshop_members)) {
            return preg_replace(
                '/\(.*?\)/',
                '<span class="text-body-secondary">$0</span>',
                e($this->organBuilder->workshop_members)
            );
        }
    }

    #[Computed]
    public function metaDescription()
    {
        if (app()->getLocale() === 'cs') {
            if (isset($this->organBuilder->perex)) return $this->organBuilder->perex;
            if (isset($this->organBuilder->description)) return str($this->organBuilder->description)->replace('*', '')->replaceMatches('/\s+/u', ' ')->limit(200);
        }
    }

    #[Computed]
    public function images()
    {
        $images = [];
        foreach ($this->organBuilder->organs as $organ) {
            if (isset($organ->image_url, $organ->outside_image_url)) {
                $caption = view('components.organomania.organ-link', [
                    'organ' => $organ,
                    'showSizeInfo' => true,
                    'iconLink' => false,
                ])->render();
                $images[] = [$organ->image_url, $organ->image_credits, $caption];
            }
        }
        $organIds = $this->organBuilder->organs->pluck('id');

        foreach ($this->organBuilder->renovatedOrgans as $organ) {
            if (isset($organ->image_url, $organ->outside_image_url) && !$organIds->contains($organ->id)) {
                $year = __('restaurováno');
                if (isset($organ->year_renovated)) $year .= " {$organ->year_renovated}";
                $caption = view('components.organomania.organ-link', [
                    'organ' => $organ,
                    'showSizeInfo' => true,
                    'iconLink' => false,
                    'year' => $year,
                    'isRenovation'=> true,
                ])->render();
                $images[] = [$organ->image_url, $organ->image_credits, $caption];
            }
        }

        // HACK: správně má být uloženo v db.
        foreach ($this->additionalImages as [$imageUrl, $imageCredits, $name, $details]) {
            $content = e($name);
            if (isset($details)) $content .= sprintf(" <span class='text-body-secondary'>(%s)</span>", e($details));

            $images[] = [$imageUrl, $imageCredits, $content, true];
        }

        return $images;
    }

    #[Computed]
    public function additionalImages()
    {
        return match ($this->organBuilder->id) {
            3 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/St._Wenceslaus_Mikulov_organ.JPG/360px-St._Wenceslaus_Mikulov_organ.JPG',
                    'PetrS., CC BY-SA 4.0, via Wikimedia Commons',
                    'Mikulov, kostel sv. Václava',
                    '1771, II/19'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/%C5%A0a%C5%A1t%C3%ADn_bazilika_41.jpg/640px-%C5%A0a%C5%A1t%C3%ADn_bazilika_41.jpg',
                    'Ľuboš Repta, CC BY-SA 4.0, via Wikimedia Commons',
                    'Šaštín, bazilika Sedmibolestné Panny Marie',
                    '1771, II/23, dochována jen skříň'
                ],
            ],
            4 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Wien_-_Michaelerkirche%2C_Orgelempore.JPG/320px-Wien_-_Michaelerkirche%2C_Orgelempore.JPG',
                    '© C.Stadler/Bwag or © C.Stadler/Bwag; CC-BY-SA-4.0, via Wikimedia Commons',
                    'Vídeň, kostel sv. Michaela',
                    '1714, III/40'
                ]
            ],
            8 => [
                [
                    '/images/osek-choralni.jpg',
                    'Vianney2, CC BY-SA 3.0, via Wikimedia Commons',
                    'Osek u Duchcova, kostel Nanebevzetí P. Marie',
                    'Wenzel Stark, 1715, I/11'
                ]
            ],
            49 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Stolmir_kostel_varhany.JPG/517px-Stolmir_kostel_varhany.JPG',
                    'Petr Vilgus, CC BY-SA 3.0, via Wikimedia Commons',
                    'Štolmíř, kostel sv. Havla',
                    '1688, I/10'
                ]
            ],
            28 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Plana_Anna_Orgel_Burkhardt.jpg/640px-Plana_Anna_Orgel_Burkhardt.jpg',
                    'Regerman, CC BY-SA 4.0, via Wikimedia Commons',
                    'Planá, kostel sv. Anny',
                    '1730, II/15'
                ]
            ],
            22 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Jablonec_nad_Nisou_-_kostel_Dr._Farsk%C3%A9ho_12.jpg/960px-Jablonec_nad_Nisou_-_kostel_Dr._Farsk%C3%A9ho_12.jpg',
                    'Dominik Matus, CC BY-SA 4.0, via Wikimedia Commons',
                    'Jablonec nad Nisou, husitský kostel',
                    '1940, III/34'
                ]
            ],
            9 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/A_1989_489_z_20.02.1959_Ko%C5%9Bci%C3%B3%C5%82_parafialny_Wniebowzi%C4%99cia_NMP_%28d._klasztorny_NMP_%C5%81askawej%29_8.jpg/640px-A_1989_489_z_20.02.1959_Ko%C5%9Bci%C3%B3%C5%82_parafialny_Wniebowzi%C4%99cia_NMP_%28d._klasztorny_NMP_%C5%81askawej%29_8.jpg',
                    'Fotonews, CC BY-SA 3.0 PL, via Wikimedia Commons',
                    'Křešov, klášterní kostel Nanebevzetí Panny Marie',
                    '1736, III/53'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Breslau_St._Elizabeth_04.jpg/429px-Breslau_St._Elizabeth_04.jpg',
                    'ErwinMeier, CC BY-SA 4.0, via Wikimedia Commons',
                    'Vratislav, kostel sv. Alžběty',
                    '1761, III/54, novodobá replika'
                ],
            ],
            60 => [
                [ 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Kostel_Nav%C5%A1t%C3%ADven%C3%AD_P._Marie_na_Sv_._Kope%C4%8Dku_u_Olomouce_-_varhany.JPG/640px-Kostel_Nav%C5%A1t%C3%ADven%C3%AD_P._Marie_na_Sv_._Kope%C4%8Dku_u_Olomouce_-_varhany.JPG',
                    'Capkova Pavlina, CC BY-SA 3.0, via Wikimedia Commons',
                    'Olomouc, bazilika Navštívení P. Marie (Svatý Kopeček)',
                    '1724, dochována jen skříň'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Minorite_Brno_fc04.jpg/640px-Minorite_Brno_fc04.jpg',
                    'Fczarnowski, CC BY-SA 4.0, via Wikimedia Commons',
                    'Brno, kostel sv. Janů',
                    '1732, přestavěno'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Velehrad_-_48448317417.jpg/640px-Velehrad_-_48448317417.jpg',
                    'liakada-web, CC BY 2.0, via Wikimedia Commons',
                    'Velehrad, bazilika Nanebevzetí P. Marie',
                    '1747, dochována jen skříň'
                ],
            ],
            50 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Kostel_sv._M%C3%A1%C5%99%C3%AD_Magdal%C3%A9ny_v_D%C4%9Btmarovic%C3%ADch%2C_kruchta_s_varhany._Noc_kostel%C5%AF_20150529.jpg/640px-Kostel_sv._M%C3%A1%C5%99%C3%AD_Magdal%C3%A9ny_v_D%C4%9Btmarovic%C3%ADch%2C_kruchta_s_varhany._Noc_kostel%C5%AF_20150529.jpg',
                    'Ikcur, CC BY-SA 4.0, via Wikimedia Commons',
                    'Dětmarovice, kostel sv. Maří Magdalény',
                    '1871, II/18'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Varhany_z_roku_1885.jpg/512px-Varhany_z_roku_1885.jpg',
                    'Tomáš Adamec, CC BY-SA 4.0, via Wikimedia Commons',
                    'Moravičany, kostel sv. Jiří',
                    '1885, II/17'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Kostel_Bo%C5%BEsk%C3%A9ho_srdce_P%C3%A1n%C4%9B_%28Bohum%C3%ADn%29_Interi%C3%A9r.jpg/640px-Kostel_Bo%C5%BEsk%C3%A9ho_srdce_P%C3%A1n%C4%9B_%28Bohum%C3%ADn%29_Interi%C3%A9r.jpg',
                    'fotograf pro webovou stránku www.mesto-bohumin.cz, CC BY-SA 3.0, via Wikimedia Commons',
                    'Bohumín, kostel Božského srdce Páně',
                    'K. Neusser, 1903?, II'
                ],
            ],
            64 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/M%C4%9Bsto_Brno_-_varhany_v_kostele_Sv._Jakuba.jpg/516px-M%C4%9Bsto_Brno_-_varhany_v_kostele_Sv._Jakuba.jpg',
                    'Kirk, CC BY-SA 3.0, via Wikimedia Commons',
                    'Brno, kostel sv. Jakuba',
                    '1691, dochována jen skříň'
                ]
            ],
            72 => [
                [
                    '/images/velke-heraltice.jpg',
                    '',
                    'Velké Heraltice, kostel Neposkvrněného početí Panny Marie',
                    '1756, II/13'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Kru%C5%BEberk_varhany.jpg/640px-Kru%C5%BEberk_varhany.jpg',
                    'Ladin, CC BY-SA 4.0, via Wikimedia Commons',
                    'Kružberk, kostel sv. Petra a Pavla',
                    '1808, I/6'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Nov%C3%BD_Ji%C4%8D%C3%ADn_-_%C5%BDilina%2C_Kostel_sv._Mikul%C3%A1%C5%A1e%2C_Pohled_z_lodi_na_kruchtu.jpg/640px-Nov%C3%BD_Ji%C4%8D%C3%ADn_-_%C5%BDilina%2C_Kostel_sv._Mikul%C3%A1%C5%A1e%2C_Pohled_z_lodi_na_kruchtu.jpg',
                    'Jakub Bartoň, CC BY-SA 4.0, via Wikimedia Commons',
                    'Nový Jičín, kostel sv. Mikuláše (Žilina)',
                    '1820, I/9'
                ],
            ],
            19 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/DM_-_Starobrn%C4%9Bnsk%C3%A1_bazilika_%2801%29.jpg/319px-DM_-_Starobrn%C4%9Bnsk%C3%A1_bazilika_%2801%29.jpg',
                    'Dominik Matus, CC BY-SA 4.0, via Wikimedia Commons',
                    'Brno, bazilika Nanebevzetí Panny Marie (Staré Brno)',
                    '1876, dochována jen skříň'
                ]
            ],
            44 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Lomnice_n._P._-_sv._Mikul%C3%A1%C5%A1_08.JPG/640px-Lomnice_n._P._-_sv._Mikul%C3%A1%C5%A1_08.JPG',
                    'Hadonos, CC BY-SA 3.0, via Wikimedia Commons',
                    'Lomnice nad Popelkou, kostel sv. Mikuláše',
                    '1882, II/20'
                ]
            ],
            45 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/Kostel_sv.Barbory_%C5%A0umperk-varhany.jpg/360px-Kostel_sv.Barbory_%C5%A0umperk-varhany.jpg',
                    'Miroslava Fišerová, CC BY-SA 4.0, via Wikimedia Commons',
                    'Šumperk, kostel sv. Barbory',
                    '1904, I/8'
                ]
            ],
            51 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Kostel_ve_Strahovsk%C3%A9m_kl%C3%A1%C5%A1te%C5%99e_FR02.jpg/640px-Kostel_ve_Strahovsk%C3%A9m_kl%C3%A1%C5%A1te%C5%99e_FR02.jpg',
                    'Fried Marek, CC BY-SA 4.0, via Wikimedia Commons',
                    'Praha, bazilika Nanebevzetí Panny Marie (Strahov)',
                    'dochována jen skříň'
                ]
            ],
            69 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/GuentherZ_2013-07-15_0308_Vranov_nad_Dyj%C3%AD-Frain_an_der_Thaya_Pfarrkirche_Mari%C3%A4_Himmelfahrt.JPG/640px-GuentherZ_2013-07-15_0308_Vranov_nad_Dyj%C3%AD-Frain_an_der_Thaya_Pfarrkirche_Mari%C3%A4_Himmelfahrt.JPG',
                    'GuentherZ, CC BY 3.0, via Wikimedia Commons',
                    'Vranov nad Dyjí, kostel Nanebevzetí Panny Marie',
                    'II/15'
                ]
            ],
            77 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/Salv%C3%A1tor-evang-kostel2012interi%C3%A9r2.jpg/640px-Salv%C3%A1tor-evang-kostel2012interi%C3%A9r2.jpg',
                    'Ben Skála, CC BY-SA 3.0, via Wikimedia Commons',
                    'Praha, kostel sv. Salvátora',
                    '1865, dochována jen skříň'
                ]
            ],
            27 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/Damn%C3%ADkov_%2822%29.jpg/320px-Damn%C3%ADkov_%2822%29.jpg',
                    'Martina Bílá, CC BY-SA 4.0, via Wikimedia Commons',
                    'Damníkov, kostel sv. Jana Křtitele',
                    '1898, II/14'
                ]
            ],
            76 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Kruchta_s_rokokov%C3%BDmi_varhanami_Horn%C3%AD_Brann%C3%A1.JPG/640px-Kruchta_s_rokokov%C3%BDmi_varhanami_Horn%C3%AD_Brann%C3%A1.JPG',
                    'Bara.honlova, CC BY-SA 4.0, via Wikimedia Commons',
                    'Horní Branná, kostel sv. Mikuláše',
                    'A. Tauchmann, 1777'
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/%C3%9A%C5%A1t%C4%9Bk_kostel_sv._Petra_a_Pavla_interi%C3%A9r_varhany.jpg/640px-%C3%9A%C5%A1t%C4%9Bk_kostel_sv._Petra_a_Pavla_interi%C3%A9r_varhany.jpg',
                    'VitVit, CC BY-SA 4.0, via Wikimedia Commons',
                    'Úštěk, kostel sv. Petra a Pavla',
                    'A. Tauchmann, 1802, dochována jen skříň'
                ],
            ],
            6 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d8/2021-10-24_Church_of_Saints_Philip_and_James_%28Lelekovice%29_interior_2.jpg/640px-2021-10-24_Church_of_Saints_Philip_and_James_%28Lelekovice%29_interior_2.jpg',
                    'Lasy, CC BY-SA 4.0, via Wikimedia Commons',
                    'Lelekovice, kostel sv. Filipa a Jakuba',
                    '1857, I/7'
                ]
            ],
            37 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Da%C5%88kovice-kostel2013interi%C3%A9r.jpg/640px-Da%C5%88kovice-kostel2013interi%C3%A9r.jpg',
                    'Ben Skála, Benfoto, CC BY-SA 3.0, via Wikimedia Commons',
                    'Daňkovice, evangelický kostel',
                    '1810, I/7, později rozšířeno'
                ]
            ],
            62 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Brno_-_Kostel_sv._Tom%C3%A1%C5%A1e%2C_m%C3%ADstodr%C5%BEitelsk%C3%BD_pal%C3%A1c_a_alegorick%C3%A1_postava_spravedlnosti.jpg/640px-Brno_-_Kostel_sv._Tom%C3%A1%C5%A1e%2C_m%C3%ADstodr%C5%BEitelsk%C3%BD_pal%C3%A1c_a_alegorick%C3%A1_postava_spravedlnosti.jpg',
                    'Millenium187, CC BY-SA 3.0, via Wikimedia Commons',
                    'Brno, kostel sv. Tomáše',
                    null,
                ]
            ],
            39 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/P%C5%99erov%2C_kostel_sv._Vav%C5%99ince%2C_interi%C3%A9r%2C_varhany.jpg/640px-P%C5%99erov%2C_kostel_sv._Vav%C5%99ince%2C_interi%C3%A9r%2C_varhany.jpg',
                    'Palickap, CC BY-SA 4.0, via Wikimedia Commons',
                    'Přerov, kostel sv. Vavřince',
                    'F. Horčička st., 1761, dochována jen skříň',
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Uhersk%C3%A9_Hradi%C5%A1t%C4%9B%2C_kostel_svat%C3%A9ho_Franti%C5%A1ka_Xaversk%C3%A9ho%2C_varhany.jpg/640px-Uhersk%C3%A9_Hradi%C5%A1t%C4%9B%2C_kostel_svat%C3%A9ho_Franti%C5%A1ka_Xaversk%C3%A9ho%2C_varhany.jpg',
                    'Palickap, CC BY-SA 4.0, via Wikimedia Commons',
                    'Uherské Hradiště, kostel sv. Františka Xaverského',
                    'F. Horčička ml., 1772, dochována jen skříň',
                ]
            ],
            38 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Varhany_Rokytnice.JPG/640px-Varhany_Rokytnice.JPG',
                    'Kmenicka, CC BY-SA 3.0, via Wikimedia Commons',
                    'Rokytnice nad Jizerou, kostel sv. Michala',
                    '1760, dochována jen skříň',
                ],
                [
                    '/images/malesov.jpg',
                    null,
                    'Malešov, kostel sv. Václava',
                    'II/13',
                ],
            ],
            34 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Prag_Dom_St._Veit_10.jpg/435px-Prag_Dom_St._Veit_10.jpg',
                    'ErwinMeier, CC BY-SA 4.0, via Wikimedia Commons',
                    'Praha, katedrála sv. Víta, horní varhany',
                    'A. Gartner, 1765, dochována jen skříň',
                ],
            ],
            46 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Varhany_nad_sakristi%C3%AD_20.jpg/360px-Varhany_nad_sakristi%C3%AD_20.jpg',
                    'Pohled 111, CC BY-SA 4.0, via Wikimedia Commons',
                    'Želiv, kostel Narození P. Marie, chorální varhany',
                    'J. Halbig, 1734, II/13',
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7f/F-Konojedy348.jpg/457px-F-Konojedy348.jpg',
                    'M. Pröller, CC0, via Wikimedia Commons',
                    'Konojedy, kostel Nanebevzetí Panny Marie, dnes ve Varnsdorfu',
                    'F. Katzer, 1763, II/18',
                ],
            ],
            1 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Varhanycerkostbrno.jpg/640px-Varhanycerkostbrno.jpg',
                    'David Novák cs:Gothic2, CC BY 3.0, via Wikimedia Commons',
                    'Brno, evangelický kostel J. A. Komenského',
                    '1887, II/20',
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Krnov%2C_evangelick%C3%BD_kostel%2C_interi%C3%A9r_%28Archiv_%C4%8CCE%29_2.jpg/305px-Krnov%2C_evangelick%C3%BD_kostel%2C_interi%C3%A9r_%28Archiv_%C4%8CCE%29_2.jpg',
                    'Evangelical Church of Czech Brethren archive, Ústřední archiv Českobratrské církve evangelické, CC BY-SA 4.0, via Wikimedia Commons',
                    'Krnov, evangelický kostel',
                    '1902, II/21',
                ],
            ],
            68 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Bohosudov%2C_2020_%2818%29.jpg/640px-Bohosudov%2C_2020_%2818%29.jpg',
                    'Draceane, CC BY-SA 4.0, via Wikimedia Commons',
                    'Bohosudov, bazilika Panny Marie Bolestné',
                    '1734, II/22, dochována jen skříň',
                ],
            ],
            40 => [
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d2/Kostel_sv_Anton%C3%ADna_Praha_2012_4.jpg/471px-Kostel_sv_Anton%C3%ADna_Praha_2012_4.jpg',
                    'Karelj, CC BY 3.0, via Wikimedia Commons',
                    'Praha, kostel sv. Antonína Paduánského (Holešovice)',
                    '1913, III',
                ],
            ],
            105 => [
                [
                    '/images/osek-feller.jpg',
                    'Lukáš Dvořák',
                    'Osek u Duchcova, kostel Nanebevzetí P. Marie',
                    '1838, II/33, dochována jen skříň',
                ],
                [
                    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/%C4%8Cesk%C3%A1_L%C3%ADpa%2C_kostel_V%C5%A1ech_svat%C3%BDch%2C_varhany_01.jpg/640px-%C4%8Cesk%C3%A1_L%C3%ADpa%2C_kostel_V%C5%A1ech_svat%C3%BDch%2C_varhany_01.jpg',
                    'Daniel Baránek, CC BY-SA 3.0, via Wikimedia Commons',
                    'Česká Lípa, kostel Všech svatých',
                    '1848, II/23',
                ],
            ],
            default => []
        };
    }

    #[Computed]
    private function organs()
    {
        $this->organBuilder->load([
            'organs' => function (HasMany $query) {
                $query->withCount('organRebuilds');
            }
        ]);

        // ::collect(): konverze Eloquent kolekce na standardní kolekci
        $organs = $this->organBuilder->organs->map(
            fn (Organ $organ) => ['isRebuild' => false, 'organ' => $organ, 'year' => $organ->year_built]
        )->collect();

        $rebuiltOrgans = $this->organBuilder->organRebuilds->filter(
            // přestavované varhany mohou být cizího uživatele, pak se vůbec nenačtou
            fn (OrganRebuild $rebuild) => isset($rebuild->organ)
        )->map(
            fn (OrganRebuild $rebuild) => ['isRebuild' => true, 'organ' => $rebuild->organ, 'year' => $rebuild->year_built]
        )->collect();

        // jsou-li v $rebuiltOrgans zahrnuty stejné varhany jako v $organs, pak $this->organs->count() je větší je počet reálně vyfiltrovaných varhan
        return $organs
            ->merge($rebuiltOrgans)
            ->sortBy('year');
    }

    #[Computed]
    private function relatedOrganBuilders()
    {
        $relatedOrganBuilderIds = match ($this->organBuilder->id) {
            // Rieger
            59 => [1, 2],
            1 => [59, 2],
            2 => [59, 1],
            // Burkhardt
            28 => [8],
            // Richter
            60 => [4],
            // Silberbauer
            69 => [29],
            // Stark
            8 => [28],
            // Neusser
            50 => [72],
            // Prediger
            55 => [76, 5],
            // Eisenhut
            33 => [77],
            // Hubička
            40 => [67],
            // Paštikové
            53 => [67],
            // Organa
            52 => [7, 47],
            // Jozefy
            42 => [38],
            38 => [42],
            // Schwarz
            68 => [4],
            // Harbich
            37 => [72],
            default => []
        };
        return collect($relatedOrganBuilderIds)->map(
            fn ($organBuilderId) => OrganBuilder::find($organBuilderId)
        );
    }
    
}; ?>

<div class="organ-builder-show container">
    @isset($this->metaDescription)
        @push('meta')
            <meta name="description" content="{{ $this->metaDescription }}">
        @endpush
    @endisset
    
    <div class="d-md-flex justify-content-between align-items-center gap-4 mb-2">
        <div>
            <h3 class="fs-2" @if (Auth::user()?->admin) title="ID: {{ $organBuilder->id }}" @endif>
                {{ $organBuilder->name }}
                @if ($this->showActivePeriodInHeading)
                    <span class="text-body-tertiary">({{ $organBuilder->active_period }})</span>
                @endif
                @if (!$organBuilder->isPublic())
                    <i class="bi-lock text-warning" data-bs-toggle="tooltip" data-bs-title="{{ __('Soukromé') }}"></i>
                @endif
                    
                <br />
                <small style="font-size: 60%">
                    {{ $this->municipalityCountry[0] }}
                    @isset ($this->municipalityCountry[1])
                        <span class="text-secondary">({{ $this->municipalityCountry[1] }})</span>
                    @endisset
                    @if ($organBuilder->region && $organBuilder->region->id !== Region::Praha->value)
                        <span class="text-secondary" style="font-size: var(--bs-body-font-size);">({{ $organBuilder->region->name }})</span>
                    @endif
                </small>
            </h3>

            @if (isset($organBuilder->perex))
                <p class="lead">{{ $organBuilder->perex }}</p>
            @endif
        </div>
            
         @if ($organBuilder->image_url || $organBuilder->region)
            <div class="text-center">
                <div class="position-relative d-inline-block">
                    @if ($organBuilder->image_url)
                        <a href="{{ $organBuilder->image_url }}" target="_blank">
                            <img class="organ-img rounded border" src="{{ $organBuilder->image_url }}" @isset($organBuilder->image_credits) title="{{ __('Licence obrázku') }}: {{ $organBuilder->image_credits }}" @endisset height="200" />
                        </a>
                    @endif
                    @if ($organBuilder->region)
                        <img width="100" @class(['region', 'start-0', 'm-2', 'bottom-0', 'position-absolute' => isset($organBuilder->image_url)]) src="{{ Vite::asset("resources/images/regions/{$organBuilder->region_id}.png") }}" />
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    @if ($organBuilder->isPublic() && $organBuilder->isInland())
        <div class="text-center mt-3">
            <x-organomania.info-alert class="d-inline-block mb-1">
                {!! __('O stylovém vývoji našeho varhanářství více') !!}
                <a class="link-primary text-decoration-none" href="{{ route('about-organ') }}" wire:navigate>{{ __('zde') }}</a>.
            </x-organomania.info-alert>
        </div>
    @endif
    
    <table class="table mb-2">
        @if (isset($organBuilder->place_of_birth))
            <tr>
                <th>{{ __('Místo narození') }}</th>
                <td>{{ $organBuilder->place_of_birth }}</td>
            </tr>
        @endif
        @if (isset($organBuilder->place_of_death))
        <tr>
            <th>{{ __('Místo úmrtí') }}</th>
            <td>{{ $organBuilder->place_of_death }}</td>
        </tr>
        @endif
        @if (isset($organBuilder->active_period) && !$this->showActivePeriodInHeading)
        <tr>
            <th>{{ __('Období') }}</th>
            <td>{{ $organBuilder->active_period }}</td>
        </tr>
        @endif
        @php $nonCustomCategoryIds = $organBuilder->organBuilderCategories->pluck('id') @endphp
        @if ($nonCustomCategoryIds->isNotEmpty() || !empty($this->categoryGroups))
            <tr>
                <th>
                    {{ __('Kategorie') }}
                    @if ($nonCustomCategoryIds->isNotEmpty())
                        <span data-bs-toggle="tooltip" data-bs-title="{{ __('Zobrazit přehled kategorií') }}" onclick="setTimeout(removeTooltips);">
                            <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#categoriesModal" @click="highlightCategoriesInModal(@json($nonCustomCategoryIds))">
                                <i class="bi bi-question-circle"></i>
                            </a>
                        </span>
                    @endif
                </th>
                <td>
                    @foreach ($this->categoryGroups as $group)
                        @foreach ($group as $category)
                            <x-organomania.category-badge :category="$category" />
                        @endforeach
                        @if (!$loop->last)
                            <br />
                        @endif
                    @endforeach
                </td>
            </tr>
        @endif
        @if (!$organBuilder->shouldHideImportance())
        <tr>
            <th>
                {{ __('Význam') }}
                <a class="btn btn-sm p-1 py-0 text-primary" data-bs-toggle="modal" data-bs-target="#importanceHintModal">
                    <i class="bi bi-question-circle"></i>
                </a>
            </th>
            <td>
                <x-organomania.stars :count="round($organBuilder->importance / 2)" :showCount="true" />
            </td>
        </tr>
        @endif
        @isset($organBuilder->varhany_net_id)
            <tr>
                <th>
                    {{ __('Katalog') }}
                    <span class="d-none d-md-inline">{{ __('varhanářů') }}</span>
                </th>
                <td>
                    <a class="icon-link icon-link-hover" target="_blank" href="{{ url()->query('http://www.varhany.net/zivotopis.php', ['idv' => $organBuilder->varhany_net_id]) }}">
                        <i class="bi bi-link-45deg"></i>
                        varhany.net
                    </a>
                </td>
            </tr>
        @endisset
        @if (isset($this->workshop_members))
        <tr>
            <th>{{ __('Členové dílny') }}</th>
            <td>
                <span class="pre-line">{!! $this->workshop_members !!}</span>
                @if (isset($organBuilder->region_id) && $organBuilder->timelineItems->count() > 0)
                    <br />
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organ-builders.index', ['filterId' => $organBuilder->id, 'viewType' => 'timeline']) }}" wire:navigate>
                        <i class="bi bi-clock"></i>
                        {{ __('Časová osa') }}
                    </a>
                @endif
            </td>
        </tr>
        @endif
        @if (isset($organBuilder->web))
            <x-organomania.tr-responsive title="{{ __('Webové odkazy') }}">
                <div class="text-break items-list">
                    @foreach (explode("\n", $organBuilder->web) as $url)
                        <x-organomania.web-link :url="$url" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if ($this->organs->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Významné varhany') }}">
                <div class="text-break items-list" style="max-height: 350px; overflow-y: auto">
                    @foreach ($this->organs as ['isRebuild' => $isRebuild, 'organ' => $organ, 'year' => $year])
                            <x-organomania.organ-link :organ="$organ" :isRebuild="$isRebuild" :year="$year" :showSizeInfo="true" />
                            @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
                @if ($this->organs->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1 me-1" href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id]) }}">
                        <i class="bi bi-music-note-list"></i>
                        {{ __('Zobrazit vše') }}
                        <span class="badge text-bg-secondary rounded-pill">{{ $this->organs->count() }}</span>
                    </a>
                    <br class="d-sm-none" />
                @endif
                @if (isset($organBuilder->region_id) && $organBuilder->timelineItems->isNotEmpty())
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organ-builders.index', ['filterId' => $organBuilder->id, 'viewType' => 'timeline']) }}" wire:navigate>
                        <i class="bi bi-clock"></i> {{ __('Časová osa') }}
                    </a>
                @endif
                @if ($this->organs->count() > 1)
                    <a class="btn btn-sm btn-outline-secondary mt-1" href="{{ route('organs.index', ['filterOrganBuilderId' => $organBuilder->id, 'viewType' => 'chart', 'sortColumn' => 'stops_count']) }}" wire:navigate>
                        <i class="bi bi-bar-chart-line"></i>
                        {{ __('Srovnat velikost') }}
                    </a>
                @endif
            </x-organomania.tr-responsive>
        @endif
        @if ($organBuilder->renovatedOrgans->isNotEmpty())
            <x-organomania.tr-responsive title="{{ __('Opravy') }} / {{ __('restaurování') }}">
                <div class="text-break items-list">
                    @foreach ($organBuilder->renovatedOrgans as $organ)
                        <x-organomania.organ-link :organ="$organ" :year="$organ->year_renovated ?? false" :isRenovation="true" />
                        @if (!$loop->last) <br /> @endif
                    @endforeach
                </div>
            </x-organomania.tr-responsive>
        @endif
        @if ($this->relatedOrganBuilders->isNotEmpty())
            <tr>
                <th>{{ __('Související varhanáři') }}</th>
                <td>
                    <div class="items-list">
                        @foreach ($this->relatedOrganBuilders as $relatedOrganBuilder)
                            <x-organomania.organ-builder-link :organBuilder="$relatedOrganBuilder" :showActivePeriod="true" />
                            @if (!$loop->last) <br /> @endif
                        @endforeach
                    <div>
                </td>
            </tr>
        @endif
        @if (isset($organBuilder->description))
            <x-organomania.tr-responsive title="{{ __('Popis') }}">
                <div class="markdown">{!! $this->descriptionHtml !!}</div>
            </x-organomania.tr-responsive>
        @endif
    </table>

    <div class="mb-4">
        @if ($organBuilder->isPublic())
            <div class="small text-secondary text-end mb-4">
                {{ __('Zobrazeno') }}: {{ Helpers::formatNumber($organBuilder->views) }}&times;
            </div>
        @endif

        @if (count($this->images) > 0)
            <x-organomania.gallery-carousel :images="$this->images" class="my-4" />
        @endif
    </div>
        
    <div class="accordion">
        @if (isset($organBuilder->region_id) && $organBuilder->latitude > 0)
            <x-organomania.accordion-item
                id="accordion-map"
                class="d-print-none"
                title="{{ __('Mapa') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_MAP)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_MAP }}')"
            >
                <x-organomania.map-detail :latitude="$organBuilder->latitude" :longitude="$organBuilder->longitude" />
            </x-organomania.accordion-item>
        @endisset
        
        @isset($organBuilder->literature)
            <x-organomania.accordion-item
                id="accordion-literature"
                title="{{ __('Literatura') }}"
                :show="$this->shouldShowAccordion(static::SESSION_KEY_SHOW_LITERATURE)"
                onclick="$wire.accordionToggle('{{ static::SESSION_KEY_SHOW_LITERATURE }}')"
            >
                <ul class="list-group list-group-flush small">
                    @foreach (explode("\n", $organBuilder->literature) as $literature1)
                        <li @class(['list-group-item', 'px-0', 'pt-0' => $loop->first, 'pb-0' => $loop->last])>{!! Helpers::formatUrlsInLiterature($literature1) !!}</li>
                    @endforeach
                </ul>
            </x-organomania.accordion-item>
        @endisset
    </div>
    
    <div class="text-end mt-3">
        <a class="btn btn-sm btn-secondary" href="{{ $this->previousUrl }}" wire:navigate><i class="bi-arrow-return-left"></i> {{ __('Zpět') }}</a>&nbsp;
        @can('update', $organBuilder)
            <a class="btn btn-sm btn-outline-primary" href="{{ route('organ-builders.edit', ['organBuilder' => $organBuilder->id]) }}" wire:navigate>
                <i class="bi-pencil"></i> <span class="d-none d-sm-inline">{{ __('Upravit') }}</span>
            </a>
        @endcan
        <a class="btn btn-sm btn-outline-primary"  href="#" data-bs-toggle="modal" data-bs-target="#shareModal" data-share-url="{{ $organBuilder->getShareUrl() }}">
            <i class="bi-share"></i> <span class="d-none d-sm-inline">{{ __('Sdílet') }}</span>
        </a>
    </div>
        
    <x-organomania.modals.categories-modal :categoriesGroups="$this->organBuilderCategoriesGroups" :categoryClass="OrganBuilderCategory::class" />
        
    <x-organomania.modals.share-modal />
        
    <x-organomania.modals.importance-hint-modal :title="__('Význam varhanáře')">
        {{ __('Význam varhanáře se eviduje, aby bylo možné množství varhanářů přibližně seřadit podle důležitosti.') }}
        {{ __('Význam je určen hrubým odhadem na základě řady kritérií a nejde o hodnocení kvality varhanáře.') }}
    </x-organomania.modals.importance-hint-modal>
    
</div>
