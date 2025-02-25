<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Organ;
use App\Models\Scopes\OwnedEntityScope;
use App\Services\DispositionTextualFormatter;

class OrganController extends Controller
{
    
    public function exportDispositionAsPdf(Organ $organ, DispositionTextualFormatter $dispositionFormatter)
    {
        $disposition = $organ->disposition;
        // "podrobnosti viz zdroj" v dispozici odřízneme
        $pos = mb_strrpos($disposition, '---');
        if ($pos !== false) $disposition = mb_substr($disposition, 0, $pos);
        $disposition = $dispositionFormatter->format($disposition);

        $filename = __('Disposition_1') . " - {$organ->municipality}, {$organ->place}.pdf";

        return response()
            ->streamDownload(
                function () use ($organ, $disposition) {
                    $pdf = Pdf::loadView('components.organomania.pdf.disposition-textual', [
                        'organ' => $organ,
                        'disposition' => $disposition,
                    ]);
                    echo $pdf->stream();
                },
                name: $filename,
                disposition: 'inline',
                headers: ['Content-Type' => 'application/pdf'],
            );
    }
    
    public function redirectToDolniLhotaSongs()
    {
        $organ = Organ::withoutGlobalScope(OwnedEntityScope::class)->findOrFail(4506);
        $url = url(URL::signedRoute('organs.worship-songs', $organ->slug, absolute: false));
        return redirect($url);
    }
    
}
