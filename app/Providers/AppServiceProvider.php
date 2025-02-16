<?php

namespace App\Providers;

// use App\Services\GeoLocationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $location = GeoLocationService::getLocation(GeoLocationService::getPublicIpAddress());

        // if (Arr::isAssoc($location)) {
        //     // dd($location['country']);
        //     if ($location['country'] === 'Canada') {
        //         // throw new \Exception('Leave', 404);
        //     }
        // }
        // // dd();
    }
}
