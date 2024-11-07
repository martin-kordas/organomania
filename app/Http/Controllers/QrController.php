<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrController extends Controller
{
    
    public function __invoke()
    {
        if ($string = request()['string']) {
            $renderer = new ImageRenderer(
                new RendererStyle(size: 400, margin: 0),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qr = $writer->writeString($string);
            
            return response()->streamDownload(function () use ($qr) {
                echo $qr;
            }, name: 'qr.svg', headers: ['Content-Type' => 'image/svg+xml'], disposition: 'inline');
        }
    }
    
}
