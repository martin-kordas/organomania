<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Festival;
use App\Models\Competition;
use App\Models\Disposition;
use App\Models\RegisterName;
use App\Models\User;
use App\Models\Scopes\OwnedEntityScope;

class SitemapController extends Controller
{
    
    public function __invoke()
    {
        $organs = Organ::all();
        // soukromé záznamy admina se nezobrazují ve vyhledávání, ale jsou dohledatelné přes Google (jde o méně známé varhany, které jsem přesto vložil do Organomanie)
        $privateOrgans = Organ::withoutGlobalScope(OwnedEntityScope::class)->where('user_id', User::USER_ID_ADMIN)->get();
        $organBuilders = OrganBuilder::all();
        $festivals = Festival::all();
        $competitions = Competition::all();
        $dispositions = Disposition::all();
        $registerNames = RegisterName::all();

        $sitemap = view(
            'sitemap',
            compact('organs', 'privateOrgans', 'organBuilders', 'festivals', 'competitions', 'dispositions', 'registerNames')
        )->render();

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
    
}
