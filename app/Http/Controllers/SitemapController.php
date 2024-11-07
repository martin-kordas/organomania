<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\Festival;
use App\Models\Competition;
use App\Models\Disposition;
use App\Models\RegisterName;

class SitemapController extends Controller
{
    
    public function __invoke()
    {
        $organs = Organ::all();
        $organBuilders = OrganBuilder::all();
        $festivals = Festival::all();
        $competitions = Competition::all();
        $dispositions = Disposition::all();
        $registerNames = RegisterName::all();

        $sitemap = view(
            'sitemap',
            compact('organs', 'organBuilders', 'festivals', 'competitions', 'dispositions', 'registerNames')
        )->render();

        return Response::make($sitemap, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
    
}
