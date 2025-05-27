<?php

namespace App\Providers;

use App\PiniaLoaders\ProjectsLoader;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\Contact;
use App\Observers\ContactObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectsLoader::class, function ($app) {
            return (new ProjectsLoader())->all();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Contact::observe(ContactObserver::class);
    }
}
