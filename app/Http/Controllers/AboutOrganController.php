<?php

namespace App\Http\Controllers;

use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\RegisterName;
use App\Helpers;

class AboutOrganController extends Controller
{
    
    public function __invoke()
    {
        $organs = $this->getOrgans();
        
        Helpers::logPageViewIntoCache('about-organ');
        
        return view('about-organ', [
            'organs' => $organs,
            'organBuilders' => $this->getOrganBuilders(),
            'registerNames' => $this->getRegisterNames(),
            
            'renovatedOrgans' => [
                $organs[Organ::ORGAN_ID_PRAHA_KOSTEL_MATKY_BOZI_PRED_TYNEM],
                $organs[Organ::ORGAN_ID_DOUBRAVNIK],
                $organs[Organ::ORGAN_ID_POLNA_KOSTEL_NANEBEVZETI_PANNY_MARIE_VELKE_VARHANY],
                $organs[Organ::ORGAN_ID_PRAHA_OBECNI_DUM],
            ],
        ]);
    }
    
    private function getOrgans()
    {
        return Organ::find([
            Organ::ORGAN_ID_OLOMOUC_KATEDRALA_SV_VACLAVA, Organ::ORGAN_ID_LUDGEROVICE, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_LUDMILY,
            Organ::ORGAN_ID_PRAHA_KOSTEL_SV_PETRA_A_PAVLA_VYSEHRAD, Organ::ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_WOHLMUTOVA_KRUCHTA, Organ::ORGAN_ID_CESKY_KRUMLOV_KOSTEL_SV_VITA,
            Organ::ORGAN_ID_NYMBURK_KOSTEL_SV_JILJI, Organ::ORGAN_ID_PLZEN_VELKA_SYNAGOGA, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_JAKUBA_VETSIHO,
            Organ::ORGAN_ID_PRAHA_RUDOLFINUM, Organ::ORGAN_ID_OLOMOUC_KOSTEL_SV_MORICE, Organ::ORGAN_ID_BRNO_KOSTEL_SV_AUGUSTINA,
            Organ::ORGAN_ID_PRAHA_KOSTEL_SV_CYRILA_A_METODEJE_KARLIN, Organ::ORGAN_ID_CHEB_KOSTEL_SV_MIKULASE, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MARKETY_BREVNOV,
            Organ::ORGAN_ID_PRAHA_KOSTEL_SV_SALVATORA, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_SALVATORA, Organ::ORGAN_ID_PRIBRAM_SVATA_HORA,
            Organ::ORGAN_ID_BRNO_JEZUITSKY_KOSTEL_NANEBEVZETI_PANNY_MARIE, Organ::ORGAN_ID_PRAHA_KAROLINUM, Organ::ORGAN_ID_PRAHA_KATEDRALA_SV_VITA_ZAPADNI_KRUCHTA,
            Organ::ORGAN_ID_PRAHA_KOSTEL_MATKY_BOZI_PRED_TYNEM, Organ::ORGAN_ID_DOUBRAVNIK, Organ::ORGAN_ID_POLNA_KOSTEL_NANEBEVZETI_PANNY_MARIE_VELKE_VARHANY,
            Organ::ORGAN_ID_PRAHA_OBECNI_DUM, Organ::ORGAN_ID_KOLIN_KOSTEL_SV_BARTOLOMEJE, Organ::ORGAN_ID_SMECNO,
            Organ::ORGAN_ID_PLASY, Organ::ORGAN_ID_ZLATA_KORUNA, Organ::ORGAN_ID_DUB_NAD_MORAVOU,
            Organ::ORGAN_ID_RYCHNOV_NAD_KNEZNOU_ZAMECKY_KOSTEL, Organ::ORGAN_ID_BRNO_STARE_BRNO, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_VOJTECHA,
            Organ::ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_BOCNI_EMPORA, Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_VELKE_VARHANY, Organ::ORGAN_ID_SLUKNOV,
            Organ::ORGAN_ID_LITOMERICE_KATEDRALA_SV_STEPANA_VELKE_VARHANY, Organ::ORGAN_ID_TEPLA, Organ::ORGAN_ID_KLADRUBY,
            Organ::ORGAN_ID_ZDAR, Organ::ORGAN_ID_REPIN, Organ::ORGAN_ID_OLOMOUC_SVATY_KOPECEK_HLAVNI_KUR,
            Organ::ORGAN_ID_FILIPOV, Organ::ORGAN_ID_KUTNA_HORA_SV_JAKUB_VELKE_VARHANY, Organ::ORGAN_ID_MOST,
            Organ::ORGAN_ID_BOZKOV, Organ::ORGAN_ID_KRNOV_KOSTEL_SV_DUCHA, Organ::ORGAN_ID_PRAHA_KRIZOVNICI,
            Organ::ORGAN_ID_PRAHA_KOSTEL_SV_MIKULASE_LEVA_EMPORA,
        ])->keyBy('id');
    }
    
