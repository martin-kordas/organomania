<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;

class ThumbnailController extends Controller
{
    
    const WIDTH = 450;

    // TODO: neřeší se vymazání cache při změně obrázku - nový obrázek nutno nahrát pod novým názvem nebo ručně vymazat cache
    public function resize(string $file)
    {
        // externí obrázky nejprve stáhneme a uložíme na disk
        if (str($file)->startsWith(['https:/'])) {
            // TODO: www.varhany.net nefunguje
            $domains = ['upload.wikimedia.org', 'www.varhany.net'];
            $domainMatch = false;
            foreach ($domains as $domain) {
                // bug v Laravelu působí, že PHP vidí jen 1 slash: https://github.com/laravel/framework/issues/22125
                if (str($file)->startsWith(["https://$domain/", "https:/$domain/"])) {
                    $domainMatch = true;
                    $file = str_replace("https:/$domain/", "https://$domain/", $file);
                    break;
                }
            }
            if (!$domainMatch) throw new Exception;

            $path = storage_path('app/thumbnails-web');

            $basename = bin2hex($file);
            $basename = mb_substr($basename, 0, 250);       // HACK
            $fileReal = $basename;
            $filename = "$path/$basename";
            if (!is_file($filename)) {
                if (!is_dir($path)) mkdir($path);
                $imageContents = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; MSIE 6.0)'
                ])
                ->get($file)->body();

                // patrně přetížení Wikimedie
                if (str_starts_with($imageContents, '<!DOCTYPE html>')) throw new Exception;
                file_put_contents($filename, $imageContents);
            }
        }
        else {
            $path = public_path('images');
            $fileReal = $file;
        }

        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $path,
            'cache' => storage_path('app/thumbnails'),
        ]);

        $params = ['fm' => 'webp'];     // nejúspornější formát WebP
        $imageSize = getimagesize("$path/$fileReal");
        if ($imageSize && $imageSize[0] > static::WIDTH) $params['w'] = static::WIDTH;
        
        $response = $server->getImageResponse($fileReal, $params);
        $response->headers->set('Cache-Control', 'public, max-age=2419200');
        return $response;
    }

    static function getThumbnailUrl(string $url)
    {
        // není-li obrázek externí, umístění ve složce images se předpokládá automaticky
        $url = str($url)->chopStart('/images/');
        return route('thumbnail', [$url]);
    }
    
}
