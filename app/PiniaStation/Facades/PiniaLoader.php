<?php

namespace App\PiniaStation\Facades;

use Illuminate\Support\Facades\Facade;
use App\PiniaStation\Factories\PiniaLoaderFactory;

class PiniaLoader extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PiniaLoaderFactory::class;
    }
}