    private function getOrganBuilders()
    {
        return OrganBuilder::find([
            OrganBuilder::ORGAN_BUILDER_ID_RIEGER, OrganBuilder::ORGAN_BUILDER_ID_RIEGER_KLOSS, OrganBuilder::ORGAN_BUILDER_ID_ORGANA,
            OrganBuilder::ORGAN_BUILDER_ID_EMANUEL_STEPAN_PETR, OrganBuilder::ORGAN_BUILDER_ID_BRATRI_PASTIKOVE, OrganBuilder::ORGAN_BUILDER_ID_JINDRICH_SCHIFFNER,
            OrganBuilder::ORGAN_BUILDER_ID_JAN_TUCEK, OrganBuilder::ORGAN_BUILDER_ID_JOSEF_MELZER, OrganBuilder::ORGAN_BUILDER_ID_BRATRI_BRAUNEROVE,
            OrganBuilder::ORGAN_BUILDER_ID_NEUSSEROVE, OrganBuilder::ORGAN_BUILDER_ID_STEINMEYER, OrganBuilder::ORGAN_BUILDER_ID_A_SCHUSTER_UND_SOHN,
            OrganBuilder::ORGAN_BUILDER_ID_JEHMLICH, OrganBuilder::ORGAN_BUILDER_ID_MARTIN_ZAUS, OrganBuilder::ORGAN_BUILDER_ID_DALIBOR_MICHEK,
            OrganBuilder::ORGAN_BUILDER_ID_DLABAL_METTLER, OrganBuilder::ORGAN_BUILDER_ID_MAREK_VORLICEK, OrganBuilder::ORGAN_BUILDER_ID_STARKOVE,
            OrganBuilder::ORGAN_BUILDER_ID_HEINRICH_MUNDT, OrganBuilder::ORGAN_BUILDER_ID_TOMAS_SCHWARZ, OrganBuilder::ORGAN_BUILDER_ID_TAUCHMANNOVE,
            OrganBuilder::ORGAN_BUILDER_ID_GARTNEROVE, OrganBuilder::ORGAN_BUILDER_ID_GUTHOVE, OrganBuilder::ORGAN_BUILDER_ID_JAN_DAVID_SIEBER,
            OrganBuilder::ORGAN_BUILDER_ID_JAN_VYMOLA, OrganBuilder::ORGAN_BUILDER_ID_MICHAEL_ENGLER, OrganBuilder::ORGAN_BUILDER_ID_STAUDINGEROVE,
            OrganBuilder::ORGAN_BUILDER_ID_FRANTISEK_SVITIL, OrganBuilder::ORGAN_BUILDER_ID_FRANZ_HARBICH, OrganBuilder::ORGAN_BUILDER_ID_JOSEF_PREDIGER,
            OrganBuilder::ORGAN_BUILDER_ID_KANSKY_BRACHTL, OrganBuilder::ORGAN_BUILDER_ID_VLADIMIR_SLAJCH, OrganBuilder::ORGAN_BUILDER_ID_KRALICKA_DILNA,
            OrganBuilder::ORGAN_BUILDER_ID_LEOPOLD_SPIEGEL, OrganBuilder::ORGAN_BUILDER_ID_LEOPOLD_BURKHARDT, OrganBuilder::ORGAN_BUILDER_ID_BEDRICH_SEMRAD,
            OrganBuilder::ORGAN_BUILDER_ID_PAVEL_FRANTISEK_HORAK, OrganBuilder::ORGAN_BUILDER_ID_HORCICKOVE, OrganBuilder::ORGAN_BUILDER_ID_CASPARIDOVE,
            OrganBuilder::ORGAN_BUILDER_ID_JOSEF_SILBERBAUER, OrganBuilder::ORGAN_BUILDER_ID_JIRI_SPANEL,
        ])->keyBy('id');
    }
    
    private function getRegisterNames()
    {
        return RegisterName::find([
            RegisterName::REGISTER_NAME_ID_FLETNA_HARMONICKA, RegisterName::REGISTER_NAME_ID_KLARINET, RegisterName::REGISTER_NAME_ID_HARMONIA_AETHEREA,
            RegisterName::REGISTER_NAME_ID_VOX_COELESTIS
        ])->keyBy('id');
    }
    
}
