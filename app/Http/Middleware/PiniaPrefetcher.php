<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\PiniaStation\Facades\PiniaLoader;

class PiniaPrefetcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->getPathInfo() === '/') {
            PiniaLoader::load('dashboard', 'all', lazy: true);
        } elseif (Auth::check() && $request->getPathInfo() !== '/') {
            PiniaLoader::load('user', 'profile', lazy: true);
            // PiniaLoader::load('options', [
            //     'roles',
            // ], lazy: true);
        }

        return $next($request);
    }
}
