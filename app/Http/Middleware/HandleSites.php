<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class HandleSites
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = request()->url();
        $path = parse_url($url, PHP_URL_PATH);
        
        // potlačení defaultního routingu kdy /marketa-prokopovicova/organs je totožné s /organs (existuje-li složka /public/marketa-prokopovicova)
        //  - důvod chování neznámý
        //  - dělá to při Laravel Sail (jinak netestováno)
        if (str($path)->startsWith(['/martin-kordas', '/marketa-prokopovicova'])) abort(404);
        
        return $next($request);
    }
}