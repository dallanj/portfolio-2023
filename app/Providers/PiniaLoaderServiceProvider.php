<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\PiniaStation\Factories\PiniaLoaderFactory;
use App\PiniaStation\Facades\PiniaLoader;
use FilesystemIterator;
use ReflectionClass;

class PiniaLoaderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        App::singleton(PiniaLoaderFactory::class, fn ($app) => new PiniaLoaderFactory($app));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initalizeLoaders();
    }

    protected function initalizeLoaders()
    {
        $loaders = collect(new FilesystemIterator(app_path('PiniaLoaders')))
            ->flatMap(function ($file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $loader = 'App\\PiniaLoaders' . '\\' . $file->getBasename('.php');
                    if (class_exists($loader)) {
                        $reflection = new ReflectionClass($loader);
                        if ($reflection->isInstantiable()) {
                            return [$reflection];
                        }
                    }
                }
            });

            PiniaLoader::initilize($loaders);
    }
}
