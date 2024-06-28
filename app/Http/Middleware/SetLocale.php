<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
            $request->session()->put(self::SESSION_KEY, $request->getPreferredLanguage(self::LOCALES));
        }

        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, self::LOCALES)) {
                $request->session()->put(self::SESSION_KEY, $lang);
            }
        }

        app()->setLocale($request->session()->get(self::SESSION_KEY));

        return $next($request);
    }
}
