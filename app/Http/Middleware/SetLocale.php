<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

// https://stackoverflow.com/a/49158689/14967413
class SetLocale
{
    
    const SESSION_KEY = 'locale';
    const LOCALES = ['en', 'cs'];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has(self::SESSION_KEY)) {
            $preferredLocale = $request->getPreferredLanguage(self::LOCALES);
            $this->saveLocale($preferredLocale);
        }

        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, self::LOCALES)) {
                $this->saveLocale($lang);
            }
        }

        $locale = $request->session()->get(self::SESSION_KEY);
        app()->setLocale($locale);

        return $next($request);
    }
    
    private function saveLocale($locale)
    {
        request()->session()->put(self::SESSION_KEY, $locale);
        if ($locale !== 'cs') {
            Log::info('Used non-default locale ({locale}).', ['locale' => $locale]);
        }
    }
}
