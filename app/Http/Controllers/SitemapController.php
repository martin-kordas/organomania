<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Festival;
use App\Models\Competition;
use App\Models\Disposition;
use App\Models\OrganBuilderMunicipalityInfo;
use App\Models\OrganMunicipalityInfo;
use App\Models\RegisterName;
use App\Models\User;
use App\Models\Scopes\OwnedEntityScope;
use App\Repositories\OrganRepository;

class SitemapController extends Controller
{

    public function __construct(
        private OrganRepository $organRepository,
    ) { }
    
    public function __invoke()
    {
        $organs = Organ::all();
        // soukromé varhany admina jsou veřejně dohledatelné (jde o méně známé varhany, které jsem přesto vložil do Organomanie)
        $privateOrgans = Organ::withoutGlobalScope(OwnedEntityScope::class)->where('user_id', User::USER_ID_ADMIN)->get();
        $organBuilders = OrganBuilder::all();
        $festivals = Festival::all();
        $competitions = Competition::all();
        $dispositions = Disposition::all();
        $registerNames = RegisterName::all();
        $caseImagesOrganBuilders = $this->getCaseImagesOrganBuilders();
        $organMunicipalityInfos = OrganMunicipalityInfo::select('municipality')->get();
        $organBuilderMunicipalityInfos = OrganBuilderMunicipalityInfo::select('municipality')->get();

        $sitemap = view(
            'sitemap',
            data: compact(
                'organs', 'privateOrgans', 'organBuilders', 'festivals', 'competitions', 'dispositions', 'registerNames',
                'caseImagesOrganBuilders', 'organMunicipalityInfos', 'organBuilderMunicipalityInfos',
            )
        )->render();

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function getCaseImagesOrganBuilders()
    {
        return OrganBuilder::query()
            ->select(['id', 'slug'])
            ->public()
            ->where('id', '!=', OrganBuilder::ORGAN_BUILDER_ID_NOT_INSERTED)
            ->orderBy('id')
            ->get()
            ->filter(function (OrganBuilder $organBuilder) {
                $caseImagesCount = $this->organRepository->getOrganBuilderCaseImagesCount($organBuilder);
                return $caseImagesCount > 0;
            });
    }
    
}
