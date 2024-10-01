<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
 
class AddContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::add('user_id', Auth::id() ?? null);
 
        return $next($request);
    }
}