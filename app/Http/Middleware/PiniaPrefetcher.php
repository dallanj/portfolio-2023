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
        if (Auth::check()) {
            PiniaLoader::load('user', 'profile', lazy: true);
            // PiniaLoader::load('options', [
            //     'roles',
            //     'navigation'
            // ], lazy: true);
            // dd(PiniaLoader::$results, PiniaLoader::$loaders);
            // PiniaLoader::load('options', 'navigation');
            // PiniaLoader::load('options', 'roles');
        }

        return $next($request);
    }
}
